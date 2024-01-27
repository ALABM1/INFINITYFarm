<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Form\ActiviteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backactivite')]
class backActiviteController extends AbstractController
{
    #[Route('/', name: 'app_backactivite_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $activites = $entityManager
            ->getRepository(Activite::class)
            ->findAll();

        return $this->render('backactivite/index.html.twig', [
            'activites' => $activites,
        ]);
    }

    #[Route('/new', name: 'app_backactivite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $activite = new Activite();
        $form = $this->createForm(ActiviteType::class, $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($activite);
            $entityManager->flush();

            return $this->redirectToRoute('app_backactivite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backactivite/new.html.twig', [
            'activite' => $activite,
            'form' => $form,
        ]);
    }

    #[Route('/{idact}', name: 'app_backactivite_show', methods: ['GET'])]
    public function show(Activite $activite): Response
    {
        return $this->render('backactivite/show.html.twig', [
            'activite' => $activite,
        ]);
    }

    #[Route('/{idact}/edit', name: 'app_backactivite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Activite $activite, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ActiviteType::class, $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_backactivite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backactivite/edit.html.twig', [
            'activite' => $activite,
            'form' => $form,
        ]);
    }

    #[Route('/{idact}', name: 'app_backactivite_delete', methods: ['POST'])]
    public function delete(Request $request, Activite $activite, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$activite->getIdact(), $request->request->get('_token'))) {
            $entityManager->remove($activite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_backactivite_index', [], Response::HTTP_SEE_OTHER);
    }
}