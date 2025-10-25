<?php

/**
 * Test Script untuk Import Rekam Medis
 * 
 * Script ini untuk testing fitur import rekam medis dengan format diagnosa tunggal dan double/triple
 */

require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create new spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Test Import Rekam Medis');

// Set headers
$headers = [
    'A=Hari / Tgl', 'B=Time', 'C=NIK', 'D=Nama Karyawan', 'E=Kode RM', 'F=Nama Pasien',
    'G=Diagnosa 1', 'H=Keluhan 1', 'I=Obat 1-1', 'J=Qty', 'K=Obat 1-2', 'L=Qty', 'M=Obat 1-3', 'N=Qty',
    'O=Diagnosa 2', 'P=Keluhan 2', 'Q=Obat 2-1', 'R=Qty', 'S=Obat 2-2', 'T=Qty', 'U=Obat 2-3', 'V=Qty',
    'W=Diagnosa 3', 'X=Keluhan 3', 'Y=Obat 3-1', 'Z=Qty', 'AA=Obat 3-2', 'AB=Qty', 'AC=Obat 3-3', 'AD=Qty',
    'AE=Petugas', 'AF=Status'
];

$column = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($column . '1', $header);
    $column++;
}

// Test Data 1: Single Diagnosa
$sheet->setCellValue('A2', '24/10/2025');
$sheet->setCellValue('B2', '09:00');
$sheet->setCellValue('C2', '1200929');
$sheet->setCellValue('D2', 'Purnomo');
$sheet->setCellValue('E2', '1200929-A');
$sheet->setCellValue('F2', 'Purnomo');
$sheet->setCellValue('G2', 'Sakit Gigi');
$sheet->setCellValue('H2', 'Nyeri gigi geraham');
$sheet->setCellValue('I2', 'Paracetamol');
$sheet->setCellValue('J2', '10');
$sheet->setCellValue('K2', 'Amoxicilin');
$sheet->setCellValue('L2', '15');
$sheet->setCellValue('M2', '-');
$sheet->setCellValue('N2', '-');
$sheet->setCellValue('O2', '-');
$sheet->setCellValue('P2', '-');
$sheet->setCellValue('Q2', '-');
$sheet->setCellValue('R2', '-');
$sheet->setCellValue('S2', '-');
$sheet->setCellValue('T2', '-');
$sheet->setCellValue('U2', '-');
$sheet->setCellValue('V2', '-');
$sheet->setCellValue('W2', '-');
$sheet->setCellValue('X2', '-');
$sheet->setCellValue('Y2', '-');
$sheet->setCellValue('Z2', '-');
$sheet->setCellValue('AA2', '-');
$sheet->setCellValue('AB2', '-');
$sheet->setCellValue('AC2', '-');
$sheet->setCellValue('AD2', '-');
$sheet->setCellValue('AE2', 'Admin');
$sheet->setCellValue('AF2', 'Close');

// Test Data 2: Double Diagnosa
$sheet->setCellValue('A3', '24/10/2025');
$sheet->setCellValue('B3', '10:30');
$sheet->setCellValue('C3', '50172104');
$sheet->setCellValue('D3', 'Adam Azhari');
$sheet->setCellValue('E3', '50172104-A');
$sheet->setCellValue('F3', 'Adam Azhari');
$sheet->setCellValue('G3', 'ISPA');
$sheet->setCellValue('H3', 'Batuk, Pilek, Sakit Tenggorokan');
$sheet->setCellValue('I3', 'Paracetamol');
$sheet->setCellValue('J3', '10');
$sheet->setCellValue('K3', '-');
$sheet->setCellValue('L3', '-');
$sheet->setCellValue('M3', '-');
$sheet->setCellValue('N3', '-');
$sheet->setCellValue('O3', 'Demam');
$sheet->setCellValue('P3', 'Pusing, Demam Tinggi');
$sheet->setCellValue('Q3', 'Paracetamol');
$sheet->setCellValue('R3', '5');
$sheet->setCellValue('S3', '-');
$sheet->setCellValue('T3', '-');
$sheet->setCellValue('U3', '-');
$sheet->setCellValue('V3', '-');
$sheet->setCellValue('W3', '-');
$sheet->setCellValue('X3', '-');
$sheet->setCellValue('Y3', '-');
$sheet->setCellValue('Z3', '-');
$sheet->setCellValue('AA3', '-');
$sheet->setCellValue('AB3', '-');
$sheet->setCellValue('AC3', '-');
$sheet->setCellValue('AD3', '-');
$sheet->setCellValue('AE3', 'Admin');
$sheet->setCellValue('AF3', 'Close');

// Test Data 3: Triple Diagnosa
$sheet->setCellValue('A4', '24/10/2025');
$sheet->setCellValue('B4', '14:15');
$sheet->setCellValue('C4', '1200337');
$sheet->setCellValue('D4', 'Suparjo');
$sheet->setCellValue('E4', '1200337-A');
$sheet->setCellValue('F4', 'Suparjo');
$sheet->setCellValue('G4', 'Hipertensi');
$sheet->setCellValue('H4', 'Pusing');
$sheet->setCellValue('I4', 'Amlodipin 5Mg');
$sheet->setCellValue('J4', '10');
$sheet->setCellValue('K4', '-');
$sheet->setCellValue('L4', '-');
$sheet->setCellValue('M4', '-');
$sheet->setCellValue('N4', '-');
$sheet->setCellValue('O4', 'Diabetes');
$sheet->setCellValue('P4', 'Lemas');
$sheet->setCellValue('Q4', 'Metformin');
$sheet->setCellValue('R4', '10');
$sheet->setCellValue('S4', '-');
$sheet->setCellValue('T4', '-');
$sheet->setCellValue('U4', '-');
$sheet->setCellValue('V4', '-');
$sheet->setCellValue('W4', 'Asam Urat');
$sheet->setCellValue('X4', 'Nyeri Sendi');
$sheet->setCellValue('Y4', 'Allopurinol');
$sheet->setCellValue('Z4', '5');
$sheet->setCellValue('AA4', '-');
$sheet->setCellValue('AB4', '-');
$sheet->setCellValue('AC4', '-');
$sheet->setCellValue('AD4', '-');
$sheet->setCellValue('AE4', 'Admin');
$sheet->setCellValue('AF4', 'On Progress');

// Save the file
$writer = new Xlsx($spreadsheet);
$filename = 'test_import_rekam_medis_' . date('Y-m-d_H-i-s') . '.xlsx';
$writer->save($filename);

echo "File test berhasil dibuat: $filename\n";
echo "File ini berisi 3 contoh data:\n";
echo "1. Baris 2: Diagnosa Tunggal (Sakit Gigi)\n";
echo "2. Baris 3: Diagnosa Double (ISPA + Demam)\n";
echo "3. Baris 4: Diagnosa Triple (Hipertensi + Diabetes + Asam Urat)\n";
echo "\nGunakan file ini untuk testing fitur import di sistem SIPO ICBP.\n";