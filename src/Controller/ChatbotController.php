<?php

namespace App\Controller;

use OpenAI;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChatbotController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index( ? string $question, ? string $response): Response
    {
        return $this->render('auth/LandingPage.html.twig', [
            'question' => $question,
            'response' => $response
        ]);
    }



    #[Route('/chat', name: 'send_chat', methods:"POST")]
    public function chat(Request $request): Response
    {
        
        
        return $this->forward('App\Controller\ChatbotController::index', [
           
           
        ]);
    }

   


}
