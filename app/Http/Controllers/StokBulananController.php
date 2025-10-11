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
     * Export data stok bulanan to Excel
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

        // Order by periode and nama obat
        $query->orderBy('periode', 'desc')
              ->join('obat', 'stok_bulanan.id_obat', '=', 'obat.id_obat')
              ->orderBy('obat.nama_obat', 'asc')
              ->select('stok_bulanan.*');

        $data = $query->get();

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Stok Bulanan');

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('SIPO ICBP')
            ->setTitle('Laporan Stok Bulanan')
            ->setSubject('Laporan Stok Bulanan')
            ->setDescription('Laporan data stok obat perbulan');

        // Header columns
        $headers = [
            'No',
            'Nama Obat',
            'Satuan',
            'Jenis Obat',
            'Periode',
            'Stok Awal',
            'Stok Pakai',
            'Stok Masuk',
            'Stok Akhir',
            'Keterangan'
        ];

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

        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

        // Fill data
        $row = 2;
        $no = 1;

        // Group data by periode
        $groupedData = $data->groupBy('periode');

        foreach ($groupedData as $periode => $items) {
            // Add periode header
            $sheet->mergeCells('A' . $row . ':J' . $row);
            $sheet->setCellValue('A' . $row, 'PERIODE: ' . $items->first()->periode_format);

            $periodeStyle = [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ];

            $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($periodeStyle);
            $row++;

            foreach ($items as $item) {
                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, $item->obat->nama_obat);
                $sheet->setCellValue('C' . $row, $item->obat->satuanObat->nama_satuan ?? '');
                $sheet->setCellValue('D' . $row, $item->obat->jenisObat->nama_jenis_obat ?? '');
                $sheet->setCellValue('E' . $row, $item->periode);
                $sheet->setCellValue('F' . $row, $item->stok_awal);
                $sheet->setCellValue('G' . $row, $item->stok_pakai);
                $sheet->setCellValue('H' . $row, $item->stok_masuk);
                $sheet->setCellValue('I' . $row, $item->stok_akhir);
                $sheet->setCellValue('J' . $row, $item->obat->keterangan ?? '');

                // Style data rows
                $dataStyle = [
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ];

                // Color code based on stock status
                if ($item->stok_akhir <= 0) {
                    $dataStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEE2E2']];
                } elseif ($item->stok_akhir <= 10) {
                    $dataStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']];
                }

                $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($dataStyle);

                $row++;
                $no++;
            }

            // Add summary for this period
            $totalAwal = $items->sum('stok_awal');
            $totalPakai = $items->sum('stok_pakai');
            $totalMasuk = $items->sum('stok_masuk');
            $totalAkhir = $items->sum('stok_akhir');

            $sheet->mergeCells('A' . $row . ':E' . $row);
            $sheet->setCellValue('A' . $row, 'TOTAL PERIODE ' . $periode);
            $sheet->setCellValue('F' . $row, $totalAwal);
            $sheet->setCellValue('G' . $row, $totalPakai);
            $sheet->setCellValue('H' . $row, $totalMasuk);
            $sheet->setCellValue('I' . $row, $totalAkhir);
            $sheet->setCellValue('J' . $row, '');

            $summaryStyle = [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ];

            $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($summaryStyle);
            $sheet->getStyle('A' . $row . ':E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(12);
        $sheet->getColumnDimension('I')->setWidth(12);
        $sheet->getColumnDimension('J')->setWidth(40);

        // Set row heights for headers
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Add footer info
        $row += 2;
        $sheet->setCellValue('A' . $row, 'Dicetak pada: ' . date('d/m/Y H:i:s'));
        $sheet->setCellValue('A' . ($row + 1), 'Total Data: ' . $data->count() . ' obat');

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan_stok_bulanan_' . date('Y-m-d_H-i-s') . '.xlsx';

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
