<?php
namespace Service;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Exception;

class ExcelService
{
    public function generateCollectionExcel(array $data): string
    {
        $publicDir = __DIR__ . '/../public';
        $collectionRouteDir = $publicDir . '/collection_route';

        // Ensure directory exists
        if (!is_dir($collectionRouteDir)) {
            mkdir($collectionRouteDir, 0777, true);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Define headers and fill data
        $headers = [
            'A1' => 'Address',
            'B1' => 'Product Name',
            'C1' => 'Barcode',
            'D1' => 'Expiration Date',
            'E1' => 'Volume (L)',
            'F1' => 'Notified Quantity',
            'G1' => 'Quantity Collected',
            'H1' => 'Is Collected'
        ];

        foreach ($headers as $cell => $header) {
            $sheet->setCellValue($cell, $header);
        }

        $row = 2;
        foreach ($data as $entry) {
            $product = $entry['product']; // Assuming 'product' is a ProductModel object

            $sheet->setCellValue('A' . $row, $entry['address']);
            $sheet->setCellValue('B' . $row, $product->getName());
            $sheet->setCellValue('C' . $row, $product->getBarcode());
            $sheet->setCellValue('D' . $row, $product->getExpirationDate()->format('Y-m-d'));
            $sheet->setCellValue('E' . $row, $product->getVolume());
            $sheet->setCellValue('F' . $row, $entry['notified_quantity']);
            $sheet->setCellValue('G' . $row, $entry['quantity_collected']);
            $sheet->setCellValue('H' . $row, $entry['is_collected'] ? 'Yes' : 'No');
            $row++;
        }

        // Save the file in the 'collection_route' directory
        $filename = $collectionRouteDir . '/collection_' . uniqid() . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);

        return $filename;
    }

}