<?php
// Path: backend/src/Service/PDFService.php
namespace Service;

use FPDF;
use Entity\DeliveryModel;

class PDFService
{
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
        
        $pdf->Output('S');
        return $pdf->Output('S');
    }
}
?>
