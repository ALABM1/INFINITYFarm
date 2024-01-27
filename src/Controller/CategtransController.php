<?php

namespace App\Controller;

use App\Entity\Categtrans;
use App\Form\CategtransType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categtrans')]
class CategtransController extends AbstractController
{
    #[Route('/', name: 'app_categtrans_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $categtrans = $entityManager
            ->getRepository(Categtrans::class)
            ->findAll();

        return $this->render('categtrans/index.html.twig', [
            'categtrans' => $categtrans,
        ]);
    }

    #[Route('/new', name: 'app_categtrans_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categtran = new Categtrans();
        $form = $this->createForm(CategtransType::class, $categtran);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categtran);
            $entityManager->flush();

            return $this->redirectToRoute('app_categtrans_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categtrans/new.html.twig', [
            'categtran' => $categtran,
            'form' => $form,
        ]);
    }

    #[Route('/{idCatTra}', name: 'app_categtrans_show', methods: ['GET'])]
    public function show(Categtrans $categtran): Response
    {
        return $this->render('categtrans/show.html.twig', [
            'categtran' => $categtran,
        ]);
    }

    #[Route('/{idCatTra}/edit', name: 'app_categtrans_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categtrans $categtran, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategtransType::class, $categtran);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_categtrans_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categtrans/edit.html.twig', [
            'categtran' => $categtran,
            'form' => $form,
        ]);
    }

    #[Route('/{idCatTra}', name: 'app_categtrans_delete', methods: ['POST'])]
    public function delete(Request $request, Categtrans $categtran, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categtran->getIdCatTra(), $request->request->get('_token'))) {
            $entityManager->remove($categtran);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_categtrans_index', [], Response::HTTP_SEE_OTHER);
    }
}
