<?php


namespace App\Controller;


use App\Entity\TerrainKey;
use App\Repository\TerrainKeyRepository;
use App\Repository\TerrainRepository;
use App\Serializer\Normalizer\TerrainKeysNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TerrainKeyController extends AbstractController
{
    /**
     * @Route("/api/keys/{id}", name="api_key_save", methods={"POST"})
     * @param int $id
     * @param EntityManagerInterface $em
     * @param TerrainRepository $repository
     * @return JsonResponse
     */
    public function addKey(int $id, EntityManagerInterface $em, TerrainRepository $repository, TerrainKeysNormalizer $normalizer): JsonResponse
    {
        $terrain = $repository->find($id);

        if ($terrain->getUser() !== $this->getUser()) {
            return new JsonResponse([
                'status' => "error",
            ]);
        }
        $terrainKey = new TerrainKey($this->getUser()->getId());
        $terrainKey->setTerrain($terrain);

        $em->persist($terrainKey);
        $em->flush();

        $normalized = $normalizer->normalize($terrainKey);

        return new JsonResponse([
            'status' => "success",
            'key' => $normalized
        ]);
    }

    /**
     * @Route("/api/keys", name="api_key_all")
     * @param TerrainRepository $repository
     * @param TerrainKeyRepository $keyRepository
     * @param TerrainKeysNormalizer $normalizer
     * @return JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getUserKeys(TerrainRepository $repository, TerrainKeyRepository $keyRepository, TerrainKeysNormalizer $normalizer): JsonResponse
    {
        $terrains = $repository->findBy(['user' => $this->getUser()->getId()]);

        $terrainKeysArray = [];

        foreach ($terrains as $terrain) {
             $terrainKeys = $keyRepository->findBy(['terrain' => $terrain]);

             foreach ($terrainKeys as $terrainKey) {
                 $normalized = $normalizer->normalize($terrainKey);
                 $terrainKeysArray[] = $normalized;
             }
        }


        return new JsonResponse([
            'status' => "success",
            'keys' => $terrainKeysArray
        ]);
    }

    /**
     * @Route("/api/keys/{id}", name="api_key", methods={"GET"})
     * @param string $id
     * @param TerrainKeysNormalizer $normalizer
     * @param TerrainKeyRepository $repository
     * @return JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getKey(string $id, TerrainKeysNormalizer $normalizer, TerrainKeyRepository $repository): JsonResponse
    {
        $terrainKey = $repository->find($id);

        if ($terrainKey->getTerrain()->getUser() !== $this->getUser()) {
            return new JsonResponse([
                'status' => "error",
            ]);
        }

        $normalized = $normalizer->normalize($terrainKey);

        return new JsonResponse([
            'status' => "success",
            'key' => $normalized
        ]);
    }
}