<?php

require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Template Import');

// Header columns
$headers = ['Nama Diagnosa', 'Deskripsi'];
$column = 'A';

foreach ($headers as $header) {
    $sheet->setCellValue($column . '1', $header);
    $column++;
}

// Add sample data
$sheet->setCellValue('A2', 'Demam Berdarah');
$sheet->setCellValue('B2', 'Demam yang disertai ruam merah dan penurunan trombosit');

$sheet->setCellValue('A3', 'Hipertensi');
$sheet->setCellValue('B3', 'Tekanan darah tinggi');

$sheet->setCellValue('A4', 'Diabetes Mellitus');
$sheet->setCellValue('B4', '');

// Set column widths
$sheet->getColumnDimension('A')->setWidth(30);
$sheet->getColumnDimension('B')->setWidth(50);

// Create Excel file
$writer = new Xlsx($spreadsheet);
$writer->save('storage/app/public/template_import_diagnosa.xlsx');

echo 'Template Excel created successfully\n';

// Create CSV file
$csvWriter = new Csv($spreadsheet);
$csvWriter->setDelimiter(',');
$csvWriter->setEnclosure('"');
$csvWriter->setLineEnding("\n");
$csvWriter->save('storage/app/public/template_import_diagnosa.csv');

echo 'Template CSV created successfully\n';
