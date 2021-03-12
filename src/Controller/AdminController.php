<?php


namespace App\Controller;


use App\Entity\Plant;
use App\Service\Entity\ModelsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/admin", name="admin_")
 *
 * Class AdminController
 * @package App\Controller
 */
class AdminController extends AbstractController
{
    public function __construct(private ModelsService $modelsService)
    { }

    /**
     * The method validates and saves the 3D model file
     * for the corresponding plant.
     *
     * @param Plant $plant
     * @return JsonResponse
     */
    #[Route("/models/{id}", name:"add_model", methods:["POST"])]
    public function addModel(Plant $plant): JsonResponse
    {
        return $this->modelsService->addPlantModel($plant);
    }

    /**
     * The method deletes the 3D model to the corresponding plant.
     *
     * @param Plant $plant
     * @return JsonResponse
     */
    #[Route("/models/{id}", name:"delete_model", methods:["DELETE"])]
    public function deleteModel(Plant $plant): JsonResponse
    {
        return $this->modelsService->deletePlantModel($plant);
    }
}