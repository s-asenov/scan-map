<?php


namespace App\Service\Entity;


use App\Entity\User;
use App\Serializer\Normalizer\UserNormalizer;
use App\Service\EmailVerifier;
use App\Util\ApiRequest;
use App\Util\FormHelper;
use App\Util\MyHelper;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class UserService
{
    public function __construct(
        private ApiRequest $request,
        private MyHelper $myHelper,
        private UserPasswordEncoderInterface $passwordEncoder,
        private EntityManagerInterface $em,
        private EmailVerifier $emailVerifier,
        private UserNormalizer $userNormalizer
    ) { }

    /**
     * The method contains all the logic behind
     * the user registration and sending the email verification.
     *
     * @return JsonResponse
     * @throws TransportExceptionInterface
     */
    public function register(): JsonResponse
    {
        $constraint = new Assert\Collection([
            'email' => [
                new Assert\NotBlank(null, "Email should not be blank."),
                new Assert\Email(null, "{{ value }} is not a valid email address.")
            ],
            'lastName' => [
                new Assert\NotBlank(null, "Last name should not be blank."),
                new Assert\Length(['min'=>2])
            ],
            'firstName' => [
                new Assert\NotBlank(null, "First name should not be blank."),
                new Assert\Length(['min'=>2])
            ],
            'password' => [
                new Assert\NotBlank(null, "Password should not be blank."),
                new Assert\Length(['min'=> 6])
            ]
        ]);

        $errors = $this->request->validate($constraint);
        
        if ($errors) {
            return $errors;
        }
        
        $data = $this->request->toArray();

        $user = new User();
        $user->setFirstName($data["firstName"])
            ->setLastName($data["lastName"])
            ->setEmail($data["email"])
            ->setPassword($data["password"])
            ->setApiToken($this->myHelper->randomStr(255));

        $user->setPassword($this->passwordEncoder->encodePassword($user, $data["password"]));

        $this->em->persist($user);
        $this->em->flush();

        $this->emailVerifier->sendEmail($user);

        $userArray = $this->userNormalizer->normalize($user);

        setcookie('x-token', $user->getApiToken(), 0, '/', null, true, true);

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'user' => $userArray
        ]);
    }

    /**
     * The method calls the verification service, which sends the email.
     *
     * @param User $user
     * @return JsonResponse
     * @throws TransportExceptionInterface
     */
    public function sendVerificationEmail(User $user): JsonResponse
    {
        $this->emailVerifier->sendEmail($user);

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS
        ]);
    }

    /**
     * The method is responsible for the email verification via the current request.
     *
     * @param User $user
     * @throws VerifyEmailExceptionInterface
     */
    public function verifyEmail(User $user)
    {
        $this->emailVerifier->verifyEmail($user, $this->request->getUri());

        $user->setRoles(['VERIFIED']);

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * The method is responsible for updating the lastSeen field
     * of the successful logged in user.
     *
     * @param User $user the user returned by the guard
     * @return JsonResponse
     */
    public function login(User $user): JsonResponse
    {
        $user->setLastSeen(new DateTime());

        $this->em->persist($user);
        $this->em->flush();

        $userArray = $this->userNormalizer->normalize($user);

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'user' => $userArray
        ]);
    }

    /**
     * The method is responsible for logging out the user by
     * clearing the cookie.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $response =  new JsonResponse([
            'status' => FormHelper::META_SUCCESS
        ]);

        if ($request->cookies->has('x-token')) {
            $response->headers->clearCookie('x-token');
        }

        return $response;
    }

    /**
     * The method is responsible for getting the current user as an array.
     *
     * @param User $user the already logged in user
     * @return JsonResponse
     */
    public function getCurrentUser(User $user): JsonResponse
    {
        $userArray = $this->userNormalizer->normalize($user);

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'user' => $userArray
        ]);
    }
}