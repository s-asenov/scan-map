<?php


namespace App\Controller;


use App\Entity\TerrainKey;
use App\Service\Entity\TerrainService;
use App\Service\ZipService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractController
{
    public function __construct(private TerrainService $terrainService)
    {
    }

    /**
     * The method is responsible for creating the terrain entity
     * and saving the ZipArchive with the necessary data.
     *
     * @return JsonResponse
     */
    #[Route("/api/map", name: "api_map_save", methods: ["POST"])]
    public function saveZip(): JsonResponse
    {
        return $this->terrainService->createTerrain($this->getUser());
    }

    #[Route("/api/sync/{zone}", name: "api_sync", methods: ["POST"])]
    public function sync($zone): JsonResponse
    {
        return $this->terrainService->syncTables($zone);
    }

    /**
     * @param TerrainKey $terrainKey
     * @param ZipService $zipService
     * @return BinaryFileResponse|JsonResponse
     */
    #[Route("/zip/{id}", name: "get_zip", methods: ["GET"])]
    public function getZip(TerrainKey $terrainKey, ZipService $zipService)
    {
        return $this->terrainService->getTerrainZipFile($terrainKey, $zipService);
    }
}