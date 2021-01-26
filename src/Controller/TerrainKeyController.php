<?php


namespace App\Controller;


use App\Entity\Terrain;
use App\Entity\TerrainKey;
use App\Repository\TerrainKeyRepository;
use App\Repository\TerrainRepository;
use App\Serializer\Normalizer\TerrainKeysNormalizer;
use App\Util\FormHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class TerrainKeyController extends AbstractController
{
    /**
     * @Route("/api/keys/{id}", name="api_key_save", methods={"POST"})
     * @param Terrain $terrain
     * @param EntityManagerInterface $em
     * @param TerrainKeysNormalizer $normalizer
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function addKey(Terrain $terrain, EntityManagerInterface $em, TerrainKeysNormalizer $normalizer): JsonResponse
    {
        if ($terrain->getUser() !== $this->getUser()) {
            return new JsonResponse([
                'status' => FormHelper::META_ERROR,
                'meta' => FormHelper::UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        $terrainKey = new TerrainKey($this->getUser()->getId());
        $terrainKey->setTerrain($terrain);

        $em->persist($terrainKey);
        $em->flush();

        $normalized = $normalizer->normalize($terrainKey);

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'key' => $normalized
        ]);
    }

    /**
     * @Route("/api/keys", name="api_key_all", methods={"GET"})
     * @param TerrainRepository $repository
     * @param TerrainKeyRepository $keyRepository
     * @param TerrainKeysNormalizer $normalizer
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function getUserKeys(TerrainRepository $repository, TerrainKeyRepository $keyRepository, TerrainKeysNormalizer $normalizer): JsonResponse
    {
        $terrains = $repository->findBy([
            'user' => $this->getUser()->getId()
        ]);

        $terrainKeysArray = [];

        foreach ($terrains as $terrain) {
            $terrainKeys = $terrain->getTerrainKeys();

            foreach ($terrainKeys as $terrainKey) {
                $normalized = $normalizer->normalize($terrainKey);
                $terrainKeysArray[] = $normalized;
            }
        }

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'keys' => $terrainKeysArray
        ]);
    }

    /**
     * @Route("/api/keys/{id}", name="api_key", methods={"GET"})
     * @param TerrainKey $terrainKey
     * @param TerrainKeysNormalizer $normalizer
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function getKey(TerrainKey $terrainKey, TerrainKeysNormalizer $normalizer): JsonResponse
    {
        if ($terrainKey->getTerrain()->getUser() !== $this->getUser()) {
            return new JsonResponse([
                'status' => FormHelper::META_ERROR,
            ]);
        }

        $normalized = $normalizer->normalize($terrainKey);

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'key' => $normalized
        ]);
    }
}