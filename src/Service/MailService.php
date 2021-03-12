<?php


namespace App\Service;

use App\Util\ApiRequest;
use App\Util\FormHelper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Validator\Constraints as Assert;

class MailService
{
    public function __construct(private MailerInterface $mailer, private ApiRequest $request)
    {
    }

    /**
     * The method is responsible for validating and sending the email.
     *
     * @return JsonResponse
     */
    public function sendMail(): JsonResponse
    {
        $constraint = new Assert\Collection([
            'from' => [
                new Assert\NotBlank(),
                new Assert\Email(),
            ],
            'subject' =>  new Assert\NotBlank(),
            'text' => new Assert\Length(['min' => 20])
        ]);

        $errors = $this->request->validate($constraint);

        if ($errors) {
            return $errors;
        }

        $data = $this->request->toArray();

        $email = (new Email())
            ->from($data['from'])
            ->to('support@flora.noit.eu')
            ->subject($data['subject'])
            ->text($data['text']);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return new JsonResponse([
                'status' => FormHelper::META_ERROR,
                'meta' => $e
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return new JsonResponse([
            'status' => FormHelper::META_SUCCESS,
            'meta' => "sent"
        ]);
    }
}