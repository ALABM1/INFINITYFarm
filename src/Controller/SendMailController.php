<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class SendMailController extends AbstractController
{
    /**
     * @Route("/forgot-password", name="forgot_password")
     */
    public function forgotPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');

            // Rechercher l'utilisateur par l'adresse e-mail
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($user) {
                // Générer un code de récupération de mot de passe (6 caractères ici)
                $code = substr(md5(random_bytes(10)), 0, 6);

                // Encodage et stockage du code (dans une colonne "passwordResetCode" par exemple)
                $user->setPasswordResetCode($passwordEncoder->encodePassword($user, $code));
                $this->getDoctrine()->getManager()->flush();

                // Envoyer l'e-mail avec le code
                $email = (new Email())
                    ->from(new Address('your@example.com', 'Your Name'))
                    ->to($user->getEmail())
                    ->subject('Password Reset Code')
                    ->text('Your password reset code is: ' . $code);

                $mailer->send($email);

                $this->addFlash('success', 'Password reset code sent to your email.');
            } else {
                $this->addFlash('error', 'User not found.');
            }
        }

        return $this->render('forgot_password/forgot_password.html.twig');
    }
}
