<?php

namespace App\Security;

use App\Util\FormHelper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class LoginAuthenticator extends AbstractGuardAuthenticator
{
    public function __construct(private UserPasswordEncoderInterface $passwordEncoder)
    {
    }

    public function supports(Request $request): bool
    {
        $this->hasCookie = $request->cookies->has("x-token");

        return $request->attributes->get('_route') === "api_login" && !$request->cookies->has("x-token");
    }

    public function getCredentials(Request $request): array
    {
        $form = $request->toArray();
        
        return [
            'email' => $form['email'],
            'password' => $form['password'] 
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        $user = $userProvider->loadUserByUsername($credentials['email']);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException('Email could not be found.');
        }

        $this->token = $user->getApiToken();

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse([
            'status' => FormHelper::META_ERROR,
            'meta' =>'Login failed!'
        ], Response::HTTP_BAD_REQUEST);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        setcookie('x-token', $this->token, 0, '/', null, true, true);

        return null;
    }

    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        if ($request->cookies->has('x-token')) {
            return new JsonResponse([
                'status' => FormHelper::META_ERROR,
                'meta' =>'User already logged in!'
            ], Response::HTTP_UNAUTHORIZED);
        } else {
            return new JsonResponse([
                'status' => FormHelper::META_ERROR,
                'meta' =>'Login failed!'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }
}
