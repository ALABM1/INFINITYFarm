<?php

namespace App\Service;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\Png;
use BaconQrCode\Writer;

class QrCodeGenerator
{
    // Déplacez la déclaration de la constante ici, en dehors de la méthode
    const QR_CODE_SIZE = 400;
    public function generateQRCode(string $userEmail, string $userPassword): string
    {
        

        // Create a rendering style with a white fill color
        $fill = new Fill(new Rgb(255, 255, 255));
        $rendererStyle = new RendererStyle(QR_CODE_SIZE, QR_CODE_SIZE, null, null, $fill);

        // Create an image renderer with the specified style
        $renderer = new ImageRenderer($rendererStyle, new Png(), new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH));

        // Create a writer based on the renderer
        $writer = new Writer($renderer);

        // Encode the user email and password data into a QR code
        $qrCode = Encoder::encode($userEmail . '|' . $userPassword, Encoder::FORMAT_TEXT);

        // Return the base64 encoded QR code image data
        return base64_encode($writer->writeString($qrCode));
    }
}
