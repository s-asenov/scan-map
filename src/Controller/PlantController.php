<?php


namespace App\Controller;


use App\Entity\Plant;
use App\Service\Entity\PlantService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PlantController extends AbstractController
{
    public function __construct(
        private PlantService $plantService
    ) { }

    #[Route("/plant/{id}", name: "plant_name", methods: ["GET"])]
    public function getPlantInfo(Plant $plant): JsonResponse
    {
        return $this->plantService->getPlantInfo($plant);
    }

    #[Route("/api/plants/find", name: "plant_find", methods:["POST"])]
    public function findPlants(): JsonResponse
    {
        return $this->plantService->findPlantByInput();
    }
}