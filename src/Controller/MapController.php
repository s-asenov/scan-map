<?php


namespace App\Controller;


use App\Entity\Terrain;
use App\Repository\TerrainKeyRepository;
use App\Serializer\Normalizer\PlantNormalizer;
use App\Service\ImageUploader;
use App\Service\PlantsFromZoneRetriever;
use App\Service\ZipService;
use App\Util\UploadedBase64File;
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
    public function saveZip(ImageUploader $uploader, Request $request, PlantsFromZoneRetriever $retriever, EntityManagerInterface $em): JsonResponse
    {
        $form = $request->request->all();

        $lat = $form['lat'];
        $lng = $form['lng'];
        $elevationModel = $form['images']['elevation'];
        $gmImage = $form['images']['gmImage'];

        $name = $form['name'];

        try {
            $plants = array_values($retriever->getPlants($lat, $lng));
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
        $terrain->setName($name);
        $fileName = $terrain->getZipName();


        $file = new UploadedBase64File($gmImage, $fileName);
        $uploadedFileName = $uploader->upload($file);

        $terrain->setImageDirectory($uploadedFileName);

        $em->persist($terrain);
        $em->flush();

        $base64 = base64_encode($json);
        $zipService = new ZipService($zipDir, $fileName);
        $zipService->addFiles([
            'json' => $base64,
            'jpg' => $elevationModel
        ]);

        return new JsonResponse([
            'status' => "success",
            'zip' => "saved"
        ]);
    }


    /**
     * @Route("/zip/{id}", name="get_zip")
     * @param string $id
     * @param TerrainKeyRepository $repository
     * @return BinaryFileResponse|JsonResponse
     */
    public function getZip(string $id, TerrainKeyRepository $repository)
    {
        $terrainKey = $repository->find($id);

        if ($terrainKey->getExpiringOn() < new \DateTime()) {
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

            if ($fileName == $terrainKey->getTerrain()->getZipName().".zip") {
                $response = new BinaryFileResponse($file);
                $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

                return $response;
            }
        }

        return new JsonResponse([
            'status' => "error",
            'file' => "not found"
        ]);
    }
}