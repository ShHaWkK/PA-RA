<?php
namespace Service;

use FPDF;
use Entity\DeliveryModel;

class PDFService
{
    private $googleMapsApiKey;

    public function __construct($googleMapsApiKey)
    {
        $this->googleMapsApiKey = $googleMapsApiKey;
    }

    public function createPDF(DeliveryModel $delivery)
    {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(40, 10, $this->encodeText('Delivery Details'));

        $pdf->SetFont('Arial', '', 12);
        $pdf->Ln(10);
        $pdf->Cell(40, 10, $this->encodeText('Route Name: ' . $delivery->getRouteName()));
        $pdf->Ln(10);
        $pdf->Cell(40, 10, $this->encodeText('Destination: ' . $delivery->getDestination()));
        $pdf->Ln(10);
        $pdf->Cell(40, 10, $this->encodeText('Recipient Type: ' . $delivery->getRecipientType()));
        $pdf->Ln(10);
        $pdf->Cell(40, 10, $this->encodeText('Status: ' . $delivery->getStatus()));
        if ($delivery->getComment()) {
            $pdf->Ln(10);
            $pdf->Cell(40, 10, $this->encodeText('Comment: ' . $delivery->getComment()));
        }

        // Add Google Maps route image
        $mapImageUrl = $this->getGoogleMapsRouteImageUrl($delivery->getRouteName(), $delivery->getDestination());
        $imagePath = tempnam(sys_get_temp_dir(), 'map') . '.png';
        file_put_contents($imagePath, file_get_contents($mapImageUrl));

        // Add the image to the PDF
        $pdf->Ln(20);
        $pdf->Image($imagePath, 10, $pdf->GetY(), 180);

        return $pdf->Output('S');
    }

    private function encodeText($text)
    {
        return mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
    }

    private function getGoogleMapsRouteImageUrl($origin, $destination)
    {
        $origin = urlencode($origin);
        $destination = urlencode($destination);
        return "https://maps.googleapis.com/maps/api/staticmap?size=600x400&markers=color:red%7Clabel:S%7C$origin&markers=color:green%7Clabel:D%7C$destination&key={$this->googleMapsApiKey}";
    }
}
?>
