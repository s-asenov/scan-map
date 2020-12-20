<?php


namespace App\Controller;


use App\Entity\User;
use App\Util\FormHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api", name="api_")\
 *
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     *
     * @param Request $request
     * @param FormHelper $formHelper
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function register(Request $request, FormHelper $formHelper, ValidatorInterface $validator)
    {
        $form = $request->request->all();

        if (!$formHelper->checkFormData(['first_name', 'last_name', 'email', 'password'], $request->request->all())) {
            return new JsonResponse($formHelper::MISSING_CREDENTIALS);
        }

        $user = new User();
        $user->setFirstName($form["first_name"])
            ->setLastName($form["last_name"])
            ->setEmail($form["email"])
            ->setPassword($form["password"])
            ->setLastSeen(new \DateTime());

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $messages = [];

            foreach ($errors as $error) {
                $messages[$error->getPropertyPath()] = $error->getMessage();
            }

            dd($messages);
        }
    }

    /**
     * @Route("/login", name="test")
     */
    public function login(Request $request)
    {
        dd($this->getParameter("app.env"));
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     * @throws \Exception
     */
    public function logout()
    {
        throw new \Exception('This should not be accessed!');
    }
}