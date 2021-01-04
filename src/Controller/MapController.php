<?php


namespace App\Controller;


use App\Entity\Terrain;
use App\Entity\TerrainKey;
use App\Repository\TerrainKeyRepository;
use App\Repository\TerrainRepository;
use App\Serializer\Normalizer\PlantNormalizer;
use App\Service\PlantsFromZoneRetriever;
use App\Service\PlantsInfoRetriever;
use App\Service\ZipService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class MapController extends AbstractController
{
    /**
     * @Route("/api/map", name="api_map_save")
     */
    public function saveZip(Request $request, PlantsFromZoneRetriever $retriever, EntityManagerInterface $em): JsonResponse
    {
        $form = $request->request->all();

        $lat = $form['lat'];
        $lng = $form['lng'];
        $image = $form['jpg'];

        try {
            $plants = $retriever->getPlants($lat, $lng);
        } catch (ClientExceptionInterface |
        TransportExceptionInterface |
        ServerExceptionInterface |
        RedirectionExceptionInterface |
        DecodingExceptionInterface $e) {
            $plants = [];
        }

        $plantNorm = new PlantNormalizer();
        $encoder = new JsonEncoder();
        $serializer = new Serializer([$plantNorm], [$encoder]);
        $json = $serializer->serialize($plants,  'json', ['json_encode_options' => \JSON_PRETTY_PRINT]);

        $zipDir = $this->getParameter('app.zip_directory');

        $terrain = new Terrain($this->getUser());
        $fileName = $terrain->getZipName();

        $em->persist($terrain);
        $em->flush();

        $base64 = base64_encode($json);
        $zipService = new ZipService($zipDir, $fileName);
        $zipService->addFiles([
            'json' => $base64,
            'jpg' => $image
        ]);

        return new JsonResponse([
            'status' => "success",
            'zip' => "saved"
        ]);
    }

    /**
     * @Route("/api/map/key/{id}", name="api_key_save")
     * @param int $id
     * @param EntityManagerInterface $em
     * @param TerrainRepository $repository
     * @return JsonResponse
     */
    public function addKey(int $id, EntityManagerInterface $em, TerrainRepository $repository): JsonResponse
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

        return new JsonResponse([
            'status' => "success",
            'key' => "saved"
        ]);
    }

    /**
     * @Route("/api/zip/{id}", name="api_get_zip")
     * @param string $id
     * @param TerrainKeyRepository $repository
     * @return BinaryFileResponse|JsonResponse
     */
    public function getZip(string $id, TerrainKeyRepository $repository)
    {
        $terrainKey = $repository->find($id);

        if ($terrainKey->getExpiringOn() < new \DateTime() || $terrainKey->getTerrain()->getUser() !== $this->getUser()) {
            return new JsonResponse([
                'status' => "error",
                'key' => "expired"
            ]);
        }

        $finder = new Finder();
        $zipDir = $this->getParameter('app.zip_directory');

        $finder->files()->in($zipDir);

        foreach ($finder as $file) {
            $fileName = $file->getFilename();

            if ($fileName == $terrainKey->getTerrain()->getZipName()) {
                $response = new BinaryFileResponse($file);
                $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

                return $response;
            }
        }

        return new JsonResponse([
            'status' => "error",
        ]);
    }
}