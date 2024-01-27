<?php
// src/Controller/TestController.php

namespace App\Controller;

use App\Service\MailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/send-test-email', name: 'send_test_email')]
    public function sendTestEmail(MailService $mailService): Response
    {
        // Utilisez votre service de messagerie pour envoyer un e-mail de test
        $mailService->sendTestEmail('aladain2000@gmail.com'); // Remplacez par l'adresse e-mail du destinataire

        // Répondez avec une confirmation
        return new Response('Test email sent!');
    }
}
