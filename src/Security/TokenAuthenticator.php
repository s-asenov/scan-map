<?php

namespace App\Security;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    private $ur;

    public function __construct(UserRepository $ur)
    {
        $this->ur = $ur;
    }

    public function supports(Request $request)
    {
        // $request->headers->has('Application')
//        return $_COOKIE['x-token'] ? true : false;

        return $request->headers->has('AUTH-TOKEN');
    }

    public function getCredentials(Request $request)
    {
//        return [
//            'AUTH-TOKEN' => $_COOKIE['x-token']
//        ];

        return [
//            'Application' => $request->headers->get('Application'),
            'AUTH-TOKEN' => $request->headers->get('AUTH-TOKEN')
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $user = $this->ur->findOneBy(['apiToken' => $credentials['AUTH-TOKEN']]);

        if (!$user) {
            throw new AuthenticationException('User not found');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
//        if ($credentials['Application'] !== $_ENV['APP_SECRET']) {
//            return false;
//        }

        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'error' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return null;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            'error' => "Unauthorized request!"
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
