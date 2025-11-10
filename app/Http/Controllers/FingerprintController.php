<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keluarga;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class FingerprintController extends Controller
{
    /**
     * Display the fingerprint management page.
     */
    public function index()
    {
        $keluargaList = Keluarga::with(['karyawan', 'hubungan'])
            ->whereNotNull('fingerprint_template')
            ->get();

        $allKeluarga = Keluarga::with(['karyawan', 'hubungan'])
            ->get();

        return view('fingerprint.index', compact('keluargaList', 'allKeluarga'));
    }

    /**
     * Capture fingerprint from SecuGen device.
     */
    public function captureFingerprint(Request $request)
    {
        try {
            $params = [
                'Timeout' => '10000',
                'Quality' => '50',
                'licstr' => '',
                'templateFormat' => 'ISO',
                'imageWSQRate' => '0.75'
            ];

            $response = Http::asForm()->post('https://localhost:8443/SGIFPCapture', $params);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'HTTP ' . $response->status()
                ], 400);
            }

            $data = $response->json();

            if ($data['ErrorCode'] === 0) {
                return response()->json([
                    'success' => true,
                    'template' => $data['TemplateBase64'],
                    'image' => $data['BMPBase64'],
                    'quality' => $data['ImageQuality'],
                    'nfiq' => $data['NFIQ'],
                    'message' => "Sidik jari berhasil ditangkap! Kualitas: {$data['ImageQuality']}, NFIQ: {$data['NFIQ']}"
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $this->getErrorDescription($data['ErrorCode']),
                    'error_code' => $data['ErrorCode']
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error koneksi: ' . $e->getMessage() . '. Pastikan SGIBIOSRV berjalan di port 8443'
            ], 500);
        }
    }

    /**
     * Enroll fingerprint for a family member.
     */
    public function enrollFingerprint(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_keluarga' => 'required|exists:keluarga,id_keluarga',
            'fingerprint_template' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        try {
            $keluarga = Keluarga::findOrFail($request->id_keluarga);

            $keluarga->update([
                'fingerprint_template' => $request->fingerprint_template,
                'fingerprint_enrolled_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Fingerprint untuk {$keluarga->nama_keluarga} berhasil didaftarkan!"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan fingerprint: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify fingerprint against enrolled templates.
     */
    public function verifyFingerprint(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fingerprint_template' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        try {
            $enrolledKeluarga = Keluarga::whereNotNull('fingerprint_template')->get();

            if ($enrolledKeluarga->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belum ada fingerprint yang terdaftar!'
                ], 400);
            }

            $bestMatch = null;
            $bestScore = 0;

            foreach ($enrolledKeluarga as $keluarga) {
                try {
                    $params = [
                        'Template1' => $request->fingerprint_template,
                        'Template2' => $keluarga->fingerprint_template,
                        'licstr' => '',
                        'templateFormat' => 'ISO'
                    ];

                    $response = Http::asForm()->post('https://localhost:8443/SGIMatchScore', $params);

                    if ($response->successful()) {
                        $data = $response->json();

                        if ($data['ErrorCode'] === 0 && $data['MatchingScore'] > $bestScore) {
                            $bestScore = $data['MatchingScore'];
                            $bestMatch = $keluarga;
                        }
                    }
                } catch (\Exception $e) {
                    continue; // Skip to next template if error occurs
                }
            }

            if ($bestScore > 100 && $bestMatch) {
                return response()->json([
                    'success' => true,
                    'message' => "✓ Verifikasi Berhasil! {$bestMatch->nama_keluarga} (Score: {$bestScore}/199)",
                    'keluarga' => $bestMatch->load(['karyawan', 'hubungan']),
                    'score' => $bestScore
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "✗ Sidik jari tidak cocok. Score tertinggi: {$bestScore}/199",
                    'score' => $bestScore
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error verifikasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove fingerprint from family member.
     */
    public function removeFingerprint($id_keluarga)
    {
        try {
            $keluarga = Keluarga::findOrFail($id_keluarga);

            if (!$keluarga->fingerprint_template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anggota keluarga ini tidak memiliki fingerprint terdaftar'
                ], 400);
            }

            $keluarga->update([
                'fingerprint_template' => null,
                'fingerprint_enrolled_at' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => "Fingerprint untuk {$keluarga->nama_keluarga} berhasil dihapus"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus fingerprint: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get error description from error code.
     */
    private function getErrorDescription($code)
    {
        $errors = [
            51 => 'System file load failure',
            52 => 'Sensor chip initialization failed',
            53 => 'Device not found',
            54 => 'Fingerprint image capture timeout',
            55 => 'No device available',
            56 => 'Driver load failed',
            57 => 'Wrong Image',
            58 => 'Lack of bandwidth',
            59 => 'Device Busy',
            60 => 'Cannot get serial number',
            61 => 'Unsupported device',
            63 => 'SgiBioSrv tidak berjalan'
        ];

        return $errors[$code] ?? 'Unknown error';
    }
}
