<?php


namespace App\Controller;


use App\Entity\DistributionZone;
use App\Repository\DistributionZonePlantRepository;
use App\Repository\DistributionZoneRepository;
use App\Util\FormHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="dz_")
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

    public function __construct(DistributionZoneRepository $dzRepository, DistributionZonePlantRepository $dzPlantRepository)
    {
        $this->dzRepository = $dzRepository;
        $this->dzPlantRepository = $dzPlantRepository;
    }

    /**
     * @Route("/zone/fetched", name="zone_fetched", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getFetchedCount(): JsonResponse
    {
        $count = $this->dzRepository->getFetchedCount();

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'count' => $count
        ]);
    }

    /**
     * @Route("/zone/most-fetched", name="most_fetched_zone", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getMostFetchedCount(): JsonResponse
    {
        $count = $this->dzRepository->getMostFetched();

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'count' => $count
        ]);
    }

    /**
     * @Route("/zone/plants-most", name="most_plants", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getMostPlantsInDistributionZone(): JsonResponse
    {
        $count = $this->dzPlantRepository->getMostPlantsInDistributionZone();

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'count' => $count
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
            'count' => $count
        ]);
    }

    /**
     * @Route("/zone/{id}/plants", name="plants_in_zone", methods={"GET"})
     *
     * @param DistributionZone $zone
     * @return JsonResponse
     */
    public function getPlantsFromZone(DistributionZone $zone): JsonResponse
    {
        $count = $zone->getDistributionZonePlants()->count();

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'count' => $count
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
            'count' => [
                'mostPlantsInDz' => $mostPlantsInDz,
                'fetchedCount' => $fetchedCount,
                'mostFetched' => $mostFetched,
                'mostSeenCounts' => $mostSeenPlants
            ]
        ]);
    }
}