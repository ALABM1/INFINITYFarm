<?php

namespace App\Controller;
use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatcaisseController extends AbstractController
{
    #[Route('/statcaisse', name: 'app_statcaisse')]
    public function statistiques(EntityManagerInterface $entityManager): Response
    {
        $transactionRepository = $entityManager->getRepository(Transaction::class);

        // Fetch transactions where typeTra is either "Revenu" or "Dépense"
        $transactions = $transactionRepository->findBy(['typeTra' => ['Revenu', 'Dépense']]);

        // Format the data for your chart
        $transactionData = [
            'labels' => [], // Labels for the X-axis (dates)
            'revenuValues' => [], // Corresponding values for 'Revenu' (amounts)
            'depenseValues' => [], // Corresponding values for 'Dépense' (amounts)
        ];

        foreach ($transactions as $transaction) {
            // Assuming the date is a DateTime object
            $date = $transaction->getDateTra()->format('Y-m-d');
            $transactionData['labels'][] = $date;

            if ($transaction->getTypeTra() === 'Revenu') {
                $transactionData['revenuValues'][$date] = $transaction->getMontant();
                $transactionData['depenseValues'][$date] = 0; // Initialize depense value to 0 for consistency
            } elseif ($transaction->getTypeTra() === 'Dépense') {
                $transactionData['depenseValues'][$date] = $transaction->getMontant();
                $transactionData['revenuValues'][$date] = 0; // Initialize revenu value to 0 for consistency
            }
        }

        return $this->render('statcaisse/index.html.twig', [
            'transactionData' => json_encode($transactionData),
        ]);
    }
}