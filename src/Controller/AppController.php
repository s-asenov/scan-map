<?php


namespace App\Controller;

use App\Service\DistributionZonesUploader;
use App\Util\FormHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/email", name="email_send")
     */
    public function sendMail(Request $request, MailerInterface $mailer, FormHelper $helper)
    {
        $form = $request->request->all();

        if (!$helper->checkFormData(['from', 'subject', 'text'], $form)) {
            return new JsonResponse([
                'status' => $helper::META_ERROR,
                'meta' => $helper::MISSING_CREDENTIALS
            ]);
        }

        $email = (new Email())
            ->from($form['from'])
            ->to('support@flora.noit.eu')
            ->subject($form['subject'])
            ->text($form['text']);

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return new JsonResponse([
                'status' => $helper::META_ERROR,
                'meta' => "not sent"
            ], 400);
        }

        return new JsonResponse([
            'status' => $helper::META_SUCCESS,
            'meta' => "sent"
        ]);
    }
    /**
     * @Route ("/", name="app_homepage")
     * @Route("/{reactRoute}", name="app_react")
     */
    public function index()
    {
        return $this->render('react/react.html.twig');
    }
}