<?php

namespace App\Controller;

use App\Entity\Activite;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TCPDF;

class PdfActiviteController extends AbstractController
{
    #[Route('/generate-pdf-activite', name: 'generate_pdf_activite')]
    public function generatePdf(EntityManagerInterface $entityManager): Response
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
        $pdf->Cell(0, 10, 'Liste des activités', 0, 1, 'C');
        $pdf->SetFont('freeserif', '', 14);

        // Add a line break
        $pdf->Ln(15);

        // Get the list of activites directly via the entity manager
        $activites = $entityManager->getRepository(Activite::class)->findAll();

        // Define table headers
        $headers = array('Objet de l\'activité ', 'Description ', 'Distinataire', 'Espèce de ressource', 'État de l\'activité');

        // Add the table to the PDF with a different color for the header
        $pdf->SetFillColor(0, 128, 0); // Change these RGB values to the desired color
        $pdf->SetFont('freeserif', 'B', 12);

        // Add table headers
        foreach ($headers as $header) {
            $pdf->Cell(38, 10, $header, 1, 0, 'C', 1);
        }
        $pdf->Ln();

        // Add table data
        $pdf->SetFont('freeserif', '', 12);
        foreach ($activites as $activite) {
            $pdf->Cell(38, 10, $activite->getObjetact(), 1);
            $pdf->Cell(38, 10, $activite->getDescriptionact(), 1);
            $pdf->Cell(38, 10, $activite->getDistact(), 1);
            $pdf->Cell(38, 10, $activite->getSpeciesres(), 1);
            $pdf->Cell(38, 10, $activite->getEtatact(), 1);
            $pdf->Ln();
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
        $pdfContent = $pdf->Output('activite_list.pdf', 'S');

        // Symfony response with the PDF content
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }
}
