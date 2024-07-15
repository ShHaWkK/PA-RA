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
        $pdf->Cell(40, 10, 'Delivery Details');

        $pdf->SetFont('Arial', '', 12);
        $pdf->Ln(10);
        $pdf->Cell(40, 10, 'Route Name: ' . $delivery->getRouteName());
        $pdf->Ln(10);
        $pdf->Cell(40, 10, 'Destination: ' . $delivery->getDestination());
        $pdf->Ln(10);
        $pdf->Cell(40, 10, 'Recipient Type: ' . $delivery->getRecipientType());
        $pdf->Ln(10);
        $pdf->Cell(40, 10, 'Status: ' . $delivery->getStatus());
        if ($delivery->getComment()) {
            $pdf->Ln(10);
            $pdf->Cell(40, 10, 'Comment: ' . $delivery->getComment());
        }

        // Add Google Maps route image
        $mapImageUrl = $this->getGoogleMapsRouteImageUrl($delivery->getRouteName(), $delivery->getDestination());
        error_log("Google Maps URL: " . $mapImageUrl); // Log the URL for debugging

        $imagePath = tempnam(sys_get_temp_dir(), 'map') . '.png';  // Ensure the image has a .png extension

        // Download the image
        $imageContent = @file_get_contents($mapImageUrl);
        if ($imageContent === FALSE) {
            error_log("Failed to download image from Google Maps API");
            throw new \Exception("Failed to download image from Google Maps API");
        }

        file_put_contents($imagePath, $imageContent);

        // Add the image to the PDF
        $pdf->Ln(20);
        $pdf->Image($imagePath, 10, $pdf->GetY(), 180);

        $pdf->Output('S');
        return $pdf->Output('S');
    }

    private function getGoogleMapsRouteImageUrl($origin, $destination)
    {
        $origin = urlencode($origin);
        $destination = urlencode($destination);
        return "https://maps.googleapis.com/maps/api/staticmap?size=600x400&markers=color:red%7Clabel:S%7C$origin&markers=color:green%7Clabel:D%7C$destination&key={$this->googleMapsApiKey}";
    }
}
?>
