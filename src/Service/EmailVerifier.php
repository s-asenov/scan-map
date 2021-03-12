<?php


namespace App\Service;


use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\RouterInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

/**
 * Class EmailVerifier
 *
 * The class is responsible for the verification emails send to
 * the newly created users.
 *
 * @package App\Service
 */
class EmailVerifier
{
    public function __construct(
        private RouterInterface $router,
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface $mailer)
    { }

    /**
     * The method is the responsible for sending the email with the signature used for verification.
     *
     * @param User $user
     * @throws TransportExceptionInterface
     */
    public function sendEmail(User $user): void
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'api_registration_confirmation_route',
            $user->getId(),
            $user->getEmail()
        );

        $email = new TemplatedEmail();
        $email->to($user->getEmail());
        $email->from("support@flora.noit.eu");
        $email->subject("Потвърждаване на имейл");
        $email->htmlTemplate('security/confirmation.html.twig');
        $email->context([
            'signedUrl' => $signatureComponents->getSignedUrl(),
            'expiresAt' => $signatureComponents->getExpiresAt()
        ]);

        $this->mailer->send($email);
    }

    /**
     * @param User $user
     * @param string $uri
     * @return void
     * @throws VerifyEmailExceptionInterface
     */
    public function verifyEmail(User $user, string $uri): void
    {
         $this->verifyEmailHelper->validateEmailConfirmation($uri, $user->getId(), $user->getEmail());
    }
}