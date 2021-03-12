<?php


namespace App\Service\Entity;


use App\Entity\Terrain;
use App\Entity\TerrainKey;
use App\Entity\User;
use App\Repository\TerrainRepository;
use App\Serializer\Normalizer\TerrainKeysNormalizer;
use App\Util\FormHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TerrainKeyService
{
    public function __construct(
        private EntityManagerInterface $em,
        private TerrainKeysNormalizer $normalizer,
        private TerrainRepository $terrainRepository
    ) { }

    /**
     * The method creates key to the assigned user terrain.
     *
     * @param Terrain $terrain
     * @param User $user
     * @return JsonResponse
     */
    public function createKey(Terrain $terrain, User $user): JsonResponse
    {
        if ($terrain->getUser() !== $user) {
            return new JsonResponse([
                'status' => FormHelper::META_ERROR,
                'meta' => FormHelper::UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        $terrainKey = new TerrainKey($user->getId());
        $terrainKey->setTerrain($terrain);

        $this->em->persist($terrainKey);
        $this->em->flush();

        $normalized = $this->normalizer->normalize($terrainKey);

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'key' => $normalized
        ]);
    }

    /**
     * The method gets the keys to the assigned user.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function getUserKeys(User $user): JsonResponse
    {
        $terrains = $this->terrainRepository->findBy([
            'user' => $user->getId()
        ]);

        $terrainKeysArray = [];

        foreach ($terrains as $terrain) {
            $terrainKeys = $terrain->getTerrainKeys();

            foreach ($terrainKeys as $terrainKey) {
                $normalized = $this->normalizer->normalize($terrainKey);
                $terrainKeysArray[] = $normalized;
            }
        }

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'keys' => $terrainKeysArray
        ]);
    }

    /**
     * The method gets the specific terrain key to the assigned user.
     *
     * @param TerrainKey $terrainKey
     * @param User $user
     * @return JsonResponse
     */
    public function getKey(TerrainKey $terrainKey, User $user): JsonResponse
    {
        if ($terrainKey->getTerrain()->getUser() !== $user) {
            return new JsonResponse([
                'status' => FormHelper::META_ERROR,
            ]);
        }

        $normalized = $this->normalizer->normalize($terrainKey);

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'key' => $normalized
        ]);
    }
}