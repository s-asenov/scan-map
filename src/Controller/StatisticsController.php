<?php


namespace App\Controller;


use App\Entity\DistributionZone;
use App\Service\Entity\StatisticsService;
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

    public function __construct(
        private StatisticsService $statisticsService
    ) { }

    /**
     * The method returns the number of fetched distribution zones.
     *
     * @return JsonResponse
     */
    #[Route("/zones/fetched", name: "zone_fetched", methods: ["GET"])]
    public function getFetchedCount(): JsonResponse
    {
        return $this->statisticsService->getFetchedCount();
    }

    /**
     * The method returns the top 10 most persisted|fetched distribution zones.
     *
     * @return JsonResponse
     */
    #[Route("/zones/most-fetched", name: "most_fetched_zone", methods: ["GET"])]
    public function getMostFetchedCount(): JsonResponse
    {
        return $this->statisticsService->getMostFetchedCount();
    }

    /**
     * The method returns the number of generated terrains.
     *
     * @return JsonResponse
     */
    #[Route("/terrains", name: "terrains", methods: ["GET"])]
    public function getTerrainCount(): JsonResponse
    {
        return $this->statisticsService->getTerrainCount();
    }

    /**
     * The method returns the distribution zone number of plants.
     *
     * @return JsonResponse
     */
    #[Route("/zones/plants-most", name: "most_plants", methods: ["GET"])]
    public function getMostPlantsInDistributionZone(): JsonResponse
    {
        return $this->statisticsService->getMostPlantsInDistributionZone();
    }

    /**
     * The method returns the top 10 zones with the biggest
     * amount of plants in the persisted distribution zones.
     *
     * @return JsonResponse
     */
    #[Route("/zones/plants-top", name: "top_zones_by_most_plants", methods: ["GET"])]
    public function getTopZonesByPlantsCount(): JsonResponse
    {
        return $this->statisticsService->getTopZonesByPlantsCount();
    }

    /**
     * The method returns the top 10 most seen plants with their grouped by
     * distribution zones.
     *
     * @return JsonResponse
     */
    #[Route("/plants/most-seen", name: "most_seen_plants", methods: ["GET"])]
    public function getMostSeenPlants(): JsonResponse
    {
        return $this->statisticsService->getMostSeenPlants();
    }

    /**
     * The method returns the top 10 most seen plants with
     * the names of the distribution zones.
     *
     * @return JsonResponse
     */
    #[Route("/plants/most-seen-names", name: "most_seen_plants_with_name", methods: ["GET"])]
    public function getMostSeenPlantsWithZoneName(): JsonResponse
    {
        return $this->statisticsService->getMostSeenPlantsWithZoneName();
    }

    /**
     * The method returns the number of plants in distribution zone.
     *
     * @param DistributionZone $zone
     * @return JsonResponse
     */
    #[Route("/zones/{id}/plants", name: "plants_in_zone", methods: ["GET"])]
    public function getPlantsFromZone(DistributionZone $zone): JsonResponse
    {
        return $this->statisticsService->getPlantsFromZone($zone);
    }

    /**
     * The method returns the number of plants
     * persisted in the DB.
     *
     * @return JsonResponse
     */
    #[Route("/plants", name: "all_plants", methods: ["GET"])]
    public function getAllPlantsCount(): JsonResponse
    {
        return $this->statisticsService->getAllPlantsCount();
    }

    /**
     * The method returns a list of all distribution zones
     *
     * @return JsonResponse
     */
    #[Route("/zones", name: "all_zones", methods: ["GET"])]
    public function getAllDZCount(): JsonResponse
    {
        return $this->statisticsService->getAllDZ();
    }
}