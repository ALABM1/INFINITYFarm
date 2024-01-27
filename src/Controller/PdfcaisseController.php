<?php

namespace App\Controller;

use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TCPDF;

class PdfcaisseController extends AbstractController
{

    #[Route('/generate-pdf', name: 'generate_pdf')]
    public function generatePdf(EntityManagerInterface $entityManager, TransactionRepository $transactionRepository): Response
    {
        // Instantiate TCPDF
        $pdf = new TCPDF();

        // Add a TrueType Unicode font
        $fontPath = $this->getParameter('kernel.project_dir') . '/path-to-font/';
        $fontname = $fontPath . 'FreeSerifItalic.ttf';
        $pdf->AddFont('freeserif', '', $fontname, true);
        $pdf->SetFont('freeserif', '', 14);

        // Add a page
        $pdf->AddPage();
        $pdf->Rect(5, 5, $pdf->GetPageWidth() - 10, $pdf->GetPageHeight() - 10);

         // Add the title in the custom font
        $pdf->SetFont('freeserif', '', 18);
        $pdf->Cell(0, 10, 'Liste de transactions', 0, 1, 'C');
        $pdf->SetFont('freeserif', '', 14);

        // Add a line break
        $pdf->Ln(15);

        // Add a line break after the image
        $pdf->Ln(50);

        // Get the list of transactions directly via autowiring
        $transactions = $transactionRepository->findAll();

        // Define table headers
        $headers = array('Transaction ID', 'Category', 'Type', 'Date', 'Amount');

        // Add the table to the PDF with a different color for the header
        $pdf->SetFillColor(0, 128, 0); // Change these RGB values to the desired color
        $pdf->SetFont('freeserif', 'B', 12);

        // Add table headers
        foreach ($headers as $header) {
            $pdf->Cell(35, 10, $header, 1, 0, 'C', 1);
        }
        $pdf->Ln();

        // Add table data
        $pdf->SetFont('freeserif', '', 12);
        foreach ($transactions as $transaction) {
            if ($transaction->getTypeTra() === 'Dépense') {
                $pdf->SetTextColor(255, 0, 0); // Red color
            } else {
                $pdf->SetTextColor(0, 128, 0); // Green color
            }
            $pdf->Cell(35, 10, $transaction->getIdTra(), 1);
            $pdf->Cell(35, 10, $transaction->getCategTra(), 1);
            $pdf->Cell(35, 10, $transaction->getTypeTra(), 1);
            $pdf->Cell(35, 10, $transaction->getDateTra()->format('Y-m-d'), 1);
            $pdf->Cell(35, 10, $transaction->getMontant(), 1);
            $pdf->Ln();
            if ($transaction->getTypeTra() === 'Dépense') {
                $pdf->SetTextColor(0, 0, 0); // Reset text color to black
            } else {
                $pdf->SetTextColor(0, 0, 0); // Green color
            }
        }

        // Add a line break after the table
        $pdf->Ln(10);

         // Add an image
         $imagePath = $this->getParameter('kernel.project_dir') . '/public/img/icon2.jpg';
         $pdf->Image($imagePath, $pdf->GetX() + 130, $pdf->GetY(), 20, 20);
 
        // Add the date to the footer with "INFINITYFARM"
        $pdf->SetFont('freeserif', 'I', 10);
        $pdf->Cell(0, 10, ' ' . date('Y-m-d') . ' - INFINITYFARM', 0, 1, 'C');
        
        // PDF output
        $pdfContent = $pdf->Output('transaction_list.pdf', 'S');

        // Symfony response with the PDF content
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }
}
