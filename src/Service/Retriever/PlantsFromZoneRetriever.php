<?php


namespace App\Service\Retriever;


use App\Entity\DistributionZone;
use App\Entity\DistributionZonePlant;
use App\Entity\Plant;
use App\Entity\PlantSync;
use App\Repository\DistributionZonePlantRepository;
use App\Repository\DistributionZoneRepository;
use App\Repository\PlantRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PlantsFromZoneRetriever
{
    const PLANTS_PER_PAGE = 20; //trefle`s pagination
    const FILE_NAME = "batch.txt";

    private ?int $zoneId;
    private string $uid;

    private ArrayCollection $plants;
    private array $currentBatch;

    private ObjectRepository|DistributionZoneRepository $repository;
    private PlantRepository|ObjectRepository $plantRepository;
    private ObjectRepository|DistributionZonePlantRepository $zonePlantRepository;

    public function __construct(
        private PlantsInfoRetriever $infoRetriever,
        private EntityManagerInterface $em,
        private HttpClientInterface $trefleApi,
        private HttpClientInterface $geonamesApi
    ) {
        $this->plants = new ArrayCollection();
        $this->currentBatch = [];

        $this->repository = $em->getRepository(DistributionZone::class);
        $this->plantRepository = $em->getRepository(Plant::class);
        $this->zonePlantRepository = $em->getRepository(DistributionZonePlant::class);
        $date = new DateTime();

        $this->uid = uniqid($date->getTimestamp(), true);
    }

    /**
     * The method creates an initial call to the plants api and
     * loops through the rest of the pages, if there are any.
     *
     * @param bool|DistributionZone $zone
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getPlants(bool|DistributionZone $zone): array
    {
        if ($zone === false) {
            return [];
        }

        /*
         * If the zone is already fetched
         * return the plants which are already saved.
         */
        if ($zone->getFullyFetched()) {
            $plants = $zone->getAllPlants();

            uksort($plants, [self::class, 'comparePlantNames']);

            return $plants;
        }

        $this->zoneId = $zone->getId();

        $request = $this->getPlantsFromPage("distributions/{$this->zoneId}/plants");

        $total = $request['total'];

        $pages = ceil($total / self::PLANTS_PER_PAGE);
//        $insertCount = $request['insertCount'];

        if ($pages > 1) {
            $existingPlants = $zone->getAllPlants();
            $existingCount = count($existingPlants);

            foreach ($existingPlants as $existingPlant) {
                $this->plants->set($existingPlant->getScientificName(), $existingPlant);
            }

            $lastFetchedPage = ceil((float) ($existingCount / self::PLANTS_PER_PAGE));

            for ($i = $lastFetchedPage; $i <= $pages; $i++) {
                $request = $this->getPlantsFromPage("distributions/{$this->zoneId}/plants?page={$i}");

//                $insertCount += $request['insertCount'];

                /*
                 * If the number of inserts is bigger than the set batch - 500,
                 * insert the persisted entities.
                 *
                 * Note: can't use Modulo ($count % $batchSize) here so we use >
                 */
                if (count($this->currentBatch) > 500) {
//                    $this->bulkInsertAll();
                    $this->em->flush();
                    $this->em->clear();

                    $this->currentBatch = [];
//                    $insertCount = 0;
                }
            }
        }

//        $this->bulkInsertAll();

        $this->em->flush();
        $this->em->clear();

        $plants = $this->plants->toArray();

        uksort($plants, [self::class, 'comparePlantNames']);

        return $plants;
    }

    public function syncTables($zoneId)
    {
        $i = 0;
        /**
         * @var $newPlants PlantSync[]
         */
        $newPlants = $this->em->getRepository(PlantSync::class)->findAll();
        $zone = $this->em->getRepository(DistributionZone::class)->find($zoneId);

        foreach ($newPlants as $newPlant) {
            $plant = new Plant();

            $plant->setScientificName($newPlant->getScientificName())
                ->setCommonName($newPlant->getCommonName())
                ->setImageUrl($newPlant->getImageUrl());

            $this->em->persist($plant);

            $distributionPlant = new DistributionZonePlant();
            $distributionPlant->setPlant($plant)
                ->setDistributionZone($zone);

            $this->em->persist($distributionPlant);

            $i += 2;

            if ($i % 500 === 0) {
                $this->em->flush();
                $this->em->clear();
            }
        }
    }


    private function bulkInsertAll(): void
    {
        if (isset($this->currentBatch['plants']) && is_array($this->currentBatch['plants']) && count(
                $this->currentBatch['plants']
            )) {
            $this->plantRepository->bulkInsert($this->currentBatch['plants']);
        }

        if (isset($this->currentBatch['distributionPlants']) && is_array(
                $this->currentBatch['distributionPlants']
            ) && count($this->currentBatch['distributionPlants'])) {
            $this->zonePlantRepository->bulkInsert($this->currentBatch['distributionPlants']);
        }
    }

    /*
    public function clean(array $batchGlobal)
    {
        foreach ($this->batched->getKeys() as $key) {
            unset($batchGlobal[$key]);
        }

        file_put_contents(self::FILE_NAME, json_encode($batchGlobal));

        $this->batched->clear();
    }

    public function detachDuplicates(): array
    {
        $contents = file_get_contents(self::FILE_NAME);
        $batchGlobal = json_decode($contents, true);

        //todo replace detach when new replacement is made
        foreach ($this->batched as $name => $batch) {
            //if it is in the file and the unique id is not the same detach the entities
            if (isset($batchGlobal[$name]) && $batchGlobal[$name] !== $this->uid) {
                $this->em->detach($batch['plant']);

                if ($batch['distributionPlant'] !== null) {
                    $this->em->detach($batch['distributionPlant']);
                }

                $this->batched->remove($name);
            }
        }

        return $batchGlobal;
    }

    public function addToGlobalBatch(array $batches)
    {
        $newArr = [];

        foreach ($batches as $batch) {
            $newArr[$batch['plant']->getScientificName()] = $batch['uid'];
        }

        $contents = file_get_contents(self::FILE_NAME);
        $batchGlobal = json_decode($contents, true);

        $newContent = json_encode($batchGlobal + $newArr);

        file_put_contents(self::FILE_NAME, $newContent);
    }


    private function addZoneToQ()
    {
        $contents = file_get_contents(self::FILE_NAME);
        $zones = json_decode($contents, true);

        $zones[$this->zoneId] = $this->uid;

        $newContent = json_encode($zones);
        file_put_contents(self::FILE_NAME, $newContent);
    }

    private function checkZoneQ(): bool
    {
        $contents = file_get_contents(self::FILE_NAME);
        $zones = json_decode($contents, true);

        return isset($zones[$this->zoneId]);
    }

    private function removeZoneQ()
    {
        $contents = file_get_contents(self::FILE_NAME);
        $zones = json_decode($contents, true);

        unset($zones[$this->zoneId]);

        $newContent = json_encode($zones);
        file_put_contents(self::FILE_NAME, $newContent);
    }
    */

    public function comparePlantNames(string $a, string $b)
    {
        $a = preg_replace('@^(a|an|the) @', '', $a);
        $b = preg_replace('@^(a|an|the) @', '', $b);

        return strcasecmp($a, $b);
    }


    /**
     * The method send a request to the geonames api and determines the distribution zone.
     *
     * Check if it is existing and if it is marked as distribution zone and if so
     * return the entity.
     *
     * @param string $lat
     * @param string $lng
     * @return false|DistributionZone Return false if it is non-existing or the entity
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getDistributionZone(string $lat, string $lng): bool|DistributionZone
    {
        $response = $this->geonamesApi->request('GET', "countrySubdivisionJSON?lat={$lat}&lng={$lng}");
        $data = $response->toArray();

        $country = $data['countryName'];
        $countryZone = $this->repository->findOneBy(['name' => $country]);

        //adminName1 is the name that the geonames api uses for lower level areas such as countries or large states.
        if (!isset($data['adminName1'])) {
            $subDiv = null;
            $subDivZone = null;
        } else {
            $subDiv = $data['adminName1'];
            $subDivZone = $this->repository->findOneBy(['name' => $subDiv]);
        }

        if ($subDivZone) {
            $zone = $subDivZone;
        } elseif ($countryZone) {
            $zone = $countryZone;
        } else {
            $zone = false;
        }

        return $zone;
    }

    /**
     * The method makes request to the trefle(plants) api and
     * calls the persisting function.
     *
     * @param string $url
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function getPlantsFromPage(string $url): array
    {
        $insertCount = 0;

        $response = $this->trefleApi->request('GET', $url);
        $data = $response->toArray();

        /*
         * Every response has data containing the plants and
         * total meta containing the number of all plants in the zone.
         */
        $body = $data['data'];
        $total = $data['meta']['total'];

        /**
         * Clear all duplicates by scientific name.
         */
        foreach ($body as $k => $v) {
            foreach ($body as $key => $value) {
                if ($k != $key && $v['scientific_name'] == $value['scientific_name']) {
                    unset($body[$k]);
                }
            }
        }

        //Get the scientific names in separate array and use them to determine if they are already existing.
