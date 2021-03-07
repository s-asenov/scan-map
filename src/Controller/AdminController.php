<?php


namespace App\Controller;


use App\Entity\Plant;
use App\Util\FormHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/admin", name="admin_")
 *
 * Class AdminController
 * @package App\Controller
 */
class AdminController extends AbstractController
{
    private $formHelper;
    private $em;

    public function __construct(FormHelper $formHelper, EntityManagerInterface $em)
    {
        $this->formHelper = $formHelper;
        $this->em = $em;
    }

    /**
     * @Route("/models/{id}", name="add_model", methods={"POST"})
     *
     * @param Plant $plant
     * @param Request $request
     * @param Filesystem $filesystem
     * @return JsonResponse
     */
    public function addModel(Plant $plant, Request $request, Filesystem $filesystem): JsonResponse
    {
        $form = $request->request->all();

        if (!$this->formHelper->checkFormData(['model', 'modelName'], $form)) {
            return new JsonResponse([
                'status' => FormHelper::META_ERROR,
                'meta' => FormHelper::MISSING_CREDENTIALS
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!$this->formHelper->base64validate($form['model']) || !preg_match("/\.(gif|png|jpg)$/", $form['model'])) {
            return new JsonResponse([
                'status' => FormHelper::META_ERROR,
                'meta' => FormHelper::META_INVALID
            ], Response::HTTP_BAD_REQUEST);
        }

        $modelsDir = $this->getParameter('app.models_directory');
        $modelPath = $modelsDir.$form['modelName'];

        $filesystem->appendToFile($modelPath, $form['model']);

        $plant->setModelPath($modelPath);

        $this->em->persist($plant);
        $this->em->flush();

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'meta' => "Added!"
        ]);
    }

    /**
     * @Route("/models/{id}", name="delete_model", methods={"DELETE"})
     *
     * @param Plant $plant
     * @param Filesystem $filesystem
     * @return JsonResponse
     */
    public function deleteModel(Plant $plant, Filesystem $filesystem): JsonResponse
    {
        $modelPath = $plant->getModelPath();

        if ($modelPath === null) {
            return new JsonResponse([
                'status' => FormHelper::META_ERROR,
                'meta' => "Plant has no model!"
            ], Response::HTTP_BAD_REQUEST);
        }

        $filesystem->remove($modelPath);

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'meta' => FormHelper::META_DELETED
        ]);
    }
}