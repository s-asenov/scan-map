<?php


namespace App\Controller;


use App\Entity\DistributionZone;
use App\Entity\DistributionZonePlant;
use App\Entity\Plant;
use App\Entity\Terrain;
use App\Entity\TerrainKey;
use App\Entity\User;
use App\Repository\TerrainKeyRepository;
use App\Service\ImageUploader;
use App\Service\PlantsFromZoneRetriever;
use App\Service\PlantsInfoRetriever;
use App\Service\ZipSaver;
use App\Service\ZipService;
use App\Util\FormHelper;
use App\Util\UploadedBase64File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class MapController extends AbstractController
{
    private $em;
    private $formHelper;

    public function __construct(EntityManagerInterface $em, FormHelper $formHelper)
    {
        $this->em = $em;
        $this->formHelper = $formHelper;
    }

    /**
     * @Route("/api/map", name="api_map_save", methods={"POST"})
     * @param PlantsInfoRetriever $infoRetriever
     * @param ImageUploader $uploader
     * @param Request $request
     * @param PlantsFromZoneRetriever $retriever
     * @return JsonResponse
     */
    public function saveZip(PlantsInfoRetriever $infoRetriever, ImageUploader $uploader, Request $request, PlantsFromZoneRetriever $retriever): JsonResponse
    {
        $form = $request->request->all();

        if (!$this->formHelper->checkFormData(['name', 'lat', 'lng', 'images' => ['elevation', 'gmImage']], $form)) {
            return new JsonResponse(["status" => FormHelper::MISSING_CREDENTIALS], 400);
        }

        $lat = $form['lat'];
        $lng = $form['lng'];
        $elevationModel = $form['images']['elevation'];
        $gmImage = $form['images']['gmImage'];

        $name = $form['name'];

        if (!$this->formHelper->base64validate($elevationModel) || !$this->formHelper->base64validate($gmImage)) {
            return new JsonResponse([
                'status' => FormHelper::META_ERROR,
                'meta' => FormHelper::META_INVALID
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $zone = $retriever->getDistributionZone($lat, $lng);

            $plants = array_values($retriever->getPlants($zone));

            if (!$zone->getFetched()) {
                // The Zone is no longer watched from the entity manager so we need to access it again by finding it.
                $saveZone = $this->em->getRepository(DistributionZone::class)->find($zone->getId());
                $saveZone->setFetched(true);
                $this->em->persist($saveZone);
            }
        } catch (ClientExceptionInterface |
        TransportExceptionInterface |
        ServerExceptionInterface |
        RedirectionExceptionInterface |
        DecodingExceptionInterface $e) {
            $plants = [];
        }

        /**
         * Manually persist the current user because, EM is not watching it for some reason and it needs to.
         * Probable cause - clearing the entity manager in the PlantFromZoneRetriever service.
         * @see PlantsFromZoneRetriever::getPlants()
         *
         * @var User $user
         */
        $user = $this->em->getRepository(User::class)->find($this->getUser()->getId());

        $terrain = new Terrain($user);
        $terrain->setName($name);
        $fileName = $terrain->getZipName();

        $file = new UploadedBase64File($gmImage, $fileName);
        $uploadedFileName = $uploader->upload($file);

        $terrain->setImageDirectory($uploadedFileName);

        $this->em->persist($terrain);
        $this->em->flush();

        $zipDir = $this->getParameter('app.zip_directory');
        $zipSaver = new ZipSaver($zipDir, $fileName, $elevationModel);
        $zipSaver->saveZip($plants);

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'zip' => "saved"
        ]);
    }


    /**
     * @Route("/zip/{id}", name="get_zip", methods={"GET"})
     * @param TerrainKey $terrainKey
     * @param ZipService $zipService
     * @return BinaryFileResponse|JsonResponse
     */
    public function getZip(TerrainKey $terrainKey, ZipService $zipService)
    {
        if ($terrainKey->getExpiringOn() < new \DateTime()) {
            return new JsonResponse([
                'status' => FormHelper::META_ERROR,
                'key' => "expired"
            ]);
        }

        $zipDir = $this->getParameter('app.zip_directory');
        $file = $zipService->getZip($zipDir, $terrainKey);

        if ($file) {
            $response = new BinaryFileResponse($file);
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

            return $response;
        }

        return new JsonResponse([
            'status' => FormHelper::META_ERROR,
            'file' => "not found"
        ]);
    }
}