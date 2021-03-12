<?php


namespace App\Service\Entity;


use App\Entity\DistributionZone;
use App\Repository\DistributionZonePlantRepository;
use App\Repository\DistributionZoneRepository;
use App\Repository\PlantRepository;
use App\Repository\TerrainRepository;
use App\Util\FormHelper;
use Symfony\Component\HttpFoundation\JsonResponse;

class StatisticsService
{
    public function __construct(
        private DistributionZoneRepository $dzRepository,
        private DistributionZonePlantRepository $dzPlantRepository,
        private PlantRepository $plantRepository,
        private TerrainRepository $terrainRepository
    ) { }

    /**
     * The method returns the number of fetched distribution zones.
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
     * The method returns the top 10 most persisted|fetched distribution zones.
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
     * The method returns the number of generated terrains.
     *
     * @return JsonResponse
     */
    public function getTerrainCount(): JsonResponse
    {
        $count = $this->terrainRepository->count([]);

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'meta' => $count
        ]);
    }

    /**
     * The method returns the distribution zone number of plants.
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
     * The method returns the top 10 zones with the biggest
     * amount of plants in the persisted distribution zones.
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
     * The method returns the top 10 most seen plants with their grouped by
     * distribution zones.
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
     * The method returns the top 10 most seen plants with
     * the names of the distribution zones.
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
     * The method returns the number of plants in distribution zone.
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
     * The method returns the number of plants
     * persisted in the DB.
     *
     * @return JsonResponse
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
     * The method returns a list of all distribution zones
     *
     * @return JsonResponse
     */
    public function getAllDZ(): JsonResponse
    {
        $count = $this->dzRepository->findAll();

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'meta' => $count
        ]);
    }
}