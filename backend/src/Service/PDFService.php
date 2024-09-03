<?php
namespace Service;

use Entity\RouteModel;
use FPDF;
use Entity\DeliveryModel;

class PDFService
{
    private $googleMapsApiKey;

    public function __construct($googleMapsApiKey)
    {
        $this->googleMapsApiKey = $googleMapsApiKey;
    }

    public function createPDF(RouteModel $route)
    {
        $pdf = new FPDF();
        $pdf->AddPage();

        // Titre de la route
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, $this->encodeText('Feuille de route: ' . $route->getName()), 0, 1, 'C');

        // Informations sur la route
        $pdf->SetFont('Arial', '', 12);
        $pdf->Ln(10);
        $pdf->Cell(0, 10, $this->encodeText('Chauffeur: ' . $route->getDriver()->getFirstName() .' '. $route->getDriver()->getLastName()));
        $pdf->Ln(10);
        $pdf->Cell(0, 10, $this->encodeText('Véhicule: ' . $route->getVehicle()->getModel() . ' ' .$route->getVehicle()->getLicensePlate()));
        $pdf->Ln(10);
        $pdf->Cell(0, 10, $this->encodeText('Début: ' . $route->getStartTime()->format('d-m-Y H:i:s')));

        // Section des destinations
        $pdf->Ln(20);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, $this->encodeText('Destinations'), 0, 1, 'L');

        foreach ($route->getDestinations() as $destination) {
            // Détails de la destination
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Ln(10);
            $pdf->Cell(0, 10, $this->encodeText($destination->getAddress()));

            $pdf->SetFont('Arial', '', 12);
            $pdf->Ln(8);
            $pdf->Cell(0, 10, $this->encodeText('Type de livraison: ' . ucfirst($destination->getRecipientType())));
            $pdf->Ln(8);
            $pdf->Cell(0, 10, $this->encodeText('Date: ' . $destination->getDeliveryDate()->format('d-m-Y H:i:s')));

            if ($destination->getComment()) {
                $pdf->Ln(8);
                $pdf->MultiCell(0, 10, $this->encodeText('Commentaire: ' . $destination->getComment()));
            }

            // Section des livraisons
            $pdf->Ln(8);
            $pdf->SetFont('Arial', 'I', 12);
            $pdf->Cell(0, 10, $this->encodeText('Produits:'), 0, 1, 'L');

            foreach ($destination->getDeliveries() as $delivery) {
                $pdf->SetFont('Arial', '', 12);
                $pdf->Ln(5);
                $pdf->Cell(0, 10, $this->encodeText('- Produit: ' . $delivery->getProduct()->getName() . ' | Quantité: ' . $delivery->getQuantity()));

                if ($delivery->getComment()) {
                    $pdf->Ln(5);
                    $pdf->MultiCell(0, 10, $this->encodeText('  Commentaire: ' . $delivery->getComment()));
                }
            }

            // Ajout d'une ligne pour séparer les destinations
            $pdf->Ln(10);
            $pdf->Cell(0, 0, '', 'T');
            $pdf->Ln(10);
        }

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
