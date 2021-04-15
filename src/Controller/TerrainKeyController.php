<?php


namespace App\Controller;


use App\Entity\Terrain;
use App\Entity\TerrainKey;
use App\Service\Entity\TerrainKeyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

/**
 *
 * Class TerrainKeyController
 *
 * 
 *
 * @package App\Controller
 */
#[Route("/api/keys", name: "api_terrain_keys_")]
class TerrainKeyController extends AbstractController
{
    public function __construct(private TerrainKeyService $keyService)
    {
    }

    /**
     * The method calls the terrain key service which create a new key.
     *
     * @param Terrain $terrain
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    #[Route("/{id}", name: "api_key_save", methods: ["POST"])]
    public function createKey(Terrain $terrain): JsonResponse
    {
        return $this->keyService->createKey($terrain, $this->getUser());
    }

    /**
     * The method calls the terrain key service and gets the keys of all user terrains.
     *
     * @return JsonResponse
     */
    #[Route("", name: "api_keys_all", methods: ["GET"])]
    public function getUserKeys(): JsonResponse
    {
        return $this->keyService->getUserKeys($this->getUser());
    }

    /**
     * The method calls the service and gets the specific key assigned to the user.
     *
     * @param TerrainKey $terrainKey
     * @return JsonResponse
     */
    #[Route("/{id}", name: "api_key", methods: ["GET"])]
    public function getKey(TerrainKey $terrainKey): JsonResponse
    {
        $this->keyService->getKey($terrainKey, $this->getUser());
    }
}