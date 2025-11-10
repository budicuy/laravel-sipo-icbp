<?php

namespace App\Http\Controllers;

use App\Models\Keluarga;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FingerprintController extends Controller
{
    /**
     * Display fingerprint management page
     */
    public function index()
    {
        $keluargas = Keluarga::whereNotNull('fingerprint_template')
            ->with('karyawan')
            ->orderBy('fingerprint_enrolled_at', 'desc')
            ->get();

        return view('fingerprint.index', compact('keluargas'));
    }

    /**
     * Get all family members for fingerprint enrollment
     */
    public function getFamilyMembers(): JsonResponse
    {
        $keluargas = Keluarga::with('karyawan')
            ->orderBy('nama_keluarga')
            ->get();

        return response()->json($keluargas);
    }

    /**
     * Save fingerprint template for family member
     */
    public function saveFingerprint(Request $request): JsonResponse
    {
        $request->validate([
            'id_keluarga' => 'required|exists:keluarga,id_keluarga',
            'fingerprint_template' => 'required|string'
        ]);

        $keluarga = Keluarga::find($request->id_keluarga);
        $keluarga->fingerprint_template = $request->fingerprint_template;
        $keluarga->fingerprint_enrolled_at = now();
        $keluarga->save();

        return response()->json([
            'success' => true,
            'message' => 'Fingerprint berhasil disimpan untuk ' . $keluarga->nama_keluarga,
            'data' => $keluarga->load('karyawan')
        ]);
    }

    /**
     * Get all fingerprint templates for verification
     */
    public function getFingerprintTemplates(): JsonResponse
    {
        $templates = Keluarga::whereNotNull('fingerprint_template')
            ->with('karyawan')
            ->get(['id_keluarga', 'nama_keluarga', 'fingerprint_template', 'id_karyawan']);

        return response()->json($templates);
    }

    /**
     * Delete fingerprint template
     */
    public function deleteFingerprint(Request $request): JsonResponse
    {
        $request->validate([
            'id_keluarga' => 'required|exists:keluarga,id_keluarga'
        ]);

        $keluarga = Keluarga::find($request->id_keluarga);
        $keluarga->fingerprint_template = null;
        $keluarga->fingerprint_enrolled_at = null;
        $keluarga->save();

        return response()->json([
            'success' => true,
            'message' => 'Fingerprint berhasil dihapus untuk ' . $keluarga->nama_keluarga
        ]);
    }
}
