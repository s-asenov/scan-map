<?php


namespace App\Controller;

use App\Service\MailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * The method is responsible for the sending of the contact form email.
     *
     * @param MailService $mailService
     * @return JsonResponse
     */
    #[Route("/email", name: "email_send", methods:["POST"])]
    public function sendMail(MailService $mailService): JsonResponse
    {
        return $mailService->sendMail();
    }

    #[
        Route("/", name: "app_homepage", methods:["GET"]),
        Route("/admin/{reactRoute}", name: "app_admin", methods:["GET"]),
        Route("/{reactRoute}", name: "app_react", methods:["GET"])
    ]
    public function index(): Response
    {
        return $this->render('react/react.html.twig');
    }
}