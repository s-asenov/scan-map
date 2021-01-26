<?php


namespace App\Controller;


use App\Entity\Terrain;
use App\Repository\TerrainRepository;
use App\Serializer\Normalizer\TerrainNormalizer;
use App\Util\FormHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class TerrainController extends AbstractController
{
    /**
     * @Route("/api/terrains", name="api_user_terrains", methods={"GET"})
     * @param TerrainRepository $repository
     * @param TerrainNormalizer $normalizer
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function getUserTerrains(TerrainRepository $repository, TerrainNormalizer $normalizer): JsonResponse
    {
        $terrains = $repository->findBy([
            'user' => $this->getUser()->getId()
        ]);

        $terrainsArray = [];

        foreach ($terrains as $terrain) {
            $normalized = $normalizer->normalize($terrain);

            $terrainsArray[] = $normalized;
        }

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'terrains' => $terrainsArray
        ]);
    }

    /**
     * @Route("/api/terrains/{id}", name="api_user_terrain", methods={"GET"})
     * @param Terrain $terrain
     * @param TerrainNormalizer $normalizer
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function getUserTerrain(Terrain $terrain, TerrainNormalizer $normalizer): JsonResponse
    {
        if (!$terrain || $terrain->getUser() !== $this->getUser()) {
            return new JsonResponse([
                'status' => FormHelper::META_ERROR,
                "meta" => FormHelper::UNAUTHORIZED
            ], 401);
        }

        $normalized = $normalizer->normalize($terrain);

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'terrains' => $normalized
        ]);
    }

    /**
     * @Route("/api/terrains/{id}", name="api_user_terrain_delete", methods={"DELETE"})
     * @param Terrain $terrain
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function deleteUserTerrain(Terrain $terrain, EntityManagerInterface $em): JsonResponse
    {
        if ($terrain->getUser() !== $this->getUser()) {
            return new JsonResponse([
                'status' => FormHelper::META_ERROR,
                "meta" => FormHelper::UNAUTHORIZED
            ], 401);
        }

        $em->remove($terrain);
        $em->flush();

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'terrain' => "deleted"
        ]);
    }
}