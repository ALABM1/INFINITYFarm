<?php
// src/Service/PdfGenerator.php
namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGenerator2
{
    private Dompdf $dompdf;

    public function __construct(Dompdf $dompdf)
    {
        $this->dompdf = $dompdf;
    }

    public function generatePdf2(string $htmlContent): \Dompdf\Canvas\Canvas
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);

        $this->dompdf->setOptions($options);
        $this->dompdf->loadHtml($htmlContent);
        $this->dompdf->setPaper('A4', 'portrait');

        $this->dompdf->render();

        return $this->dompdf->output();
    }
}
