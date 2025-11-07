<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
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
     * Check NIK and return employee data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkNik(Request $request)
    {
        $request->validate([
            'nik' => 'required|string',
            'password' => 'required|string',
        ]);

        $nik = $request->nik;
        $password = $request->password;

        // Validate NIK format (must be numeric)
        if (! preg_match('/^\d+$/', $nik)) {
            return response()->json([
                'success' => false,
                'message' => 'NIK harus berupa angka saja',
            ], 400);
        }

        // Validate password matches NIK
        if ($password !== $nik) {
            return response()->json([
                'success' => false,
                'message' => 'Password harus sama dengan NIK Anda',
            ], 400);
        }

        // Find employee by NIK
        $karyawan = Karyawan::where('nik_karyawan', $nik)->first();

        if (! $karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'NIK tidak ditemukan dalam database',
            ], 404);
        }

        // Return employee data
        return response()->json([
            'success' => true,
            'data' => [
                'nik' => $karyawan->nik_karyawan,
                'nama' => $karyawan->nama_karyawan,
                'departemen' => $karyawan->departemen->nama_departemen ?? 'Tidak ada departemen',
            ],
        ]);
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
            'user_nik' => 'nullable|string',
            'user_name' => 'nullable|string',
        ]);

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

            // Get user info if provided
            $userNik = $request->input('user_nik');
            $userName = $request->input('user_name');

            // Build user context
            $userContext = '';
            if ($userName && $userNik) {
                $userContext = "\n\n**INFORMASI USER YANG SEDANG BERBICARA:**\n";
                $userContext .= "- Nama: {$userName}\n";
                $userContext .= "- NIK: {$userNik}\n";
                $userContext .= "\nGunakan informasi ini untuk menyapa user secara personal dengan nama mereka. ";
                $userContext .= "HANYA gunakan nama dan NIK untuk personalisasi sapaan. ";
                $userContext .= "JANGAN akses atau tampilkan data pribadi/medis lainnya seperti riwayat kesehatan, alamat, atau informasi sensitif.\n";
            }

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

'.$userContext.'

KONTAK INFORMASI:
- Telepon: (+62-21) 5795 8822
- Fax: (+62-21) 5793 5960
- Call Center 24/7: +62 800 1122 888
- WhatsApp: +62 889 1122 888
- Email: corporate@indofood.co.id

PANDUAN MENJAWAB:
1. **FORMAT OUTPUT: GUNAKAN HTML** - Semua response harus dalam format HTML yang valid
2. Jawab dengan struktur yang jelas: Greeting → Jawaban Inti → Detail Pendukung → Call-to-Action (jika perlu)
3. Selalu profesional, ramah, dan informatif dalam Bahasa Indonesia
4. Jika ditanya tentang fitur, jelaskan manfaat konkret untuk pengguna
5. Untuk pertanyaan teknis login/akses, arahkan ke administrator atau call center
6. Gunakan konteks percakapan sebelumnya untuk jawaban yang lebih relevan dan koheren
7. Jika tidak yakin atau pertanyaan di luar scope SIPO ICBP, arahkan ke kontak resmi
8. Sertakan emoji yang relevan untuk membuat komunikasi lebih friendly (tapi tidak berlebihan)

**PENTING - FORMAT HTML:**
Gunakan tag HTML berikut untuk formatting (JANGAN gunakan markdown):
- Heading: <h3 class="text-lg font-bold text-purple-800 mb-2">Judul</h3>
- Bold: <strong class="font-bold text-purple-900">teks tebal</strong>
- Italic: <em class="italic text-purple-800">teks miring</em>
- Paragraph: <p class="mb-2">paragraf</p>
- Bullet list: <ul class="list-disc list-inside my-2"><li>item 1</li><li>item 2</li></ul>
- Numbered list: <ol class="list-decimal list-inside my-2"><li>item 1</li><li>item 2</li></ol>
- Line break: <br>

CONTOH OUTPUT HTML:
<p class="mb-2">👋 Terima kasih atas pertanyaan Anda!</p>
<h3 class="text-lg font-bold text-purple-800 mb-2">Tentang SIPO ICBP</h3>
<p class="mb-2"><strong class="font-bold text-purple-900">SIPO ICBP</strong> adalah sistem informasi kesehatan yang memiliki fitur:</p>
<ul class="list-disc list-inside my-2">
<li>Rekam Medis Digital</li>
<li>Manajemen Obat</li>
<li>AI Assistant 24/7</li>
</ul>
<p class="mb-2">Ada yang bisa saya bantu? 😊</p>

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
}
