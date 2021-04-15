<?php


namespace App\Controller;

use App\Serializer\Normalizer\UserNormalizer;
use App\Service\EmailVerifier;
use App\Service\Entity\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;

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
    private $mailer;
    private $emailVerifier;

    public function __construct(private UserService $userService, EmailVerifier $emailVerifier, MailerInterface $mailer, UserNormalizer $normalizer, EntityManagerInterface $em)
    {
        $this->normalizer = $normalizer;
        $this->em = $em;
        $this->mailer = $mailer;
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * The method calls the user service, which is creating the new user
     * with the corresponding data.
     *
     * @return JsonResponse
     */
    #[Route("/register", name: "register", methods: ["POST"])]
    public function register(): JsonResponse
    {
        return $this->userService->register();
    }

    /**
     * The method is used when creating a new verification email.
     *
     * @return JsonResponse
     */
   #[Route("/send/verify", name: "verification_send", methods:["POST", "GET"])]
    public function sendVerificationEmail(): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->userService->sendVerificationEmail($this->getUser());
    }

    /**
     * The method is responsible for the verification of the email.
     *
     * @return RedirectResponse
     */
    #[Route("/verify", name:"registration_confirmation_route", methods: ["GET"])]
    public function verifyUserEmail(Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $this->userService->verifyEmail($this->getUser(), $request);

        return $this->redirectToRoute('app_homepage');
    }

    /**
     * The method is responsible for updating the logged in user.
     *
     * @return JsonResponse
     */
    #[Route("/login", name: "login", methods: ["POST"])]
    public function login(): JsonResponse
    {
        return $this->userService->login($this->getUser());
    }

    /**
     * The method is responsible for logging out the user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route("/logout", name: "logout", methods:["GET"])]
    public function logout(Request $request): JsonResponse
    {
        return $this->userService->logout($request);
    }

    /**
     * The method is responsible for getting the current user.
     *
     * @return JsonResponse
     */
    #[Route("/user", name: "user", methods: ["GET"])]
    public function getCurrentUser(): JsonResponse
    {
        return $this->userService->getCurrentUser($this->getUser());
    }
}