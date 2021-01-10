<?php

namespace App\EventListener;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class RequestListener
{
    /**
     * @param RequestEvent $event
     * @return Request
     * @throws Exception
     */
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if (strpos($request->attributes->get("_route"), "api") === true) {
            if (strpos($request->headers->get('Content-Type'), 'application/json') !== 0) {
                throw new AccessDeniedException();
            }
        }

        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : []);

        return $request;
    }
}