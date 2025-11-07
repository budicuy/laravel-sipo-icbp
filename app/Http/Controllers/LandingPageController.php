<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LandingPageController extends Controller
{
    /**
     * Display the landing page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('landing.index');
    }

    /**
     * Handle AI chat request with Gemini API
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array',
            'history.*.role' => 'required|string|in:user,model',
            'history.*.text' => 'required|string',
        ]);

        // Try fallback response first for common questions
        $fallbackReply = $this->getFallbackResponse($request->message);
        if ($fallbackReply) {
            return response()->json([
                'success' => true,
                'reply' => $fallbackReply,
            ]);
        }

        $apiKey = config('gemini.api_key');

        // Check if API key is configured
        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'reply' => 'Maaf, AI Assistant belum dikonfigurasi. Untuk informasi lebih lanjut, hubungi kami di Call Center +62 800 1122 888.',
            ], 500);
        }

        try {
            $model = config('gemini.model', 'gemini-1.5-flash-latest');
            $endpoint = config('gemini.api_endpoint');

            // System prompt untuk konteks AI
            $systemPrompt = 'Anda adalah AI Assistant resmi untuk SIPO ICBP (Sistem Informasi Poliklinik ICBP) - PT. Indofood CBP Sukses Makmur Tbk.

IDENTITAS SISTEM:
- Nama: SIPO ICBP (Sistem Informasi Poliklinik ICBP)
- Perusahaan: PT. Indofood CBP Sukses Makmur Tbk
- Lokasi: Jalan Ayani KM. 32 Liang Anggang, Pandahan, Kec. Bati Bati, Kabupaten Tanah Laut, Kalimantan Selatan 70723
- Fungsi: Sistem manajemen pelayanan kesehatan karyawan berbasis digital

FITUR UTAMA SIPO ICBP:
1. **Rekam Medis Digital**: Pencatatan lengkap riwayat kesehatan karyawan secara elektronik dan aman
2. **Manajemen Obat**: Tracking stok obat otomatis, alert stok menipis, riwayat penggunaan obat terperinci
3. **AI Assistant**: Chat assistant powered by Google Gemini untuk bantuan 24/7
4. **Surat Keterangan Kesehatan**: Generate otomatis surat sakit, rujukan, dan dokumen medis
5. **Laporan & Analitik**: Dashboard komprehensif dengan visualisasi data kesehatan dan tren penyakit
6. **Keamanan Data**: Enkripsi data, role-based access control, dan audit trail lengkap

KONTAK INFORMASI:
- Telepon: (+62-21) 5795 8822
- Fax: (+62-21) 5793 5960
- Call Center 24/7: +62 800 1122 888
- WhatsApp: +62 889 1122 888
- Email: corporate@indofood.co.id

PANDUAN MENJAWAB:
1. Gunakan formatting markdown untuk readability (### untuk heading, **bold** untuk emphasis, - untuk bullet points, 1. untuk numbered lists)
2. Jawab dengan struktur yang jelas: Greeting → Jawaban Inti → Detail Pendukung → Call-to-Action (jika perlu)
3. Selalu profesional, ramah, dan informatif dalam Bahasa Indonesia
4. Jika ditanya tentang fitur, jelaskan manfaat konkret untuk pengguna
5. Untuk pertanyaan teknis login/akses, arahkan ke administrator atau call center
6. Gunakan konteks percakapan sebelumnya untuk jawaban yang lebih relevan dan koheren
7. Jika tidak yakin atau pertanyaan di luar scope SIPO ICBP, arahkan ke kontak resmi
8. Sertakan emoji yang relevan untuk membuat komunikasi lebih friendly (tapi tidak berlebihan)

PANDUAN KONSULTASI KESEHATAN:

**BOLEH DIJAWAB - Kondisi Ringan sampai Sedang:**
Anda BOLEH memberikan informasi umum dan saran first-aid untuk kondisi ringan-sedang seperti:

✅ **Kondisi Ringan:**
- Sakit kepala ringan (tension headache)
- Pilek/flu biasa tanpa demam tinggi
- Batuk ringan
- Sakit tenggorokan ringan
- Kelelahan/lelah biasa
- Pusing ringan
- Nyeri otot ringan (dari aktivitas)
- Mual ringan
- Luka lecet/gores kecil
- Gigitan nyamuk/serangga (non-venomous)

✅ **Kondisi Sedang (dengan catatan konsultasi lebih lanjut):**
- Demam ringan (<38.5°C) yang baru muncul
- Diare tanpa darah (1-2 hari)
- Sembelit ringan
- Nyeri perut ringan (non-akut)
- Maag/asam lambung ringan
- Insomnia sesekali
- Stres ringan/anxiety ringan
- Alergi ringan (gatal, bersin)

**Format Jawaban untuk Kondisi Ringan-Sedang:**
1. Berikan informasi umum tentang kondisi
2. Saran perawatan mandiri (self-care) yang aman
3. Tanda kapan harus ke dokter
4. **SELALU** tambahkan disclaimer: "Ini adalah informasi umum. Jika gejala memburuk atau tidak membaik dalam 2-3 hari, segera konsultasi dengan dokter di klinik perusahaan."

❌ **WAJIB REDIRECT KE DOKTER - Kondisi Berat/Serius:**
Untuk kondisi berikut, JANGAN berikan saran medis, LANGSUNG redirect ke dokter/emergency:

- Nyeri dada atau sesak napas
- Demam tinggi (>38.5°C) atau demam berkepanjangan (>3 hari)
- Pendarahan yang tidak berhenti
- Trauma kepala/cedera berat
- Kehilangan kesadaran atau pingsan
- Nyeri perut akut/hebat
- Diare berdarah atau muntah darah
- Gejala stroke (wajah miring, bicara pelo, lemah sebelah)
- Reaksi alergi berat (pembengkakan wajah/lidah, sulit napas)
- Kejang
- Nyeri/bengkak pada satu kaki (potensi DVT)
- Penurunan berat badan drastis tanpa sebab
- Gejala infeksi berat (demam + menggigil + lemas)
- Gangguan mental akut (halusinasi, ide bunuh diri)
- Kehamilan dengan komplikasi
- Diabetes tidak terkontrol
- Penyakit kronis yang memburuk

**Format Jawaban untuk Kondisi Berat:**
"Berdasarkan gejala yang Anda sebutkan, ini termasuk kondisi yang memerlukan evaluasi medis profesional segera. Saya sangat menyarankan Anda untuk:

🏥 **SEGERA konsultasi dengan dokter** di klinik perusahaan atau fasilitas kesehatan terdekat
📞 **Hubungi Call Center kami**: +62 800 1122 888 untuk bantuan medis
🚨 **Jika darurat**: Hubungi ambulans 118/119 atau datang ke IGD terdekat

Jangan tunda penanganan medis untuk gejala ini. Kesehatan dan keselamatan Anda adalah prioritas utama."

BATASAN UMUM:
- JANGAN memberikan diagnosa pasti atau resep obat
- JANGAN menyarankan pengobatan untuk kondisi kronis tanpa konsultasi dokter
- JANGAN memberikan kredensial login atau data sensitif
- JANGAN menjanjikan fitur yang tidak ada di sistem
- FOKUS pada informasi umum sistem, fitur, panduan penggunaan, dan first-aid advice untuk kondisi ringan

SELALU GUNAKAN MEDICAL DISCLAIMER:
"⚠️ **Disclaimer**: Informasi ini bersifat umum dan tidak menggantikan konsultasi medis profesional. Setiap individu memiliki kondisi kesehatan yang unik. Untuk diagnosis dan perawatan yang tepat, silakan konsultasi dengan dokter."

Jawab pertanyaan dengan akurat, empati, dan bertanggung jawab berdasarkan panduan di atas.';

            // Build conversation history
            $contents = [];

            // Add chat history if exists
            $history = $request->input('history', []);
            if (! empty($history)) {
                foreach ($history as $message) {
                    $contents[] = [
                        'role' => $message['role'],
                        'parts' => [
                            ['text' => $message['text']],
                        ],
                    ];
                }
            }

            // Add current user message
            $contents[] = [
                'role' => 'user',
                'parts' => [
                    ['text' => $systemPrompt."\n\nPertanyaan: ".$request->message],
                ],
            ];

            // Call Gemini API
            $response = Http::timeout(30)->post("{$endpoint}/{$model}:generateContent?key={$apiKey}", [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => (float) config('gemini.temperature', 0.7),
                    'maxOutputTokens' => (int) config('gemini.max_tokens', 1024),
                    'topP' => (float) config('gemini.top_p', 0.95),
                    'topK' => (int) config('gemini.top_k', 40),
                ],
            ]);

            if ($response->successful()) {
                $result = $response->json();

                // Extract AI reply from response
                $aiReply = $result['candidates'][0]['content']['parts'][0]['text']
                    ?? 'Maaf, saya tidak dapat memproses permintaan Anda saat ini.';

                return response()->json([
                    'success' => true,
                    'reply' => $aiReply,
                ]);
            } else {
                $statusCode = $response->status();
                $responseBody = $response->json();

                Log::error('Gemini API Error', [
                    'status' => $statusCode,
                    'body' => $response->body(),
                ]);

                // Handle specific error codes
                $errorMessage = 'Maaf, terjadi kesalahan saat menghubungi AI Assistant. Silakan coba lagi nanti.';

                if ($statusCode === 429) {
                    // Rate limit or quota exceeded
                    $errorMessage = 'Maaf, AI Assistant sedang sibuk. Silakan coba lagi dalam beberapa saat. '
                        .'Atau hubungi kami melalui telepon/email yang tersedia di bawah halaman.';
                } elseif ($statusCode === 401 || $statusCode === 403) {
                    // Authentication error
                    $errorMessage = 'Maaf, AI Assistant belum tersedia saat ini. '
                        .'Silakan hubungi administrator atau gunakan kontak lain yang tersedia.';
                } elseif ($statusCode === 400) {
                    // Bad request
                    $errorMessage = 'Maaf, permintaan tidak valid. Silakan coba dengan pertanyaan yang berbeda.';
                }

                return response()->json([
                    'success' => false,
                    'reply' => $errorMessage,
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Gemini API Exception: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'reply' => 'Maaf, AI Assistant sedang tidak tersedia. Silakan coba lagi nanti atau hubungi administrator.',
            ], 500);
        }
    }

    /**
     * Get fallback response for common questions
     *
     * @param  string  $message
     * @return string|null
     */
    private function getFallbackResponse($message)
    {
        $lowercaseMsg = strtolower($message);

        $responses = [
            'sipo' => 'SIPO ICBP adalah Sistem Informasi Poliklinik untuk Indofood. Sistem ini dirancang untuk mengelola data kesehatan karyawan dengan efisien dan modern. 🏥',
            'apa itu sipo' => 'SIPO ICBP adalah Sistem Informasi Poliklinik untuk Indofood yang membantu mengelola rekam medis, obat, dan layanan kesehatan karyawan secara digital.',
            'fitur' => 'SIPO ICBP memiliki fitur unggulan: 📋 Rekam Medis Digital, 💊 Manajemen Obat, 🤖 AI Assistant, 📄 Surat Keterangan, 📊 Laporan & Analitik, dan 🔒 Keamanan Data yang terjamin.',
            'login' => 'Untuk login ke sistem SIPO ICBP, klik tombol "Login" di pojok kanan atas halaman. Gunakan kredensial yang telah diberikan oleh administrator. 🔐',
            'kontak' => 'Anda dapat menghubungi kami melalui:\n📞 Telepon: (+62-21) 5795 8822\n📠 Fax: (+62-21) 5793 5960\n🎧 Call Center: +62 800 1122 888\n💬 WhatsApp: +62 889 1122 888\n📧 Email: corporate@indofood.co.id',
            'hubungi' => 'Silakan hubungi kami di:\n📞 Call Center: +62 800 1122 888\n💬 WhatsApp: +62 889 1122 888\n📧 Email: corporate@indofood.co.id\nKami siap membantu Anda 24/7! ✨',
            'telepon' => 'Nomor telepon kami: (+62-21) 5795 8822. Call Center 24/7: +62 800 1122 888. WhatsApp: +62 889 1122 888',
            'email' => 'Email kami: corporate@indofood.co.id. Kami akan merespons email Anda sesegera mungkin. 📧',
            'alamat' => 'Alamat kami: Jalan Ayani KM. 32 Liang Anggang, Pandahan, Kec. Bati Bati, Kabupaten Tanah Laut, Kalimantan Selatan 70723. 📍',
            'rekam medis' => 'Fitur Rekam Medis Digital memungkinkan pencatatan lengkap riwayat kesehatan karyawan secara aman dan mudah diakses. Semua data terenkripsi untuk keamanan maksimal. 🏥',
            'obat' => 'Sistem Manajemen Obat kami membantu mengelola stok obat dengan tracking otomatis, alert stok menipis, dan riwayat penggunaan yang terperinci. 💊',
            'laporan' => 'Dashboard Laporan & Analitik menyediakan visualisasi data kesehatan, tren penyakit, dan laporan keuangan secara komprehensif. 📊',
            'keamanan' => 'Sistem kami dilengkapi keamanan berlapis dengan enkripsi data, role-based access control, dan audit trail lengkap untuk melindungi informasi kesehatan Anda. 🔒',
            'jam kerja' => 'Sistem SIPO ICBP dapat diakses 24/7. Untuk layanan customer support, hubungi Call Center kami di +62 800 1122 888 yang tersedia 24 jam. ⏰',
        ];

        // Check for exact or partial matches
        foreach ($responses as $keyword => $response) {
            if (strpos($lowercaseMsg, $keyword) !== false) {
                return $response;
            }
        }

        return null;
    }
}
