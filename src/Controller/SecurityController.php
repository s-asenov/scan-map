<?php


namespace App\Controller;

use App\Entity\User;
use App\Serializer\Normalizer\UserNormalizer;
use App\Service\EmailVerifier;
use App\Util\FormHelper;
use App\Util\MyHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

/**
 * @Route("/api", name="api_")
 *
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController extends MyController
{
    private $normalizer;
    private $em;
    private $verifyEmailHelper;
    private $mailer;
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier, VerifyEmailHelperInterface $helper, MailerInterface $mailer, UserNormalizer $normalizer, EntityManagerInterface $em)
    {
        $this->normalizer = $normalizer;
        $this->em = $em;
        $this->verifyEmailHelper = $helper;
        $this->mailer = $mailer;
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(MyHelper $myHelper, Request $request, FormHelper $formHelper, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator): JsonResponse
    {
        $form = $request->request->all();

        if (!$formHelper->checkFormData(['firstName', 'lastName', 'email', 'password'], $form)) {
            return new JsonResponse(["status" => $formHelper::MISSING_CREDENTIALS], Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setFirstName($form["firstName"])
            ->setLastName($form["lastName"])
            ->setEmail($form["email"])
            ->setPassword($form["password"])
            ->setApiToken($myHelper->random_str(255));

        $errors = $formHelper->validate($validator, $user);

        if (is_array($errors)) {
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
        }

        $user->setPassword($passwordEncoder->encodePassword($user, $form["password"]));
        
        $this->em->persist($user);
        $this->em->flush();

        $this->emailVerifier->sendEmail($user);

        $userArray = $this->normalizer->normalize($user);

        setcookie('x-token', $user->getApiToken(), 0, '/', null, true, true);

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'user' => $userArray
        ]);
    }

    /**
     * @Route("/send/verify", name="verification_send", methods={"POST"})
     */
    public function sendVerificationEmail(): JsonResponse
    {
        $this->emailVerifier->sendEmail($this->getUser());

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS
        ]);
    }

    /**
     * @Route("/verify", name="registration_confirmation_route", methods={"GET"})
     * @param Request $request
     * @return RedirectResponse
     */
    public function verifyUserEmail(Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $this->emailVerifier->verifyEmail($user, $request->getUri());

        $user->setRoles(['VERIFIED']);

        $this->em->persist($user);
        $this->em->flush();

        return $this->redirectToRoute('app_homepage');
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(): JsonResponse
    {
        $user = $this->getUser();
        $user->setLastSeen(new \DateTime());

        $this->em->persist($user);
        $this->em->flush();

        $userArray = $this->normalizer->normalize($this->getUser());

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'user' => $userArray
        ]);
    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     */
    public function logout(): JsonResponse
    {
        if (isset($_COOKIE['x-token'])) {
            unset($_COOKIE['x-token']); 
            setcookie('x-token', null, -1, '/'); 
        }

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS
        ]);
    }

    /**
     * @Route("/user", name="user", methods={"GET"})
     */
    public function getCurrentUser(): JsonResponse
    {
        $userArray = $this->normalizer->normalize($this->getUser());

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'user' => $userArray
        ], 200);
    }
}