<?php


namespace App\Service\Entity;


use App\Entity\Plant;
use App\Repository\PlantRepository;
use App\Serializer\Normalizer\PlantNormalizer;
use App\Service\Retriever\PlantsInfoRetriever;
use App\Util\ApiRequest;
use App\Util\FormHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PlantService
{
    public function __construct(
        private ApiRequest $request,
        private PlantNormalizer $normalizer,
        private PlantsInfoRetriever $infoRetriever,
        private EntityManagerInterface $em,
        private PlantRepository $plantRepository
    ) { }

    /**
     * The method calls the retriever which gets the information about the plants.
     *
     * @param Plant $plant
     * @return JsonResponse
     */
    public function getPlantInfo(Plant $plant): JsonResponse
    {
        if ($plant->getDescription() !== null) {
            $info = $plant->getDescription();
        } else {
            try {
                $info = $this->infoRetriever->getInfoOfPlant($plant);
            } catch (ClientExceptionInterface |
            TransportExceptionInterface |
            ServerExceptionInterface |
            RedirectionExceptionInterface |
            DecodingExceptionInterface $e) {
                $info = "";
            }
        }

        $plant->setDescription($info);

        $this->em->persist($plant);
        $this->em->flush();

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'info' => $info
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function findPlantByInput(): JsonResponse
    {
        $data = $this->request->toArray();

        $input = $data['input'];

        $plants = $this->plantRepository->findByInput($input);

        $normalized = [];

        foreach ($plants as $plant) {
            $normalized[] = $this->normalizer->normalize($plant);
        }

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'plants' => $normalized
        ]);
    }
}