<?php


namespace App\Controller;


use App\Entity\TerrainKey;
use App\Repository\TerrainRepository;
use App\Serializer\Normalizer\TerrainNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TerrainController extends AbstractController
{
    /**
     * @Route("/api/terrains", name="api_user_terrains")
     * @param TerrainRepository $repository
     * @param TerrainNormalizer $normalizer
     * @return JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getUserTerrains(TerrainRepository $repository, TerrainNormalizer $normalizer): JsonResponse
    {
        $terrains = $repository->findBy(['user' => $this->getUser()->getId()]);

        $terrainsArray = [];

        foreach ($terrains as $terrain) {
            $normalized = $normalizer->normalize($terrain);

            $terrainsArray[] = $normalized;
        }

        return new JsonResponse([
            'status' => "success",
            'terrains' => $terrainsArray
        ]);
    }

    /**
     * @Route("/api/terrains/{id}", name="api_user_terrain", methods={"GET"})
     * @param int $id
     * @param TerrainRepository $repository
     * @param TerrainNormalizer $normalizer
     * @return JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getUserTerrain(int $id, TerrainRepository $repository, TerrainNormalizer $normalizer): JsonResponse
    {
        $terrain = $repository->find($id);

        if (!$terrain || $terrain->getUser() !== $this->getUser()) {
            return new JsonResponse([
                'status' => "error",
                "meta" => "Unauthorized"
            ]);
        }

        $normalized = $normalizer->normalize($terrain);

        return new JsonResponse([
            'status' => "success",
            'terrain' => $normalized
        ]);
    }

    /**
     * @Route("/api/terrains/{id}", name="api_user_terrain_delete", methods={"DELETE"})
     * @param int $id
     * @param TerrainRepository $repository
     * @return JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function deleteUserTerrain(int $id, TerrainRepository $repository, EntityManagerInterface $em): JsonResponse
    {
        $terrain = $repository->find($id);

        if (!$terrain || $terrain->getUser() !== $this->getUser()) {
            return new JsonResponse([
                'status' => "error",
                "meta" => "Unauthorized"
            ]);
        }

        $em->remove($terrain);
        $em->flush();

        return new JsonResponse([
            'status' => "success",
            'terrain' => "deleted"
        ]);
    }
}