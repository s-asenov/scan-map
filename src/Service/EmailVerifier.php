<?php


namespace App\Service;


use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
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
    private $mailer;
    private $verifyEmailHelper;
    private $router;

    public function __construct(RouterInterface $router, VerifyEmailHelperInterface $verifyEmailHelper, MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->router = $router;
    }

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
     * @return string|void
     */
    public function verifyEmail(User $user, string $uri)
    {
        try {
            $this->verifyEmailHelper->validateEmailConfirmation($uri, $user->getId(), $user->getEmail());
        } catch (VerifyEmailExceptionInterface $e) {
            return $this->router->generate('app_login');
        }
    }
}