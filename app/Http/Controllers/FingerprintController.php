<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FingerprintController extends Controller
{
    /**
     * Display fingerprint management page
     */
    public function index(Request $request)
    {
        $query = Karyawan::whereNotNull('fingerprint_template')
            ->with('departemen');

        // Handle sorting
        $sort = $request->get('sort', 'fingerprint_enrolled_at');
        $direction = $request->get('direction', 'desc');

        switch ($sort) {
            case 'nama_karyawan':
                $query->orderBy('nama_karyawan', $direction);
                break;
            case 'nik_karyawan':
                $query->orderBy('nik_karyawan', $direction);
                break;
            case 'departemen.nama_departemen':
                $query->orderByHas('departemen', function ($q) use ($direction) {
                    $q->orderBy('nama_departemen', $direction);
                });
                break;
            case 'fingerprint_enrolled_at':
            default:
                $query->orderBy('fingerprint_enrolled_at', $direction);
                break;
        }

        $karyawans = $query->get();

        return view('fingerprint.index', compact('karyawans'));
    }

    /**
     * Get all employees for fingerprint enrollment
     */
    public function getEmployees(): JsonResponse
    {
        $karyawans = Karyawan::with('departemen')
            ->orderBy('nama_karyawan')
            ->get();

        return response()->json($karyawans);
    }

    /**
     * Search employees by name or NIK
     */
    public function searchEmployees(Request $request): JsonResponse
    {
        $search = $request->get('search', '');

        $karyawans = Karyawan::with('departemen')
            ->where(function($query) use ($search) {
                $query->where('nama_karyawan', 'like', '%' . $search . '%')
                      ->orWhere('nik_karyawan', 'like', '%' . $search . '%');
            })
            ->orderBy('nama_karyawan')
            ->limit(20)
            ->get();

        return response()->json($karyawans);
    }

    /**
     * Save fingerprint template for employee
     */
    public function saveFingerprint(Request $request): JsonResponse
    {
        $request->validate([
            'id_karyawan' => 'required|exists:karyawan,id_karyawan',
            'fingerprint_template' => 'required|string'
        ]);

        $karyawan = Karyawan::find($request->id_karyawan);
        $karyawan->fingerprint_template = $request->fingerprint_template;
        $karyawan->fingerprint_enrolled_at = now();
        $karyawan->save();

        return response()->json([
            'success' => true,
            'message' => 'Fingerprint berhasil disimpan untuk ' . $karyawan->nama_karyawan,
            'data' => $karyawan->load('departemen')
        ]);
    }

    /**
     * Get all fingerprint templates for verification
     */
    public function getFingerprintTemplates(): JsonResponse
    {
        $templates = Karyawan::whereNotNull('fingerprint_template')
            ->with('departemen')
            ->get(['id_karyawan', 'nama_karyawan', 'nik_karyawan', 'fingerprint_template', 'id_departemen']);

        return response()->json($templates);
    }

    /**
     * Delete fingerprint template
     */
    public function deleteFingerprint(Request $request): JsonResponse
    {
        $request->validate([
            'id_karyawan' => 'required|exists:karyawan,id_karyawan'
        ]);

        $karyawan = Karyawan::find($request->id_karyawan);
        $karyawan->fingerprint_template = null;
        $karyawan->fingerprint_enrolled_at = null;
        $karyawan->save();

        return response()->json([
            'success' => true,
            'message' => 'Fingerprint berhasil dihapus untuk ' . $karyawan->nama_karyawan
        ]);
    }
}
