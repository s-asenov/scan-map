<?php


namespace App\Service;


use App\Entity\DistributionZone;
use App\Entity\DistributionZonePlant;
use App\Entity\Plant;
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
    private $trefleApi;
    private $geonamesApi;
    private $repository;
    private $plantRepository;
    private $em;
    private $zone;

    public function __construct(EntityManagerInterface $em, HttpClientInterface $trefleApi, HttpClientInterface $geonamesApi)
    {
        $this->trefleApi = $trefleApi;
        $this->geonamesApi = $geonamesApi;
        $this->repository = $em->getRepository(DistributionZone::class);;
        $this->plantRepository = $em->getRepository(Plant::class);;
        $this->em = $em;
    }

    /**
     * @param $lat
     * @param $lng
     * @return mixed|null
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getPlants($lat, $lng)
    {
        $batchSize = 1000; //bulk inserts with doctrine

        $zone = $this->getDistributionZone($lat, $lng);

        if ($zone === false) {
            return null;
        }

        $this->zone = $zone;
        $zoneId = $zone->getId();

        $request = $this->getPlantsFromPage("distributions/{$zoneId}/plants");

        $plants = $request['plants'];
        $total = $request['total'];

        $zonePlants = $this->getPlantsInZone($zoneId);

        if ($total === count($zonePlants)) {
            $plants = [];

            foreach ($zonePlants as $zonePlant) {
                $plant = $zonePlant->getPlant();

                $plants[$plant->getScientificName()] = $plant;
            }
            return $plants;
        }

        $pages = ceil($total / 20);
        $count = count($plants);

        if ($pages > 1) {
            for ($i = 2; $i<=$pages; $i++) {
                $response = $this->getPlantsFromPage("distributions/{$zoneId}/plants?page={$i}");
                $responsePlants = $response['plants'];

                $count += count($responsePlants);

                if ($count % $batchSize === 0) {
                    $this->em->flush();
//                    $this->em->clear();
                }

                foreach ($responsePlants as $key => $value) {
                    $plants[$key] = $value;
                }
            }
        }

        $this->em->flush();
//        $this->em->clear();

        return $plants;
    }

    /**
     * @param $lat
     * @param $lng
     * @return DistributionZone|bool
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function getDistributionZone($lat, $lng)
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
            return false;
        }

        return $zone;
    }

    private function getPlantsInZone(int $zone)
    {
        return $this->em->getRepository(DistributionZonePlant::class)->findByDistributionZone($zone);
    }

    private function getPlantsFromPage(string $url)
    {
        $response = $this->trefleApi->request('GET', $url);
        $data = $response->toArray();

        $body = $data['data'];
        $total = $data['meta']['total'];

        $plants = [];

        $scientificNames = array_column($body, 'scientific_name');

        //first
        $existingPlants = $this->plantRepository->findByScientificName($scientificNames);


        foreach ($body as $item) {
            $existing = $this->plantInArray($existingPlants, $item);

            if (!$existing) {
                $plant = new Plant();

                $plant->setScientificName($item['scientific_name'])
                    ->setCommonName($item['common_name'])
                    ->setImageUrl($item['image_url'])
                    ->setInformation("");

                $this->em->persist($plant);

                $distributionPlant = new DistributionZonePlant();
                $distributionPlant->setPlant($plant)
                    ->setDistributionZone($this->zone);

                $this->em->persist($distributionPlant);

            } else {
                $plant = $existing;

                $test = $this->em->getRepository(DistributionZonePlant::class)->findOneBy([
                    'distributionZone' => $this->zone->getId(),
                    'plant' => $existing
                ]);

                if (!$test) {
                    $distributionPlant = new DistributionZonePlant();
                    $distributionPlant->setPlant($plant)
                        ->setDistributionZone($this->zone);

                    $this->em->persist($distributionPlant);
                }
            }

            $plants[$item['scientific_name']] = $plant;

        }

        return [
            'plants' => $plants,
            'total' => $total
        ];
    }

    /**
     * @param Plant[]|object[] $existingPlants
     * @param array $item
     * @return false|mixed
     */
    private function plantInArray(array $existingPlants, array $item)
    {
        $exists = array_key_exists($item['scientific_name'], $existingPlants);

        if ($exists) {
            return $existingPlants[$item['scientific_name']];
        } else {
            return false;
        }

//        $key = array_search($item['scientific_name'], array_column($existingPlants, 'scientificName'));
//
//        if ($key !== false) {
//            return $existingPlants[$key];
//        } else {
//            return false;
//        }

//        foreach ($existingPlants as $existingPlant) {
//            if ($existingPlant->getScientificName() === $item['scientific_name']) {
//                return $existingPlant;
//            }
//        }
//        return false;

    }
}