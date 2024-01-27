<?php

namespace App\Controller;


use App\Entity\Transaction;
use App\Form\TransactionType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;



#[Route('/transaction')]
class TransactionController extends AbstractController
{
    #[Route('/', name: 'app_transaction_index', methods: ['GET'])]
    public function index(Request $request, TransactionRepository $transactionRepository, MailerInterface $mailer): Response
    {
        // Récupérer les paramètres de recherche depuis la requête
        $category = $request->query->get('category');
        $type = $request->query->get('type');
        $startDate = $request->query->get('startDate');
        $endDate = $request->query->get('endDate');

        // Appeler la méthode de recherche dans le repository
        $criteria = [
            'category' => $category,
            'type' => $type,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];

        // Fetch transactions data based on your search criteria
        $transactions = $transactionRepository->findBySearchCriteria($criteria);

        // ... rest of your email sending logic using $transactions

        // Calculer la différence entre les revenus et les dépenses
        $sommeRevenu = $transactionRepository->calculateSumByType('Revenu', $startDate, $endDate);
        $sommeDepense = $transactionRepository->calculateSumByType('Dépense', $startDate, $endDate);
        $difference = $sommeRevenu - $sommeDepense;
        $this->sendcaisseEmail($mailer, $difference);

        return $this->render('transaction/index.html.twig', [
            'transactions' => $transactions,
            'difference' => $difference,
        ]);
    }
    #[Route('/send_caisse_email', name: 'send_caisse_email')]
    public function sendCaisseEmail(MailerInterface $mailer, float $difference): Response
    {
        // Replace these placeholders with your actual email configuration
        $senderEmail = 'houssemmeguebli@outlook.com';
        $receiverEmail = 'sarra.jouini@esprit.tn';

        // Check if the difference is negative
        if ($difference < 0) {
            // Construct the HTML content of the email
            $htmlContent = $this->renderView('mailcaisse/index.html.twig', ['difference' => $difference]);

            // Create the email
            $email = (new Email())
                ->from($senderEmail)
                ->to($receiverEmail)
                ->subject('Negative Caisse Balance Alert')
                ->html($htmlContent);

            try {
                // Send the email
                $mailer->send($email);
                $this->addFlash('success', 'Negative caisse balance alert email sent successfully!');
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('error', 'Error sending the negative caisse balance alert email: ' . $e->getMessage());
            }
        } else {
            $this->addFlash('info', 'No negative caisse balance to report.');
        }

        return $this->render('mailcaisse/index.html.twig', ['difference' => $difference]);
    }

    #[Route('/new', name: 'app_transaction_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $transaction = new Transaction();
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($transaction);
            $entityManager->flush();

            return $this->redirectToRoute('app_transaction_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/new.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    #[Route('/{idTra}', name: 'app_transaction_show', methods: ['GET'])]
    public function show(Transaction $transaction): Response
    {
        return $this->render('transaction/show.html.twig', [
            'transaction' => $transaction,
        ]);
    }

    #[Route('/{idTra}/edit', name: 'app_transaction_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/edit.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    #[Route('/{idTra}', name: 'app_transaction_delete', methods: ['POST'])]
    public function delete(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transaction->getIdTra(), $request->request->get('_token'))) {
            $entityManager->remove($transaction);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
    }
}
