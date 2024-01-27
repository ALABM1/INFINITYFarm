<?php
// src/Service/PdfGenerator.php

namespace App\Service;

use Knp\Snappy\Pdf;
use Twig\Environment;

class PdfGenerator
{
    private $pdf;
    private $twig;

    public function __construct(Pdf $pdf, Environment $twig)
    {
        $this->pdf = $pdf;
        $this->twig = $twig;
    }

    public function generatePdf(array $users): string
    {
        // Calculate statistics
        $totalUsers = count($users);

        // Filter users based on gender
        $maleUsers = array_filter($users, function($user) {
            return $user->getSexe() == 'Homme'; // Assuming there's a getSexe method in the User entity
        });

        $femaleUsers = array_filter($users, function($user) {
            return $user->getSexe() == 'Femme'; // Assuming there's a getSexe method in the User entity
        });

        // Calculate percentages
        $malePercentage = ($totalUsers > 0) ? (count($maleUsers) / $totalUsers) * 100 : 0;
        $femalePercentage = ($totalUsers > 0) ? (count($femaleUsers) / $totalUsers) * 100 : 0;

        // Render the PDF content using Twig
        $html = $this->twig->render('admin1/pdf.html.twig', [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'maleUsers' => $maleUsers,
            'femaleUsers' => $femaleUsers,
            'malePercentage' => $malePercentage,
            'femalePercentage' => $femalePercentage,
        ]);

        // Generate the PDF file
        return $this->pdf->getOutputFromHtml($html);
    }

}
