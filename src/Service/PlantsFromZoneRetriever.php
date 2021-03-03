<?php


namespace App\Service;


use App\Entity\DistributionZone;
use App\Entity\DistributionZonePlant;
use App\Entity\Plant;
use App\Repository\DistributionZonePlantRepository;
use App\Repository\DistributionZoneRepository;
use App\Repository\PlantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PlantsFromZoneRetriever
{
    const PLANTS_PER_PAGE = 20; //trefle`s pagination

    private $trefleApi;

    private $geonamesApi;

    /**
     * @var DistributionZoneRepository
     */
    private $repository;

    /**
     * @var PlantRepository
     */
    private $plantRepository;

    /**
     * @var DistributionZonePlantRepository
     */
    private $zonePlantRepository;

    private $em;

    private $zoneId;

    /**
     * @var PlantsInfoRetriever
     */
    private $infoRetriever;

    public function __construct(PlantsInfoRetriever $infoRetriever, EntityManagerInterface $em, HttpClientInterface $trefleApi, HttpClientInterface $geonamesApi)
    {
        $this->trefleApi = $trefleApi;
        $this->geonamesApi = $geonamesApi;
        $this->em = $em;
        $this->repository = $em->getRepository(DistributionZone::class);
        $this->plantRepository = $em->getRepository(Plant::class);
        $this->zonePlantRepository = $em->getRepository(DistributionZonePlant::class);
        $this->infoRetriever = $infoRetriever;
    }

    /**
     * The method creates an initial call to the plants api and
     * loops through the rest of the pages, if there are any.
     *
     * @param DistributionZone|bool $zone
     * @return array
     *
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getPlants($zone): array
    {
        if ($zone === false) {
            return [];
        }

        /*
         * If the total count meta is the same as number of saved plants in zone
         * return the already saved.
         */
        if ($zone->getFetched()) {
            $plants = [];
            $zonePlants = $zone->getDistributionZonePlants();

            foreach ($zonePlants as $zonePlant) {
                $plant = $zonePlant->getPlant();

                $plants[$plant->getScientificName()] = $plant;
            }

            return $plants;
        }

        $batchSize = 500;

        $this->zoneId = $zone->getId();

        $request = $this->getPlantsFromPage("distributions/{$this->zoneId}/plants", []);

        $plants = $this->infoRetriever->getInfo($request['plants']);
        $total = $request['total'];

        $pages = ceil($total / self::PLANTS_PER_PAGE);
        $count = $request['batchCount'];

        $newPlants = [];

        if ($pages > 1) {
            for ($i = 2; $i<=$pages; $i++) {
                $response = $this->getPlantsFromPage("distributions/{$this->zoneId}/plants?page={$i}", $plants);

                $responsePlants = $response['plants'];

                $count += $response['batchCount'];

                foreach ($responsePlants as $key => $value) {
//                    $plants[$key] = $value;
                    $newPlants[$key] = $value;
                }

                $withInfo = $this->infoRetriever->getInfo($newPlants);

                $plants += $withInfo;

                //can't use Modulo ($count % $batchSize) here
                if ($count > $batchSize) {

                    $newPlants = [];
                    $count = 0;

                    $this->em->flush();
                    $this->em->clear();
                }
            }
            $this->em->flush();
            $this->em->clear();
        }

        return $plants;
    }


    /**
     * The method send a request to the geonames api and determines the distribution zone.
     *
     * @param string $lat
     * @param string $lng
     * @return false|DistributionZone
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getDistributionZone(string $lat, string $lng)
    {
        $response = $this->geonamesApi->request('GET', "countrySubdivisionJSON?lat={$lat}&lng={$lng}");
        $data = $response->toArray();

        $country = $data['countryName'];
        $countryZone = $this->repository->findOneBy(['name' => $country]);

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
     * @param array $persistedObjects The already persisted objects from previous requests.
     * @return array of the Plants entities|objects and the total number of plants.
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @see persistPlantInZone()
     *
     */
    private function getPlantsFromPage(string $url, array $persistedObjects): array
    {
        $batchCount = 0;
        $response = $this->trefleApi->request('GET', $url);
        $data = $response->toArray();

        /*
         * Every response has data containing the plants and
         * total meta containing the number of all plants in the zone.
         */
        $body = $data['data'];
        $total = $data['meta']['total'];

        $plants = [];

        //Get the scientific names in separate array and use them to determine if they are already existing.
        $scientificNames = array_column($body, 'scientific_name');

        foreach ($body as $item) {
            $persist = $this->persistPlantInZone($scientificNames, $item, $persistedObjects + $plants);
            $batchCount += $persist['batchCount'];

            $plant = $persist['plant'];
            $plants[$item['scientific_name']] = $plant;
        }

        return [
            'plants' => $plants,
            'total' => $total,
            'batchCount' => $batchCount
        ];
    }

    /**
     * The method responsible for saving the required information for the plants in the DB
     * such as: scientificName, commonName and imageUrl.
     *
     * @param array $scientificNames The list of all the scientific names of the current request.
     * @param array $item The plant array from the json response.
     * @param array $parsedPlants The current persisted entities from previous requests
     * @return Plant|mixed|object
     */
    private function persistPlantInZone(array $scientificNames, array $item, array $parsedPlants)
    {
        $batchCount = 0;

        $existingPlants = $this->plantRepository->findByScientificName($scientificNames);

        /**
         * @var $zone DistributionZone
         * @var $existing bool|Plant
         */
        $zone = $this->em->getRepository(DistributionZone::class)->find($this->zoneId);

        $existing = $this->plantInArray($existingPlants, $item, $parsedPlants);

        /*
         * If the plant does not exist create the new entities and persist them,
         * otherwise make a relation between the plant and distribution zones.
         */
        if (!$existing) {
            $plant = new Plant();

            $plant->setScientificName($item['scientific_name'])
                ->setCommonName($item['common_name'])
                ->setImageUrl($item['image_url']);

            $this->em->persist($plant);

            $distributionPlant = new DistributionZonePlant();
            $distributionPlant->setPlant($plant)
                ->setDistributionZone($zone);

            $this->em->persist($distributionPlant);

            $batchCount = 2;
        } else {
            $plant = $existing;

            /*
             * If the existing id is null it means it is not inserted in db
             * and there are already persisted entities
             */
            if ($existing->getId() !== null) {
                $zonePlant = $this->zonePlantRepository->findOneBy([
                    'distributionZone' => $this->zoneId,
                    'plant' => $existing->getId()
                ]);

                if (!$zonePlant) {
                    $distributionPlant = new DistributionZonePlant();
                    $distributionPlant->setPlant($plant)
                        ->setDistributionZone($zone);

                    $this->em->persist($distributionPlant);
                    $batchCount = 1;
                }
            }
        }

        return [
            'plant' => $plant,
            'batchCount' => $batchCount
        ];
    }

    /**
     * @param Plant[]|object[] $existingPlants Array of the Plants entities, which are being searched.
     * @param array $plant The plant which is searched in the array.
     * @param array $parsedPlants The current persisted entities from previous requests
     * @return false|mixed
     */
    private function plantInArray(array $existingPlants, array $plant, array $parsedPlants)
    {
        $searched = $existingPlants + $parsedPlants;

//        $exists = array_key_exists($plant['scientific_name'], $searched);
        $exists = isset($searched[$plant['scientific_name']]);

        if ($exists) {
            return $searched[$plant['scientific_name']];
        } else {
            return false;
        }
    }
}