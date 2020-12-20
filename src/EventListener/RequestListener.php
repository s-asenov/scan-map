<?php

namespace App\EventListener;

use App\Exception\BadHeaderException;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

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

        if (strpos($request->attributes->get("_route"), "api") === false) {
            return $request;
        }

        if (strpos($request->headers->get('Content-Type'), 'application/json') !== 0) {
//            throw new BadHeaderException();
        }

        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : []);

        return $request;
    }
}