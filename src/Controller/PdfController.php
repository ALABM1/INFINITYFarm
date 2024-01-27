<?php

namespace App\Controller;

use App\Repository\ParcRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use setasign\Fpdi\Tcpdf\Fpdi; 

class PdfController extends AbstractController
{
    #[Route('/generate-pdf', name: 'generate_pdf', methods: ['GET', 'POST'])]
    public function generatePdf(Request $request, ParcRepository $parcRepository): Response
    {
        // Récupérer les données de la signature depuis la requête POST
        $signatureData = json_decode($request->getContent(), true)['signature'];

        // Instancier FPDI (qui étend déjà TCPDF)
        $pdf = new Fpdi();

        // Ajouter une page
        $pdf->AddPage();
        $pdf->Rect(5, 5, $pdf->GetPageWidth() - 10, $pdf->GetPageHeight() - 10);

        // Ajouter le titre
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Liste des parcs', 0, 1, 'C');
        $pdf->Ln();

        // Récupérer la liste des parcs
        $parcs = $parcRepository->findAll();

        // Définir les en-têtes du tableau
        $headers = array('Nom du Parc', 'Adresse du Parc', 'Superficie du Parc');

        // Ajouter le tableau au PDF
        $pdf->SetFillColor(200, 220, 255);
        $pdf->SetFont('freeserif', 'B', 12); // Utiliser une police en gras pour les en-têtes

        // Ajouter les en-têtes du tableau
        foreach ($headers as $header) {
            $pdf->Cell(60, 10, $header, 1, 0, 'C', 1); // Augmenter la largeur de la cellule
        }
        $pdf->Ln();
        $pdf->SetFont('freeserif', 'A', 12); // Utiliser une police en gras pour les en-têtes

        // Ajouter les données du tableau
        foreach ($parcs as $parc) {
            $pdf->Cell(60, 10, $parc->getNomparc(), 1);
            $pdf->Cell(60, 10, $parc->getAdresseparc(), 1);
            $pdf->Cell(60, 10, $parc->getSuperficieparc(), 1);
            $pdf->Ln();
        }
        $pdf->Ln(20);


        // Ajouter la signature au PDF
        $this->addSignatureToPdf($pdf, $signatureData);
        $pdf->SetFont('freeserif', 'I', 10); // Utiliser une police italique pour la date
        $pdf->Cell(0, 120, ' Le ' . date( 'Y-m-d') . ' - Houssem Meguebli', 0, 1, 'R');

        $finalPdfContent = $pdf->Output('output.pdf', 'S');

        // Réponse Symfony avec le contenu du PDF final
        $response = new Response($finalPdfContent);
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }

    private function addSignatureToPdf(Fpdi $pdf, $signatureData)
    {

        $imgData = explode(',', $signatureData);
        $imgBase64 = $imgData[1];
        $imgBinary = base64_decode($imgBase64);

        // Ajouter l'image de signature au PDF
        $pdf->Image('@' . $imgBinary, 147, 177, 70);
    }
}