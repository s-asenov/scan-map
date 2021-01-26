<?php


namespace App\Controller;


use App\Util\FormHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 *
 * Class AdminController
 * @package App\Controller
 */
class AdminController extends AbstractController
{
    private $formHelper;

    public function __construct(FormHelper $formHelper)
    {
        $this->formHelper = $formHelper;
    }

    /**
     * @Route("/add", name="add_model", methods={"POST"})
     *
     * @param Request $request
     * @param Filesystem $filesystem
     * @return JsonResponse
     */
    public function addModel(Request $request, Filesystem $filesystem): JsonResponse
    {
        $form = $request->request->all();

        if (!$this->formHelper->checkFormData(['model', 'modelName', 'plantName'], $form)) {
            return new JsonResponse([
                'status' => FormHelper::META_ERROR,
                'meta' => FormHelper::MISSING_CREDENTIALS
            ], Response::HTTP_BAD_REQUEST);
        }

        $modelsDir = $this->getParameter('app.models_directory');

        if (!$this->formHelper->base64validate($form['model'])) {
            return new JsonResponse([
                'status' => FormHelper::META_ERROR,
                'meta' => FormHelper::META_INVALID
            ], Response::HTTP_BAD_REQUEST);
        }

        $filesystem->appendToFile($modelsDir.$form['modelName'], $form['model']);

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
        ]);
    }
}