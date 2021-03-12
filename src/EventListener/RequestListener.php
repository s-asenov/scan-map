<?php

namespace App\EventListener;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class RequestListener
 *
 * The listener catches the request made to the api routes
 * and adds additional logic
 *
 * @package App\EventListener
 */
class RequestListener
{
    /**
     * The sole reason of the method is to check if the route name
     * contains "api" meaning it is a request to an api endpoint and
     * if it has Content-Type set to application/json and if not
     * throw an exception.
     *
     * @param RequestEvent $event
     * @return Request
     * @throws Exception
     */
    public function onKernelRequest(RequestEvent $event): Request
    {
        $request = $event->getRequest();

        if (strpos($request->attributes->get("_route"), "api") === true) {
            if (strpos($request->headers->get('Content-Type'), 'application/json') !== 0) {
                throw new AccessDeniedException();
            }
        }

        return $request;
    }
}