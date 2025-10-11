<?php

namespace App\Http\Controllers;

use App\Models\StokBulanan;
use App\Models\Obat;
use App\Models\JenisObat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StokBulananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StokBulanan::with([
            'obat:id_obat,nama_obat,keterangan,id_jenis_obat,id_satuan',
            'obat.jenisObat:id_jenis_obat,nama_jenis_obat',
            'obat.satuanObat:id_satuan,nama_satuan'
        ]);

        // Filter by periode
        if ($request->has('periode') && $request->periode != '') {
            $query->where('periode', $request->periode);
        }

        // Filter by range periode
        if ($request->has('periode_start') && $request->periode_start != '') {
            $query->where('periode', '>=', $request->periode_start);
        }
        if ($request->has('periode_end') && $request->periode_end != '') {
            $query->where('periode', '<=', $request->periode_end);
        }

        // Filter by obat
        if ($request->has('obat') && $request->obat != '') {
            $query->whereHas('obat', function ($q) use ($request) {
                $q->where('nama_obat', 'like', '%' . $request->obat . '%');
            });
        }

        // Filter by jenis obat
        if ($request->has('jenis_obat') && $request->jenis_obat != '') {
            $query->whereHas('obat', function ($q) use ($request) {
                $q->where('id_jenis_obat', $request->jenis_obat);
            });
        }

        // Filter by stok status
        if ($request->has('stok_status') && $request->stok_status != '') {
            switch ($request->stok_status) {
                case 'habis':
                    $query->where('stok_akhir', '<=', 0);
                    break;
                case 'rendah':
                    $query->where('stok_akhir', '>', 0)->where('stok_akhir', '<=', 10);
                    break;
                case 'tersedia':
                    $query->where('stok_akhir', '>', 10);
                    break;
            }
        }

        // Sorting
        $sortField = $request->get('sort', 'id_stok_bulanan');
        $sortDirection = $request->get('direction', 'desc');

        if (in_array($sortField, ['periode', 'nama_obat', 'jenis_obat', 'stok_awal', 'stok_pakai', 'stok_akhir', 'stok_masuk'])) {
            if ($sortField === 'nama_obat') {
                $query->join('obat', 'stok_bulanan.id_obat', '=', 'obat.id_obat')
                      ->orderBy('obat.nama_obat', $sortDirection)
                      ->select('stok_bulanan.*');
            } elseif ($sortField === 'jenis_obat') {
                $query->join('obat', 'stok_bulanan.id_obat', '=', 'obat.id_obat')
                      ->join('jenis_obat', 'obat.id_jenis_obat', '=', 'jenis_obat.id_jenis_obat')
                      ->orderBy('jenis_obat.nama_jenis_obat', $sortDirection)
                      ->select('stok_bulanan.*');
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            $query->orderBy('periode', 'desc')
                  ->join('obat', 'stok_bulanan.id_obat', '=', 'obat.id_obat')
                  ->orderBy('obat.nama_obat', 'asc')
                  ->select('stok_bulanan.*');
        }

        // Pagination
        $perPage = $request->get('per_page', 50);
        $perPage = in_array($perPage, [50, 100, 150, 200]) ? $perPage : 50;

        $stokBulanans = $query->paginate($perPage);

        // Get available periodes for filter
        $availablePeriodes = StokBulanan::getAvailablePeriodes();

        // Get reference data
        $jenisObats = Cache::remember('jenis_obats_all', 60, function () {
            return JenisObat::get();
        });

        return view('stok-bulanan.index', compact(
            'stokBulanans',
            'availablePeriodes',
            'jenisObats',
            'request'
        ));
    }

    /**
     * Export data stok bulanan to Excel dengan format periode horizontal
     */
    public function export(Request $request)
    {
        $query = StokBulanan::with([
            'obat:id_obat,nama_obat,keterangan,id_jenis_obat,id_satuan',
            'obat.jenisObat:id_jenis_obat,nama_jenis_obat',
            'obat.satuanObat:id_satuan,nama_satuan'
        ]);

        // Apply same filters as index
        if ($request->has('periode') && $request->periode != '') {
            $query->where('periode', $request->periode);
        }
        if ($request->has('periode_start') && $request->periode_start != '') {
            $query->where('periode', '>=', $request->periode_start);
        }
        if ($request->has('periode_end') && $request->periode_end != '') {
            $query->where('periode', '<=', $request->periode_end);
        }
        if ($request->has('obat') && $request->obat != '') {
            $query->whereHas('obat', function ($q) use ($request) {
                $q->where('nama_obat', 'like', '%' . $request->obat . '%');
            });
        }
        if ($request->has('jenis_obat') && $request->jenis_obat != '') {
            $query->whereHas('obat', function ($q) use ($request) {
                $q->where('id_jenis_obat', $request->jenis_obat);
            });
        }
        if ($request->has('stok_status') && $request->stok_status != '') {
            switch ($request->stok_status) {
                case 'habis':
                    $query->where('stok_akhir', '<=', 0);
                    break;
                case 'rendah':
                    $query->where('stok_akhir', '>', 0)->where('stok_akhir', '<=', 10);
                    break;
                case 'tersedia':
                    $query->where('stok_akhir', '>', 10);
                    break;
            }
        }

        // Get data
        $data = $query->join('obat', 'stok_bulanan.id_obat', '=', 'obat.id_obat')
                     ->orderBy('obat.nama_obat', 'asc')
                     ->orderBy('stok_bulanan.periode', 'asc')
                     ->select('stok_bulanan.*')
                     ->get();

        // Group data by obat
        $groupedData = $data->groupBy('id_obat');

        // Get all unique periodes
        $periodes = $data->pluck('periode')->unique()->sort()->values()->toArray();

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Stok Bulanan');

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('SIPO ICBP')
            ->setTitle('Data Stok Bulanan')
            ->setSubject('Data Stok Bulanan')
            ->setDescription('Data stok obat perbulan');

        // Header columns - Format dengan periode horizontal
        $headers = [
            'No', 'Nama Obat', 'Satuan', 'Kegunaan', 'Jenis / Golongan Obat'
        ];

        // Add periode headers
        foreach ($periodes as $periode) {
            $headers[] = $periode . ' Awal';
            $headers[] = $periode . ' Pakai';
            $headers[] = $periode . ' Akhir';
            $headers[] = $periode . ' Masuk';
        }

        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        // Calculate last column index properly
        $lastColumnIndex = count($headers) - 1;
        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColumnIndex + 1);
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray($headerStyle);

        // Fill data
        $row = 2;
        $no = 1;

        foreach ($groupedData as $obatId => $items) {
            $obat = $items->first()->obat;

            // Basic info
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $obat->nama_obat);
            $sheet->setCellValue('C' . $row, $obat->satuanObat->nama_satuan ?? '');
            $sheet->setCellValue('D' . $row, $obat->keterangan ?? '');
            $sheet->setCellValue('E' . $row, $obat->jenisObat->nama_jenis_obat ?? '');

            // Create a map of periode to stok data for this obat
            $stokByPeriode = [];
            foreach ($items as $item) {
                $stokByPeriode[$item->periode] = $item;
            }

            // Fill stok data for each periode
            $colIndex = 5; // Start from column F (index 5)
            foreach ($periodes as $periode) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                $colLetter1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 2);
                $colLetter2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 3);
                $colLetter3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 4);

                if (isset($stokByPeriode[$periode])) {
                    $stok = $stokByPeriode[$periode];
                    $sheet->setCellValue($colLetter . $row, $stok->stok_awal);
                    $sheet->setCellValue($colLetter1 . $row, $stok->stok_pakai);
                    $sheet->setCellValue($colLetter2 . $row, $stok->stok_akhir);
                    $sheet->setCellValue($colLetter3 . $row, $stok->stok_masuk);
                } else {
                    // Empty if no data for this periode
                    $sheet->setCellValue($colLetter . $row, '-');
                    $sheet->setCellValue($colLetter1 . $row, '-');
                    $sheet->setCellValue($colLetter2 . $row, '-');
                    $sheet->setCellValue($colLetter3 . $row, '-');
                }
                $colIndex += 4;
            }

            // Style data rows
            $dataStyle = [
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ];

            $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->applyFromArray($dataStyle);

            $row++;
            $no++;
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(20);

        // Set width for periode columns
        for ($i = 5; $i < count($headers); $i++) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->getColumnDimension($columnLetter)->setWidth(12);
        }

        // Set row heights for headers
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Add instructions sheet
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Petunjuk Import');

        // Add instructions
        $instructions = [
            'Petunjuk Import Data Stok Bulanan:',
            '',
            '1. Format File:',
            '   - Gunakan file CSV atau Excel (.csv, .xlsx, .xls)',
            '   - Pastikan format kolom sesuai dengan template',
            '',
            '2. Struktur Kolom:',
            '   - No: Nomor urut',
            '   - Nama Obat: Nama obat yang sudah ada di sistem',
            '   - Satuan: Satuan obat',
            '   - Kegunaan: Keterangan penggunaan obat',
            '   - Jenis / Golongan Obat: Kategori obat',
            '   - Periode: Format MM-YY (contoh: 01-25 untuk Januari 2025)',
            '   - Setiap periode memiliki 4 kolom: Awal, Pakai, Akhir, Masuk',
            '',
            '3. Ketentuan:',
            '   - Pastikan nama obat sudah terdaftar di sistem',
            '   - Format periode harus MM-YY',
            '   - Isi hanya dengan angka pada kolom stok',
            '   - Gunakan - untuk nilai kosong',
            '   - Untuk nilai negatif, gunakan format (60) = -60',
            '',
            '4. Contoh Data:',
            '   1, Paracetamol, Tablet, Obat demam, Analgetik, 100, 20, 80, 50, 01-25 Awal, 01-25 Pakai, 01-25 Akhir, 01-25 Masuk',
        ];

        $row = 1;
        foreach ($instructions as $instruction) {
            $sheet->setCellValue('A' . $row, $instruction);
            $row++;
        }

        // Style instructions
        $sheet->getStyle('A1:A' . ($row - 1))->getFont()->setSize(11);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getColumnDimension('A')->setWidth(80);

        // Set active sheet back to data
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'data_stok_bulanan_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $stokBulanan = StokBulanan::findOrFail($id);
        $stokBulanan->delete();

        return response()->json(['success' => true, 'message' => 'Data stok bulanan berhasil dihapus']);
    }

    /**
     * Bulk delete stok bulanan
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih'], 400);
        }

        StokBulanan::whereIn('id_stok_bulanan', $ids)->delete();

        return response()->json(['success' => true, 'message' => count($ids) . ' data stok bulanan berhasil dihapus']);
    }
}
