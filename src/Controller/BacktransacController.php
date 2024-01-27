<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\TransactionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backtransac')]
class BacktransacController extends AbstractController
{
    #[Route('/', name: 'app_backtransac_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $transactions = $entityManager
            ->getRepository(Transaction::class)
            ->findAll();

        return $this->render('backoffice/index.html.twig', [
            'transactions' => $transactions,
        ]);
    }

   #[Route('/new', name: 'app_backtransac_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $transaction = new Transaction();
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($transaction);
            $entityManager->flush();

            return $this->redirectToRoute('app_backtransac_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backoffice/new.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

   #[Route('/{idTra}', name: 'app_backtransac_show', methods: ['GET'])]
    public function show(Transaction $transaction): Response
    {
        return $this->render('backoffice/show.html.twig', [
            'transaction' => $transaction,
        ]);
    }

    #[Route('/{idTra}/edit', name: 'app_backtransac_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_backtransac_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backoffice/edit.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    #[Route('/{idTra}', name: 'app_backtransac_delete', methods: ['POST'])]
    public function delete(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transaction->getIdTra(), $request->request->get('_token'))) {
            $entityManager->remove($transaction);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_backtransac_index', [], Response::HTTP_SEE_OTHER);
    }
}