<?php


namespace App\Service\Entity;


use App\Entity\DistributionZone;
use App\Entity\Terrain;
use App\Entity\TerrainKey;
use App\Entity\User;
use App\Repository\TerrainRepository;
use App\Serializer\Normalizer\TerrainNormalizer;
use App\Service\ImageUploader;
use App\Service\Retriever\PlantsFromZoneRetriever;
use App\Service\ZipSaver;
use App\Service\ZipService;
use App\Util\ApiRequest;
use App\Util\FormHelper;
use App\Util\UploadedBase64File;
use App\Validator\Constraints as MyAssert;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class TerrainService
{
    public function __construct(
        private ApiRequest $request,
        private EntityManagerInterface $em,
        private PlantsFromZoneRetriever $plantsFromZoneRetriever,
        private ImageUploader $uploader,
        private TerrainRepository $terrainRepository,
        private TerrainNormalizer $terrainNormalizer,
        private string $zipDirectory,
        private HttpClientInterface $floraApi
    ) { }

    public function syncTables($zoneId)
    {
        $this->plantsFromZoneRetriever->syncTables($zoneId);

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'meta' => "sync"
        ]);
    }

    /**
     * The method is responsible for creating the terrain entity
     * and saving the ZipArchive with the necessary data.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function createTerrain(User $user): JsonResponse
    {
        $constraint = new Assert\Collection([
            'name' => new Assert\NotBlank(),
            'lat' => new Assert\NotBlank(),
            'lng' => new Assert\NotBlank(),
            'images' => new Assert\Collection([
                'elevation' => new MyAssert\Base64Constraint(),
                'gmImage' => new MyAssert\Base64Constraint()
            ])
        ]);

        $errors = $this->request->validate($constraint);

        if ($errors) {
            return $errors;
        }

        $data = $this->request->toArray();

        $lat = $data['lat'];
        $lng = $data['lng'];
        $elevationModel = $data['images']['elevation'];
        $gmImage = $data['images']['gmImage'];
        $name = $data['name'];

        $zone = $this->plantsFromZoneRetriever->getDistributionZone($lat, $lng);

        try {
            //remove the keys of the array, for now
            $plants = array_values($this->plantsFromZoneRetriever->getPlants($zone));

            /**
             * The Zone is no longer watched from the entity manager so we need to access it again by finding it.
             * @var $saveZone DistributionZone
             */
            if ($zone) {
                $saveZone = $this->em->getRepository(DistributionZone::class)->find($zone->getId());
                $saveZone->setFullyFetched(true);
                $saveZone->incrementFetched();
                $this->em->persist($saveZone);
                $this->em->flush($saveZone);
            }
        } catch (ClientExceptionInterface |
        TransportExceptionInterface |
        ServerExceptionInterface |
        RedirectionExceptionInterface |
        DecodingExceptionInterface $e) {
            $saveZone = $this->em->getRepository(DistributionZone::class)->find($zone->getId());

            $plants = $saveZone->getAllPlants();

            uksort($plants, [PlantsFromZoneRetriever::class, 'comparePlantNames']);
        }

        $this->em->clear();

        /**
         * Manually persist the current user because, EM is not watching it for some reason and it needs to.
         * Probable cause - clearing the entity manager in the PlantFromZoneRetriever service.
         * @see PlantsFromZoneRetriever::getPlants()
         *
         * @var User $user
         */
        $user = $this->em->getRepository(User::class)->find($user->getId());

        $terrain = new Terrain($user);
        $terrain->setName($name);
        $fileName = $terrain->getZipName();

        $file = new UploadedBase64File($gmImage, $fileName);
        $uploadedFileName = $this->uploader->upload($file);

        $terrain->setImageDirectory($uploadedFileName);

        $zipSaver = new ZipSaver($this->zipDirectory, $fileName, $elevationModel);
        $zipSaver->saveZip($plants);

        $this->em->persist($terrain);
        $this->em->flush();

//        $url = $this->request->getSchemeAndHttpHost();

        if ($zone) {
            $this->floraApi->request('POST', 'api/sync/'.$zone->getId());
        }


        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'zip' => "saved"
        ]);
    }

    /**
     * Check the validity of the terrain key and return the zip file,
     * if it is valid.
     *
     * @param TerrainKey $terrainKey
     * @param ZipService $zipService
     * @return BinaryFileResponse|JsonResponse
     */
    public function getTerrainZipFile(TerrainKey $terrainKey, ZipService $zipService): BinaryFileResponse|JsonResponse
    {
        if ($terrainKey->getExpiringOn() < new DateTime()) {
            return new JsonResponse([
                'status' => FormHelper::META_ERROR,
                'key' => "expired"
            ], Response::HTTP_BAD_REQUEST);
        }

        $file = $zipService->getZip($this->zipDirectory, $terrainKey);

        if ($file) {
            $response = new BinaryFileResponse($file);
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
            $response->headers->set('Content-Type', 'application/zip');

            return $response;
        }

        return new JsonResponse([
            'status' => FormHelper::META_ERROR,
            'file' => "not found"
        ], Response::HTTP_NOT_FOUND);
    }

    /**
     * The method returns all the user terrains.
     *
     * @param User $user
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function getUserTerrains(User $user): JsonResponse
    {
        $terrains = $this->terrainRepository->findBy([
            'user' => $user->getId()
        ]);

        $terrainsArray = [];

        foreach ($terrains as $terrain) {
            $normalized = $this->terrainNormalizer->normalize($terrain);

            $terrainsArray[] = $normalized;
        }

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'terrains' => $terrainsArray
        ]);
    }

    /**
     * The method is responsible for getting the desired user terrain.
     *
     * @param Terrain $terrain
     * @param User $user
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function getUserTerrain(Terrain $terrain, User $user): JsonResponse
    {
        if (!$terrain || $terrain->getUser() !== $user) {
            return new JsonResponse([
                'status' => FormHelper::META_ERROR,
                "meta" => FormHelper::UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        $normalized = $this->terrainNormalizer->normalize($terrain);

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'terrains' => $normalized
        ]);
    }

    /**
     * The method is responsible for deleting the user terrain.
     *
     * @param Terrain $terrain
     * @param User $user
     * @param string $zipDir
     * @return JsonResponse
     */
    public function deleteUserTerrain(Terrain $terrain, User $user, string $zipDir): JsonResponse
    {
        if ($terrain->getUser() !== $user) {
            return new JsonResponse([
                'status' => FormHelper::META_ERROR,
                'meta' => FormHelper::UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        $zipFilePath = $zipDir . $terrain->getZipName() . ".zip";
        $imageFilePath = $zipDir . $terrain->getImageDirectory();

        $filesystem = new Filesystem();
        $filesystem->remove([$zipFilePath, $imageFilePath]);

        $this->em->remove($terrain);
        $this->em->flush();

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'terrain' => FormHelper::META_DELETED
        ]);
    }
}