//        $scientificNames = array_column($body, 'scientific_name');
//        $existingPlants = $this->plantRepository->findByScientificName($scientificNames);

        foreach ($body as $item) {
//            $insert = $this->persistPlantInZone($existingPlants, $item);
//            $insertCount += $insert;
            $this->persistInSync($item);
        }


        return [
//            'insertCount' => $insertCount,
            'total' => $total,
        ];
    }


    /**
     * @param array $item
     * @return void
     */
    private function persistInSync(array $item): void
    {
        $insertCount = 0;

        $plant = new PlantSync();

        $plant->setScientificName($item['scientific_name'])
            ->setCommonName($item['common_name'])
            ->setImageUrl($item['image_url'])
            ->setDistributionZone($this->zoneId);

        $this->plants->set($item['scientific_name'], $plant);
        $this->currentBatch[$item['scientific_name']] =  $plant;

        $this->em->persist($plant);
    }

    /**
     * The method responsible for saving the required information for the plants in the DB
     * such as: scientificName, commonName and imageUrl.
     *
     * @param array $existingPlants
     * @param array $item The plant array from the json response.
     * @return int
     */
    private function persistPlantInZone(array $existingPlants, array $item): int
    {
        $insertCount = 0;

        foreach ($existingPlants as $name => $existingPlant) {
            $this->plants->set($name, $existingPlant);
        }

        /**
         * @var $zone DistributionZone
         * @var $existing bool|Plant
         */
        $zone = $this->em->getRepository(DistributionZone::class)->find($this->zoneId);

        $existing = $this->plants->get($item['scientific_name']);

        /*
         * If the plant does not exist create the new entities and persist them,
         * otherwise make a relation between the plant and distribution zones.
         */
        if ($existing === null) {
            $plant = new Plant();

            $plant->setScientificName($item['scientific_name'])
                ->setCommonName($item['common_name'])
                ->setImageUrl($item['image_url']);

            $this->currentBatch['plants'][] = $plant;
            // $this->em->persist($plant);

            $distributionPlant = new DistributionZonePlant();
            $distributionPlant->setPlant($plant)
                ->setDistributionZone($zone);

            // $this->em->persist($distributionPlant);

            $this->currentBatch['distributionPlants'][] = $distributionPlant;
            $insertCount = 2;
        } else {
            /**
             * @return bool
             * @var $item DistributionZonePlant|null
             */
            $filter = function (?DistributionZonePlant $item) {
                return $item->getDistributionZone()->getId() === $this->zoneId;
            };

            $existingZonePlant = $existing->getDistributionZonesPlants()->filter($filter);

            /*
             * If the existing id is null it means it is not inserted in db
             * and there are already persisted entities
             */
            if ($existing->getId() !== null && $existingZonePlant->isEmpty()) {
                $distributionPlant = new DistributionZonePlant();
                $distributionPlant->setPlant($existing)
                    ->setDistributionZone($zone);

                $existing->addDistributionZonesPlant($distributionPlant);

//                $this->em->persist($distributionPlant);

                $this->currentBatch['distributionPlants'][] = $distributionPlant;
                $insertCount = 1;
            }

            $plant = $existing;
        }

//        $batch = [
//            'uid' => $this->uid,
//            'distributionPlant' => $distributionPlant,
//            'plant' => $plant
//        ];
//        $this->batched->set($item['scientific_name'], $batch);

        $this->plants->set($item['scientific_name'], $plant);

        return $insertCount;
    }
}