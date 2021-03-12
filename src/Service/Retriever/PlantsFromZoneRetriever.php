<?php


namespace App\Service\Retriever;


use App\Entity\DistributionZone;
use App\Entity\DistributionZonePlant;
use App\Entity\Plant;
use App\Repository\DistributionZonePlantRepository;
use App\Repository\DistributionZoneRepository;
use App\Repository\PlantRepository;
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

    private ?int $zoneId;
    private ArrayCollection $plants;

    private ObjectRepository|DistributionZoneRepository $repository;
    private PlantRepository|ObjectRepository $plantRepository;
    private ObjectRepository|DistributionZonePlantRepository $zonePlantRepository;

    public function __construct(
        private PlantsInfoRetriever $infoRetriever,
        private EntityManagerInterface $em,
        private HttpClientInterface $trefleApi,
        private HttpClientInterface $geonamesApi
    )
    {
        $this->plants = new ArrayCollection();
        $this->repository = $em->getRepository(DistributionZone::class);
        $this->plantRepository = $em->getRepository(Plant::class);
        $this->zonePlantRepository = $em->getRepository(DistributionZonePlant::class);
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
        if ($zone->getFetched()) {
            $plants = $zone->getAllPlants();

            uksort($plants, [self::class, 'comparePlantNames']);

            return $plants;
        }

        $this->zoneId = $zone->getId();

        $request = $this->getPlantsFromPage("distributions/{$this->zoneId}/plants");

        $total = $request['total'];

        $pages = ceil($total / self::PLANTS_PER_PAGE);
        $insertCount = $request['insertCount'];

        if ($pages > 1) {
            for ($i = 2; $i<=$pages; $i++) {
                $request = $this->getPlantsFromPage("distributions/{$this->zoneId}/plants?page={$i}");

                $insertCount +=  $request['insertCount'];;

                /*
                 * If the number of inserts is bigger than the set batch - 500,
                 * insert the persisted entities.
                 *
                 * Note: can't use Modulo ($count % $batchSize) here so we use >
                 */
                if ($insertCount > 500) {
                    $insertCount = 0;

                    $this->em->flush();
                    $this->em->clear();
                }
            }

            $this->em->flush();
            $this->em->clear();
        }

        $plants = $this->plants->toArray();

        uksort($plants, [self::class, 'comparePlantNames']);

        return $plants;
    }

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
    {;
        $response = $this->geonamesApi->request('GET', "countrySubdivisionJSON?lat={$lat}&lng={$lng}");
        $data = $response->toArray();

        $country = $data['countryName'];
        $countryZone = $this->repository->findOneBy(['name' => $country]);

        //adminName1 is the name the geonames api uses for lower level areas such as countries or large states.
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
        foreach($body as $k => $v)
        {
            foreach($body as $key => $value)
            {
                if($k != $key && $v['scientific_name'] == $value['scientific_name'])
                {
                    unset($body[$k]);
                }
            }
        }

        //Get the scientific names in separate array and use them to determine if they are already existing.
        $scientificNames = array_column($body, 'scientific_name');

        $existingPlants = $this->plantRepository->findByScientificName($scientificNames);

        foreach ($body as $item) {
            $insert = $this->persistPlantInZone($existingPlants, $item);

            $insertCount += $insert;
        }

        return [
            'insertCount' => $insertCount,
            'total' => $total
        ];
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

            $this->em->persist($plant);

            $distributionPlant = new DistributionZonePlant();
            $distributionPlant->setPlant($plant)
                ->setDistributionZone($zone);

            $this->em->persist($distributionPlant);

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
                $insertCount = 1;
            }
            $plant = $existing;
//            }
        }

        $this->plants->set($item['scientific_name'], $plant);

        return $insertCount;
    }
}