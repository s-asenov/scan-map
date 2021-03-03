<?php


namespace App\Controller;


use App\Entity\DistributionZone;
use App\Entity\Terrain;
use App\Repository\DistributionZonePlantRepository;
use App\Repository\DistributionZoneRepository;
use App\Repository\PlantRepository;
use App\Util\FormHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/statistics", name="stats_")
 *
 * Class StatisticsController
 * @package App\Controller
 */
class StatisticsController extends AbstractController
{
    /**
     * @var DistributionZoneRepository
     */
    private $dzRepository;
    /**
     * @var DistributionZonePlantRepository
     */
    private $dzPlantRepository;
    /**
     * @var PlantRepository
     */
    private $plantRepository;

    public function __construct(DistributionZoneRepository $dzRepository, DistributionZonePlantRepository $dzPlantRepository, PlantRepository $plantRepository)
    {
        $this->dzRepository = $dzRepository;
        $this->dzPlantRepository = $dzPlantRepository;
        $this->plantRepository = $plantRepository;
    }

    /**
     * @Route("/zones/fetched", name="zone_fetched", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getFetchedCount(): JsonResponse
    {
        $count = $this->dzRepository->getFetchedCount();

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'meta' => $count
        ]);
    }

    /**
     * @Route("/zones/most-fetched", name="most_fetched_zone", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getMostFetchedCount(): JsonResponse
    {
        $count = $this->dzRepository->getMostFetched();

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'meta' => $count
        ]);
    }

    /**
     * @Route("/terrains", name="terrains", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getTerrainCount(): JsonResponse
    {
        $count = $this->getDoctrine()->getRepository(Terrain::class)->count([]);

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'meta' => $count
        ]);
    }

    /**
     * @Route("/zones/plants-most", name="most_plants", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getMostPlantsInDistributionZone(): JsonResponse
    {
        $count = $this->dzPlantRepository->getMostPlantsInDistributionZone();

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'meta' => $count
        ]);
    }

    /**
     * @Route("/zones/plants-top", name="top_zones_by_most_plants", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getTopZonesByPlantsCount(): JsonResponse
    {
        $count = $this->dzPlantRepository->getTopZonesByPlantsCount();

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'meta' => $count
        ]);
    }

    /**
     * @Route("/plants/most-seen", name="most_seen_plants", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getMostSeenPlants(): JsonResponse
    {
        $count = $this->dzPlantRepository->getMostSeenPlants();

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'meta' => $count
        ]);
    }

    /**
     * @Route("/plants/most-seen-names", name="most_seen_plants_with_name", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getMostSeenPlantsWithZoneName(): JsonResponse
    {
        $count = $this->dzPlantRepository->getMostSeenPlantsWithZoneName();

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'meta' => $count
        ]);
    }

    /**
     * @Route("/zones/{id}/plants", name="plants_in_zone", methods={"GET"})
     *
     * @param DistributionZone $zone
     * @return JsonResponse
     */
    public function getPlantsFromZone(DistributionZone $zone): JsonResponse
    {
        $count = $zone->getDistributionZonePlants()->count();

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'meta' => $count
        ]);
    }

    /**
     * @Route("/plants", name="all_plants", methods={"GET"})
     */
    public function getAllPlantsCount(): JsonResponse
    {
        $count = $this->plantRepository->count([]);

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'meta' => $count
        ]);
    }

    /**
     * @Route("/zones", name="all_zones", methods={"GET"})
     */
    public function getAllDZCount(): JsonResponse
    {
        $count = count($this->dzRepository->findAll());

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'meta' => $count
        ]);
    }

    /**
     * @Route("/all-statistics", name="all_statistics", methods={"GET"})
     */
    public function allStatistics(): JsonResponse
    {
        $mostPlantsInDz = $this->dzPlantRepository->getMostPlantsInDistributionZone();
        $fetchedCount = $this->dzRepository->getFetchedCount();
        $mostFetched = $this->dzRepository->getMostFetched();
        $mostSeenPlants = $this->dzPlantRepository->getMostSeenPlants();

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'meta' => [
                'mostPlantsInDz' => $mostPlantsInDz,
                'fetchedCount' => $fetchedCount,
                'mostFetched' => $mostFetched,
                'mostSeenCounts' => $mostSeenPlants
            ]
        ]);
    }
}