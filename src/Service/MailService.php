<?php
/// src/Service/MailService.php
namespace App\Service;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    public function sendTestEmail(string $recipientEmail): void
    {
        $email = (new Email())
            ->from('infintyfarm11@outlook.com') // Remplacez par votre adresse e-mail
            ->to($recipientEmail)
            ->subject('Test Email')
            ->text('This is a test email.');

        $this->mailer->send($email);
    }

    public function sendEmailVerification(string $to, string $verificationCode): void
    {
        // Ajoutez un log pour voir si cette méthode est appelée
        dump('Sending email verification...');

        $email = (new Email())
            ->from('houssemmeguebli@outlook.com') // Remplacez par votre adresse e-mail
            ->to($to)
            ->subject('Code de Vérification')
            ->html('Votre code de vérification est : ' . $verificationCode);

        try {
            $this->mailer->send($email);

            // Ajoutez un log pour indiquer que l'e-mail a été envoyé avec succès
            dump('Email sent successfully!');
        } catch (\Exception $e) {
            // Ajoutez un log pour indiquer s'il y a une erreur lors de l'envoi de l'e-mail
            dump('Error sending email: ' . $e->getMessage());
        }
    }
}
