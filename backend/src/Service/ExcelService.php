<?php
namespace Service;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Exception;

$publicDir = getenv('PUBLIC_DIR');

class ExcelService
{
    private string $publicDir;

    public function __construct()
    {
        $this->publicDir = __DIR__ . '/../../public';
    }

    public function generateCollectionExcel(array $data): string
    {
        $collectionRouteDir = $this->publicDir . '/collection_route';

        // Ensure directory exists
        if (!is_dir($collectionRouteDir)) {
            mkdir($collectionRouteDir, 0777, true);
        }

        $filename = 'collection_' . uniqid() . '.xlsx';
        $filePath = $collectionRouteDir . '/' . $filename;

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
            $product = $entry['product'];

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
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        // Return the relative path, starting after 'public/'
        return '/collection_route/' . $filename;
    }

    public function getFileContent(?string $relativeFilePath): array
    {
        if (!is_string($relativeFilePath) || empty($relativeFilePath)) {
            return ['error' => 'Invalid file path provided.'];
        }

        // Construire le chemin absolu du fichier
        $absoluteFilePath = $this->publicDir . '/' . ltrim($relativeFilePath, '/');

        if (!file_exists($absoluteFilePath)) {
            return ['error' => "File not found at path: $absoluteFilePath"];
        }

        // Lire le contenu du fichier
        $fileContent = file_get_contents($absoluteFilePath);

        if ($fileContent === false) {
            return ['error' => "Error reading the file at path $absoluteFilePath."];
        }

        return [
            'content' => $fileContent,
            'filename' => basename($absoluteFilePath),
            'size' => filesize($absoluteFilePath)
        ];
    }

}