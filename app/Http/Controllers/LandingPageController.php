<?php

namespace App\Http\Controllers;

use App\Models\AIChatHistory;
use App\Models\Karyawan;
use App\Models\Keluarga;
use App\Models\Post;
use App\Models\RekamMedis;
use Gemini\Data\Content;
use Gemini\Enums\Role;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Http\Request;
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
     * Display the AI Chat page.
     *
     * @return \Illuminate\View\View
     */
    public function aiChat()
    {
        return view('landing.ai-chat');
    }

    /**
     * Display a specific post detail for public access.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\View\View
     */
    public function showPost(Post $post)
    {
        return view('landing.post-detail', compact('post'));
    }

    /**
     * Display all posts for public access.
     *
     * @return \Illuminate\View\View
     */
    public function indexPosts()
    {
        $posts = Post::latest()->paginate(12);
        return view('landing.posts', compact('posts'));
    }

    /**
     * Check NIK and return employee data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkNik(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|max:20',
            'password' => 'required|string|max:20',
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
     * Get medical history for a user by NIK
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMedicalHistory(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|max:20',
        ]);

        $nik = $request->nik;

        // Find employee by NIK
        $karyawan = Karyawan::where('nik_karyawan', $nik)->first();

        if (! $karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'NIK tidak ditemukan',
            ], 404);
        }

        // Get medical records through keluarga relationship
        $riwayatKunjungan = RekamMedis::whereHas('keluarga', function ($query) use ($karyawan) {
            $query->where('id_karyawan', $karyawan->id_karyawan);
        })
            ->with(['keluhans.diagnosa', 'keluhans.obat'])
            ->orderBy('tanggal_periksa', 'desc')
            ->orderBy('waktu_periksa', 'desc')
            ->limit(10) // Limit to last 10 visits
            ->get();

        // Format the data
        $formattedHistory = $riwayatKunjungan->map(function ($rekam) {
            $keluhans = $rekam->keluhans->map(function ($keluhan) {
                $obatArray = [];
                if ($keluhan->obat) {
                    $obatArray[] = [
                        'nama_obat' => $keluhan->obat->nama_obat ?? '-',
                        'jumlah' => $keluhan->jumlah_obat ?? 0,
                        'satuan' => $keluhan->obat->satuan ?? '',
                    ];
                }

                return [
                    'diagnosa' => $keluhan->diagnosa->nama_diagnosa ?? '-',
                    'keterangan' => $keluhan->keterangan ?? '-',
                    'terapi' => $keluhan->terapi ?? '-',
                    'obat' => $obatArray,
                ];
            });

            return [
                'tanggal' => $rekam->tanggal_periksa->format('d/m/Y'),
                'waktu' => $rekam->waktu_periksa,
                'keluhan' => $keluhans->toArray(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'nama' => $karyawan->nama_karyawan,
                'nik' => $karyawan->nik_karyawan,
                'total_kunjungan' => $formattedHistory->count(),
                'riwayat' => $formattedHistory,
            ],
        ]);
    }

    /**
     * Get list of family members for patient selection
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFamilyList(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|max:20',
        ]);

        $result = $this->getFamilyMembers($request->nik);

        return response()->json($result);
    }

    /**
     * Pre-load medical data for AI memory when patient is selected
     * This prevents AI hallucination and improves performance
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function preloadMedicalData(Request $request)
    {
        $request->validate([
            'user_nik' => 'required|string|max:20',
            'id_keluarga' => 'required|integer',
        ]);

        $userNik = $request->user_nik;
        $idKeluarga = $request->id_keluarga;

        try {
            // Get medical history data
            $historyData = $this->getMedicalHistoryData($userNik, $idKeluarga);

            if (! $historyData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data rekam medis',
                ]);
            }

            // Format data for AI memory
            $medicalContext = $this->formatMedicalDataForAI($historyData['data']);

            // Check if medical context is too long and truncate if necessary
            if (strlen($medicalContext) > 45000) {
                Log::warning('Medical context too long, truncating', [
                    'user_nik' => $userNik,
                    'id_keluarga' => $idKeluarga,
                    'original_length' => strlen($medicalContext),
                    'total_visits' => $historyData['data']['total_kunjungan'],
                ]);

                // Truncate to prevent validation errors
                $medicalContext = substr($medicalContext, 0, 45000)."\n\n⚠️ Catatan: Data riwayat medis dipotong karena terlalu panjang.";
            }

            return response()->json([
                'success' => true,
                'medical_context' => $medicalContext,
                'patient_name' => $historyData['data']['nama'],
                'total_visits' => $historyData['data']['total_kunjungan'],
            ]);
        } catch (\Exception $e) {
            Log::error('Error in preloadMedicalData: '.$e->getMessage(), [
                'user_nik' => $userNik,
                'id_keluarga' => $idKeluarga,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data medis. Silakan coba lagi.',
            ], 500);
        }
    }

    /**
     * Format medical data into AI-friendly context string
     */
    private function formatMedicalDataForAI($data)
    {
        $context = "**📋 DATA REKAM MEDIS PASIEN:**\n\n";
        $context .= 'Nama Pasien: '.$data['nama']."\n";
        $context .= 'NIK: '.$data['nik']."\n";
        $context .= 'Total Kunjungan: '.$data['total_kunjungan']." kali\n";
        $context .= 'Waktu Sekarang: '.now('Asia/Makassar')->format('d/m/Y H:i').' WITA'."\n\n";

        if ($data['total_kunjungan'] === 0) {
            $context .= "⚠️ Pasien ini belum memiliki riwayat kunjungan.\n";

            return $context;
        }

        $context .= "**DETAIL RIWAYAT KUNJUNGAN:**\n\n";

        // Limit to last 15 visits to prevent context from being too long
        $riwayatTerbatas = $data['riwayat']->slice(0, 15);
        $jumlahDitampilkan = $riwayatTerbatas->count();

        if ($jumlahDitampilkan < $data['total_kunjungan']) {
            $context .= "📝 Menampilkan {$jumlahDitampilkan} kunjungan terakhir dari {$data['total_kunjungan']} total kunjungan:\n\n";
        }

        foreach ($riwayatTerbatas as $index => $kunjungan) {
            $context .= 'Kunjungan '.($index + 1).":\n";
            $context .= '- Tanggal: '.$kunjungan['tanggal'].' '.$kunjungan['waktu']."\n";
            $context .= '- Pasien: '.$kunjungan['nama_pasien']."\n";

            $keluhanList = $kunjungan['keluhan'];
            foreach ($keluhanList as $keluhanIdx => $keluhan) {
                if (count($keluhanList) > 1) {
                    $context .= '  Keluhan '.($keluhanIdx + 1).":\n";
                }
                $context .= '  - Diagnosa: '.$keluhan['diagnosa']."\n";

                // Limit keterangan to prevent overly long context
                $keterangan = $keluhan['keterangan'];
                if (strlen($keterangan) > 200) {
                    $keterangan = substr($keterangan, 0, 200).'...';
                }
                $context .= '  - Keterangan: '.$keterangan."\n";

                $context .= '  - Terapi: '.$keluhan['terapi']."\n";

                if (! empty($keluhan['obat'])) {
                    $context .= "  - Obat yang diberikan:\n";
                    foreach ($keluhan['obat'] as $obat) {
                        $context .= '    * '.$obat['nama_obat'].' - '.$obat['jumlah'].' '.$obat['satuan'];
                        if (! empty($obat['aturan_pakai']) && $obat['aturan_pakai'] != '-') {
                            // Limit aturan pakai to prevent overly long context
                            $aturanPakai = $obat['aturan_pakai'];
                            if (strlen($aturanPakai) > 100) {
                                $aturanPakai = substr($aturanPakai, 0, 100).'...';
                            }
                            $context .= ' (Aturan: '.$aturanPakai.')';
                        }
                        $context .= "\n";
                    }
                } else {
                    $context .= "  - Obat: Tidak ada\n";
                }
            }
            $context .= "\n";
        }

        $context .= "\n**⚠️ INSTRUKSI UNTUK AI:**\n";
        $context .= '- Data di atas adalah riwayat medis pasien '.$data['nama']."\n";
        $context .= '- Total kunjungan: '.$data['total_kunjungan']." kali\n";
        if ($jumlahDitampilkan < $data['total_kunjungan']) {
            $context .= "- ⚠️ **PENTING**: Hanya {$jumlahDitampilkan} kunjungan terakhir yang ditampilkan. Anda hanya bisa melihat dan merujuk {$jumlahDitampilkan} kunjungan terakhir ini.\n";
            $context .= '- JANGAN menyebutkan atau merujuk kunjungan sebelum kunjungan ke-'.($data['total_kunjungan'] - $jumlahDitampilkan + 1)." karena data tidak tersedia.\n";
            $context .= "- Jika user bertanya tentang kunjungan lama, jelaskan bahwa hanya data {$jumlahDitampilkan} kunjungan terakhir yang tersedia.\n";
        } else {
            $context .= "- Semua kunjungan tersedia untuk dianalisis.\n";
        }
        $context .= "- Gunakan data ini untuk menjawab pertanyaan tentang riwayat kesehatan\n";
        $context .= "- JANGAN menambah atau mengurangi jumlah kunjungan yang terlihat\n";
        $context .= "- Jika user bertanya tentang riwayat, gunakan data ini langsung (tidak perlu generate ulang)\n";

        return $context;
    }

    /**
     * Handle AI chat request with Gemini API
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function chat(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:5000',
                'history' => 'nullable|array',
                'history.*.role' => 'required|string|in:user,model|max:10',
                'history.*.text' => 'required|string|max:50000', // Increased to accommodate medical context
                'user_nik' => 'nullable|string|max:20',
                'user_name' => 'nullable|string|max:100',
                'id_keluarga' => 'nullable|integer', // ID pasien yang dipilih
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation error for debugging
            Log::error('Chat validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
                'user_nik' => $request->input('user_nik'),
            ]);

            // Return user-friendly error message
            return response()->json([
                'success' => false,
                'reply' => '⚠️ Terjadi kesalahan validasi. Mohon refresh halaman dan coba lagi. Jika masalah berlanjut, hubungi administrator.',
            ], 422);
        }

        // Get user info
        $userNik = $request->input('user_nik');
        $userName = $request->input('user_name');
        $idKeluarga = $request->input('id_keluarga'); // Get selected patient ID

        // Log for debugging
        Log::info('Chat request received', [
            'user_nik' => $userNik,
            'user_name' => $userName,
            'id_keluarga' => $idKeluarga,
            'message' => $request->message,
        ]);

        try {
            // Track AI chat access if user is authenticated
            if ($userNik && $userName) {
                AIChatHistory::recordAIChatAccess($userNik);
            }

            // Initialize Gemini chat with enhanced memory configuration
            $chat = Gemini::chat(model: config('gemini.model', 'models/gemini-2.0-flash-exp'));

            // Build user context
            $userContext = '';
            if ($userName && $userNik) {
                $userContext = "\n\n**INFORMASI USER YANG SEDANG BERBICARA:**\n";
                $userContext .= "- Nama: {$userName}\n";
                $userContext .= "- NIK: {$userNik}\n";
                $userContext .= "\nGunakan informasi ini untuk menyapa user secara personal dengan nama mereka.\n";
            }

            // Add current time context
            $currentTime = now('Asia/Makassar')->format('d/m/Y H:i');
            $timeContext = "\n\n**INFORMASI WAKTU SAAT INI:**\n";
            $timeContext .= "- Waktu Sekarang: {$currentTime} WITA\n";
            $timeContext .= "- Gunakan informasi waktu ini untuk memberikan konteks yang relevan dalam jawaban Anda.\n";
            $timeContext .= "- Jika user bertanya tentang kondisi saat ini, pertimbangkan waktu dan tanggal saat ini.\n";

            // System prompt untuk konteks AI
            $systemPrompt = 'Anda adalah AI Assistant resmi untuk SIPO (Sistem Informasi Poliklinik) - PT. Indofood CBP Sukses Makmur Tbk.

**GAYA BAHASA YANG DIGUNAKAN:**
- Gunakan "Anda" untuk merujuk kepada user (netral, formal)
- Gunakan "Kamu" untuk gaya yang lebih santai tapi tetap netral
- JANGAN gunakan "Bapak", "Ibu", "Bapak/Ibu", "Perempuan", "Laki-laki", atau sebutan gender spesifik lainnya
- Hindari asumsi gender user berdasarkan nama atau pertanyaan
- Tetap profesional dan ramah kepada semua user tanpa membedakan gender

IDENTITAS SISTEM:
- Nama: SIPO (Sistem Informasi Poliklinik)
- Perusahaan: PT. Indofood CBP Sukses Makmur Tbk.
- Lokasi: Jalan Ayani KM. 32 Liang Anggang, Pandahan, Kec. Bati Bati, Kabupaten Tanah Laut, Kalimantan Selatan - 70852
- Fungsi: Sistem manajemen pelayanan kesehatan karyawan berbasis digital

FITUR UTAMA SIPO :
1. **Rekam Medis Digital**: Pencatatan lengkap riwayat kesehatan karyawan secara elektronik dan aman
2. **Manajemen Obat**: Tracking stok obat otomatis, alert stok menipis, riwayat penggunaan obat terperinci
3. **AI Assistant**: Chat assistant powered by Google Gemini untuk bantuan 24/7
4. **Fingerprint**: Autentikasi karyawan menggunakan sidik jari untuk keamanan akses
5. **Laporan & Analitik**: Dashboard komprehensif dengan visualisasi data kesehatan dan tren penyakit
6. **Keamanan Data**: Enkripsi data, role-based access control, dan audit trail lengkap

'.$userContext.'

'.$timeContext. '

KONTAK INFORMASI:
- Telepon   : +0511 4787 981
- WhatsApp  : +6281293222772 / +6281349052799 / +6285248828285
- Email     : noodle.bjm@gmail.com / noodle.banjarmasin@gmail.com

PANDUAN MENJAWAB:
1. **FORMAT OUTPUT: GUNAKAN HTML MURNI** - Semua response harus dalam format HTML yang valid TANPA markdown
2. Jawab dengan struktur yang jelas: Greeting → Jawaban Inti → Detail Pendukung → Call-to-Action (jika perlu)
3. Selalu profesional, ramah, dan informatif dalam Bahasa Indonesia
4. Jika ditanya tentang fitur, jelaskan manfaat konkret untuk pengguna
5. Untuk pertanyaan teknis login/akses, arahkan ke administrator atau call center
6. Gunakan konteks percakapan sebelumnya untuk jawaban yang lebih relevan dan koheren
7. Jika tidak yakin atau pertanyaan di luar scope SIPO, arahkan ke kontak resmi
8. Sertakan emoji yang relevan untuk membuat komunikasi lebih friendly (tapi tidak berlebihan)

**GAYA BAHASA DAN PENYAPAAN:**
- Gunakan "Anda" untuk merujuk kepada user (formal, profesional)
- Gunakan "Kamu" untuk gaya yang lebih santai tapi tetap netral
- JANGAN gunakan "Bapak", "Ibu", "Bapak/Ibu", "Perempuan", "Laki-laki", atau sebutan gender spesifik
- Hindari asumsi gender user berdasarkan nama atau pertanyaan
- Tetap profesional dan ramah kepada semua user tanpa membedakan gender
- Gunakan bahasa yang inklusif dan tidak diskriminatif

**KRITIS - FORMAT HTML MURNI DENGAN INLINE STYLES:**
WAJIB gunakan HTML dengan INLINE STYLES saja (DILARANG gunakan class CSS atau Tailwind!):
- Heading: <h3 style="font-size: 18px; font-weight: bold; color: #7C3AED; margin-bottom: 12px;">Judul</h3>
- Bold: <strong style="font-weight: bold; color: #6B21A8;">teks tebal</strong>
- Italic: <em style="font-style: italic; color: #7C3AED;">teks miring</em>
- Paragraph: <p style="margin-bottom: 12px; color: #374151;">paragraf</p>
- Bullet list: <ul style="margin: 12px 0; padding-left: 24px;"><li style="margin-bottom: 4px;">item 1</li></ul>
- Numbered list: <ol style="margin: 12px 0; padding-left: 24px;"><li style="margin-bottom: 4px;">item 1</li></ol>
- Card/Box: <div style="background: #F3F4F6; padding: 16px; border-radius: 8px; margin: 12px 0;">content</div>

**DILARANG KERAS - JANGAN GUNAKAN FORMAT INI:**
- JANGAN gunakan PHP code (<?php, =>, $variable, dll)
- JANGAN gunakan class CSS (class="..." tidak boleh)
- JANGAN gunakan Tailwind classes
- Hanya gunakan style="..." untuk semua formatting
- JANGAN gunakan markdown formatting (```html, **text**, *text*, # heading, dll)
- JANGAN gunakan code blocks atau backticks
- JANGAN gunakan ```html``` atau ``` apapun ```
- JANGAN gunakan asterisks untuk formatting
- JANGAN gunakan backticks `
- JANGAN gunakan *** text *** formatting

**KHUSUS UNTUK RIWAYAT KUNJUNGAN/MEDICAL HISTORY:**
Jika user menanyakan riwayat kunjungan, format dengan inline styles:
1. Berikan salam dan total kunjungan
2. Tampilkan setiap kunjungan dalam card/box dengan style
3. Include: Nomor, Tanggal, Diagnosa, Obat (jika ada)
4. Gunakan warna untuk membedakan kunjungan
5. Tambahkan tips kesehatan di akhir jika ada pola

**⚠️ BATASAN DATA RIWAYAT:**
- Jika total kunjungan > 15, Anda hanya akan melihat 15 kunjungan terakhir
- JANGAN merujuk atau menyebutkan kunjungan di luar 15 terakhir
- Jika user tanya tentang kunjungan lama, jelaskan bahwa hanya 15 kunjungan terakhir yang tersedia
- Selalu sebutkan "15 kunjungan terakhir" saat membahas riwayat

CONTOH OUTPUT HTML DENGAN INLINE STYLES:
<p style="margin-bottom: 12px; color: #374151;">👋 Terima kasih atas pertanyaan Anda!</p>
<h3 style="font-size: 18px; font-weight: bold; color: #7C3AED; margin-bottom: 12px;">Tentang SIPO</h3>
<p style="margin-bottom: 12px; color: #374151;"><strong style="font-weight: bold; color: #6B21A8;">SIPO</strong> adalah sistem informasi kesehatan yang memiliki fitur:</p>
<ul style="margin: 12px 0; padding-left: 24px;">
<li style="margin-bottom: 4px;">Rekam Medis Digital</li>
<li style="margin-bottom: 4px;">Manajemen Obat</li>
<li style="margin-bottom: 4px;">AI Assistant 24/7</li>
</ul>
<p style="margin-bottom: 12px; color: #374151;">Ada yang bisa saya bantu? 😊</p>

**CONTOH OUTPUT RIWAYAT KUNJUNGAN (INLINE STYLES):**
<h3 style="font-size: 18px; font-weight: bold; color: #7C3AED; margin-bottom: 16px;">📋 Riwayat Kunjungan Anda</h3>
<p style="margin-bottom: 16px; color: #374151;">Berdasarkan data SIPO, Anda tercatat <strong style="font-weight: bold; color: #6B21A8;">9 kali kunjungan</strong> ke poliklinik.</p>

<div style="background: #F9FAFB; padding: 12px; border-left: 4px solid #7C3AED; border-radius: 6px; margin-bottom: 12px;">
<p style="margin: 0; font-weight: bold; color: #6B21A8;">1. Kunjungan 14/10/2025</p>
<p style="margin: 4px 0 0 0; font-size: 14px; color: #6B7280;">Diagnosa: <strong style="color: #374151;">Luka</strong></p>
<p style="margin: 4px 0 0 0; font-size: 14px; color: #6B7280;">Obat: Hansaplast (2 Pcs)</p>
</div>

<div style="background: #F9FAFB; padding: 12px; border-left: 4px solid #3B82F6; border-radius: 6px; margin-bottom: 12px;">
<p style="margin: 0; font-weight: bold; color: #1E40AF;">2. Kunjungan 10/10/2025</p>
<p style="margin: 4px 0 0 0; font-size: 14px; color: #6B7280;">Diagnosa: <strong style="color: #374151;">Hordeolum</strong></p>
<p style="margin: 4px 0 0 0; font-size: 14px; color: #6B7280;">Obat: Cendo Xitrol (1 Botol)</p>
</div>

<p style="margin-top: 16px; padding: 12px; background: #FEF3C7; border: 1px solid #FDE047; border-radius: 6px; font-size: 14px; color: #92400E;">💡 <strong>Tips:</strong> Anda sering mengalami alergi, disarankan konsultasi lebih lanjut.</p>

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
- Dan sakit lainnya yang memerlukan evaluasi medis segera

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

**PERINGATAN KRITIS - FORMAT OUTPUT:**
- JANGAN gunakan markdown formatting apapun
- JANGAN gunakan code blocks ```html``` atau ``` apapun ```
- JANGAN gunakan backticks `
- JANGAN gunakan asterisks ** atau * untuk formatting
- JANGAN gunakan ```html``` di awal atau akhir response
- LANGSUNG output HTML murni tanpa pembungkus markdown

Jawab pertanyaan dengan akurat, empati, dan bertanggung jawab berdasarkan panduan di atas.';

            // Prepare history for Gemini API
            $historyContent = [];
            $userHistory = $request->input('history', []);

            // Add history to context (convert format)
            foreach ($userHistory as $item) {
                $role = $item['role'] === 'user' ? Role::USER : Role::MODEL;
                $historyContent[] = Content::parse(part: $item['text'], role: $role);
            }

            // Add system prompt as first message
            array_unshift($historyContent, Content::parse(part: $systemPrompt, role: Role::USER));

            // Send message to Gemini
            $response = $chat->startChat(history: $historyContent)->sendMessage($request->message);

            // Get response text
            $reply = $response->text();

            // Log successful response
            Log::info('Chat response generated successfully', [
                'user_nik' => $userNik,
                'response_length' => strlen($reply),
            ]);

            return response()->json([
                'success' => true,
                'reply' => $reply,
            ]);
        } catch (\Exception $e) {
            Log::error('Gemini API Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_nik' => $userNik ?? null,
            ]);

            return response()->json([
                'success' => false,
                'reply' => '⚠️ Maaf, terjadi kesalahan saat memproses permintaan Anda. Silakan coba lagi dalam beberapa saat. Jika masalah berlanjut, hubungi administrator.',
            ], 500);
        }
    }

    /**
     * Get medical history data (internal method)
     * Now accepts optional id_keluarga parameter to filter by specific patient
     */
    private function getMedicalHistoryData($nik, $idKeluarga = null)
    {
        // Find employee by NIK
        $karyawan = Karyawan::where('nik_karyawan', $nik)->first();

        if (! $karyawan) {
            return [
                'success' => false,
                'message' => 'NIK tidak ditemukan',
            ];
        }

        // Build query for medical records
        $query = RekamMedis::whereHas('keluarga', function ($q) use ($karyawan) {
            $q->where('id_karyawan', $karyawan->id_karyawan);
        });

        // If specific id_keluarga is provided, filter by that patient only
        if ($idKeluarga) {
            $query->where('id_keluarga', $idKeluarga);
        }

        $riwayatKunjungan = $query
            ->with([
                'keluarga:id_keluarga,nama_keluarga,id_karyawan',
                'keluhans.diagnosa:id_diagnosa,nama_diagnosa',
                'keluhans.obat:id_obat,nama_obat,id_satuan',
                'keluhans.obat.satuanObat:id_satuan,nama_satuan',
            ])
            ->orderBy('tanggal_periksa', 'asc')
            ->orderBy('waktu_periksa', 'asc')
            ->limit(20) // Limit to prevent memory issues and long context
            ->get();

        // Get patient name for display
        $namaPasien = $karyawan->nama_karyawan;
        if ($idKeluarga) {
            $keluarga = Keluarga::find($idKeluarga);
            $namaPasien = $keluarga ? $keluarga->nama_keluarga : $namaPasien;
        }

        // Format the data - DO NOT GROUP, show each visit separately
        $formattedHistory = $riwayatKunjungan->map(function ($rekam) {
            // Build keluhan list for this specific visit
            $keluhanList = [];
            foreach ($rekam->keluhans as $keluhan) {
                $diagnosaNama = $keluhan->diagnosa->nama_diagnosa ?? 'Tidak ada diagnosa';

                // Build obat list for this keluhan
                $obatList = [];
                if ($keluhan->obat) {
                    $obatList[] = [
                        'nama_obat' => $keluhan->obat->nama_obat ?? '-',
                        'jumlah' => $keluhan->jumlah_obat ?? 0,
                        'satuan' => $keluhan->obat->satuanObat->nama_satuan ?? '',
                        'aturan_pakai' => $keluhan->aturan_pakai ?? '-',
                    ];
                }

                $keluhanList[] = [
                    'diagnosa' => $diagnosaNama,
                    'keterangan' => $keluhan->keterangan ?? '-',
                    'terapi' => $keluhan->terapi ?? '-',
                    'obat' => $obatList,
                ];
            }

            return [
                'id_rekam' => $rekam->id_rekam,
                'tanggal' => $rekam->tanggal_periksa->format('d/m/Y'),
                'waktu' => $rekam->waktu_periksa,
                'nama_pasien' => $rekam->keluarga->nama_keluarga ?? '-',
                'keluhan' => $keluhanList,
            ];
        });

        return [
            'success' => true,
            'data' => [
                'nama' => $namaPasien,
                'nik' => $karyawan->nik_karyawan,
                'total_kunjungan' => $formattedHistory->count(),
                'riwayat' => $formattedHistory,
            ],
        ];
    }

    /**
     * Get family members for a NIK (for patient selection)
     */
    private function getFamilyMembers($nik)
    {
        $karyawan = Karyawan::where('nik_karyawan', $nik)->first();

        if (! $karyawan) {
            return [
                'success' => false,
                'message' => 'NIK tidak ditemukan',
            ];
        }

        // Get all family members including the employee with hubungan relationship
        $keluargaList = Keluarga::where('id_karyawan', $karyawan->id_karyawan)
            ->with('hubungan') // Eager load hubungan relationship
            ->get()
            ->map(function ($keluarga) {
                // Get hubungan text from relationship
                $hubunganText = 'Karyawan';
                if ($keluarga->hubungan && isset($keluarga->hubungan->hubungan)) {
                    $hubunganText = $keluarga->hubungan->hubungan;
                }

                return [
                    'id_keluarga' => $keluarga->id_keluarga,
                    'nama_pasien' => $keluarga->nama_keluarga,
                    'hubungan' => $hubunganText,
                ];
            });

        return [
            'success' => true,
            'data' => [
                'nama_karyawan' => $karyawan->nama_karyawan,
                'nik' => $karyawan->nik_karyawan,
                'anggota_keluarga' => $keluargaList,
            ],
        ];
    }
}
