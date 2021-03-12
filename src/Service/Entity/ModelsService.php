<?php


namespace App\Service\Entity;


use App\Entity\Plant;
use App\Util\ApiRequest;
use App\Util\FormHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as MyAssert;

class ModelsService
{
    public function __construct(
        private string $targetDirectory,
        private ApiRequest $request,
        private Filesystem $filesystem,
        private EntityManagerInterface $em
    ) { }

    /**
     * The method validates and saves the 3D model file
     * for the corresponding plant.
     *
     * @param Plant $plant
     * @return JsonResponse
     */
    public function addPlantModel(Plant $plant): JsonResponse
    {
        $constraint = new Assert\Collection([
            'model' => [
                new Assert\NotBlank(),
                new MyAssert\Base64Constraint()
            ],
            'modelName' =>  new Assert\NotBlank(),
        ]);

        $errors = $this->request->validate($constraint);

        if ($errors) {
            return $errors;
        }

        $data = $this->request->toArray();

        $modelPath = $this->targetDirectory.$data['modelName'];

        if ($plant->getModelPath() !== null) {
            $this->filesystem->remove($this->targetDirectory.$plant->getModelPath());
        }

        $this->filesystem->appendToFile($modelPath, base64_decode($data['model']));

        $plant->setModelPath($data['modelName']);

        $this->em->persist($plant);
        $this->em->flush();

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'meta' => "Added!"
        ]);
    }

    /**
     * The method deletes the 3D model to the corresponding plant.
     *
     * @param Plant $plant
     * @return JsonResponse
     */
    public function deletePlantModel(Plant $plant): JsonResponse
    {
        $modelPath = $this->targetDirectory.$plant->getModelPath();
       ;
        if ($plant->getModelPath() === null) {
            return new JsonResponse([
                'status' => FormHelper::META_ERROR,
                'meta' => "Plant has no model!"
            ], Response::HTTP_BAD_REQUEST);
        }

        $plant->setModelPath(null);

        $this->em->persist($plant);
        $this->em->flush();

        $this->filesystem->remove($modelPath);

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'meta' => FormHelper::META_DELETED
        ]);
    }
}