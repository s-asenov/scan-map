<?php


namespace App\Controller;


use App\Entity\DistributionZone;
use App\Entity\User;
use App\Serializer\Normalizer\UserNormalizer;
use App\Util\FormHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api", name="api_")\
 *
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    private $normalizer;
    private $em;

    public function __construct(UserNormalizer $normalizer, EntityManagerInterface $em)
    {
        $this->normalizer = $normalizer;
        $this->em = $em;
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, FormHelper $formHelper, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator)
    {
        $form = $request->request->all();

        if (!$formHelper->checkFormData(['firstName', 'lastName', 'email', 'password'], $request->request->all())) {
            return new JsonResponse($formHelper::MISSING_CREDENTIALS);
        }

        $user = new User();
        $user->setFirstName($form["firstName"])
            ->setLastName($form["lastName"])
            ->setEmail($form["email"])
            ->setPassword($form["password"]);

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $messages = [];

            foreach ($errors as $error) {
                $messages[$error->getPropertyPath()] = $error->getMessage();
            }

            return new JsonResponse($messages, 400);
        }

        $user->setPassword($passwordEncoder->encodePassword($user, $form["password"]));
        
        $this->em->persist($user);
        $this->em->flush();

        $userArray = $this->normalizer->normalize($user);
        $success = [
            'status' => "success",
            'user' => $userArray
        ];

        return new JsonResponse($success, 200);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login()
    {
        $user = $this->getUser();
        $user->setLastSeen(new \DateTime());

        $this->em->persist($user);
        $this->em->flush();

        $userArray = $this->normalizer->normalize($this->getUser());
        $success = [
            'status' => "success",
            'user' => $userArray
        ];

        return new JsonResponse($success, 200);
    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     */
    public function logout()
    {
        throw new \Exception('This should not be accessed!');
    }

    /**
     * @Route("/user", name="user")
     */
    public function getCurrentUser()
    {
        $userArray = $this->normalizer->normalize($this->getUser());
        $success = [
            'status' => "success",
            'user' => $userArray
        ];

        return new JsonResponse($success, 200);
    }


}