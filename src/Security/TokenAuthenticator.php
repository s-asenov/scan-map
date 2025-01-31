<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    public function __construct(private UserRepository $ur)
    {
    }

    /**
     * @inheritDoc
     *
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request): bool
    {
//        return $request->headers->has("Authorization");
        $currentRoute = $request->attributes->get("_route");
        dd($currentRoute);
        //todo old
        return $request->cookies->has('x-token');
//            && $request->headers->has('Application');
    }

    /**
     * @inheritDoc
     *
     * @param Request $request
     * @return array
     */
    public function getCredentials(Request $request): array
    {
//        $request->headers->get('Authorization');
        return [
//            'Application' => $request->headers->get('Application'),
            'AUTH-TOKEN' => $request->cookies->get('x-token')
        ];
    }

    /**
     * @inheritDoc
     *
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return User|UserInterface|null
     */
    public function getUser(mixed $credentials, UserProviderInterface $userProvider): UserInterface|User|null
    {
//        $user = $userProvider->loadUserByUsername($credentials['AUTH-TOKEN']);

        $user = $this->ur->findOneBy(['apiToken' => $credentials['AUTH-TOKEN']]);

        if (!$user) {
            throw new AuthenticationException('User not found');
        }

        return $user;
    }

    /**
     * @inheritDoc
     *
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
//       if ($credentials['Application'] !== $_ENV['APP_SECRET']) {
//           return false;
//       }

        return true;
    }

    /**
     * @inheritDoc
     *
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        $response = new JsonResponse([
                'error' => $exception->getMessage()
        ], Response::HTTP_UNAUTHORIZED);

        $response->headers->clearCookie('x-token');

        return $response;
    }

    /**
     * @inheritDoc
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return JsonResponse|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): ?JsonResponse
    {
        $currentRoute = $request->attributes->get("_route");

        //list of routes where email verification is not needed
        $routes = ["api_registration_confirmation_route", "api_verification_send", "api_registration_confirmation_route", "api_user", "api_logout"];

        if (!$token->getUser()->getIsVerified() && !in_array($currentRoute, $routes)) {
            return new JsonResponse([
                'email' => "Not verified"
            ], Response::HTTP_FORBIDDEN);
        }

        return null;
    }

    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        $data = [
            'error' => "Unauthorized request!"
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }
}
