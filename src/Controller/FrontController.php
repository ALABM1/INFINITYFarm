<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    #[Route('/front', name: 'app_front')]
    public function index(): Response
    {
        return $this->render('/auth/LandingPage.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }
    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('front/contact.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }
    #[Route('/services', name: 'app_services')]
    public function services(): Response
    {
        return $this->render('front/services.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }



}