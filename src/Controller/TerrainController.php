<?php


namespace App\Controller;


use App\Entity\Terrain;
use App\Service\Entity\TerrainService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

/**
 * Class TerrainController
 *
 * @package App\Controller
 */
#[Route("/api/terrains", name: "api_terrain_")]
class TerrainController extends AbstractController
{
    public function __construct(private TerrainService $terrainService)
    { }

    /**
     * The method returns all the user terrains.
     *
     * @throws ExceptionInterface
     */
    #[Route("", name: "user_terrains", methods: ["GET"])]
    public function getUserTerrains(): JsonResponse
    {
        return $this->terrainService->getUserTerrains($this->getUser());
    }

    /**
     *  The method calls the service method, which gets the user terrain.
     *
     * @param Terrain $terrain
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    #[Route("/{id}", name: "get_user_terrain", methods: ["GET"])]
    public function getUserTerrain(Terrain $terrain): JsonResponse
    {
        return $this->terrainService->getUserTerrain($terrain, $this->getUser());
    }

    /**
     * The method calls the service method, which deletes the user terrain.
     *
     * @param Terrain $terrain
     * @return JsonResponse
     */
    #[Route("/{id}", name: "delete_user_terrain", methods: ["DELETE"])]
    public function deleteUserTerrain(Terrain $terrain): JsonResponse
    {
        $zipDir = $this->getParameter('app.zip_directory');

        return $this->terrainService->deleteUserTerrain($terrain, $this->getUser(), $zipDir);
    }
}