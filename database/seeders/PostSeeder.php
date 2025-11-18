<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
$posts = [
    [
        'title' => 'Pentingnya Pemeriksaan Kesehatan Rutin: Kunci Hidup Sehat dan Produktif 🩺',
        'body' => '<p>Pemeriksaan kesehatan rutin (Medical Check-up atau MCU) merupakan langkah proaktif dan investasi penting untuk menjaga kesehatan tubuh jangka panjang. Dengan melakukan pemeriksaan secara berkala, kita dapat mendeteksi dini berbagai penyakit, bahkan pada tahap awal yang seringkali berkembang tanpa gejala yang jelas (asimtomatik), seperti tekanan darah tinggi atau peningkatan kadar gula darah. Pendeteksian dini ini sangat krusial karena memungkinkan penanganan segera dan efektif.</p>
        <p>Prosedur pemeriksaan rutin biasanya mencakup pemeriksaan fisik, tes darah lengkap, tes urin, dan penilaian gaya hidup. Rekomendasi frekuensi pemeriksaan bisa berbeda, namun disarankan minimal 1 tahun sekali, atau lebih sering jika Anda memiliki faktor risiko tertentu (seperti riwayat penyakit keluarga).</p>
        <p> Manfaat Utama Pemeriksaan Kesehatan Rutin:</p>
        <ul>
            <li>Mendeteksi Penyakit Secara Dini: Memungkinkan pengobatan dimulai saat penyakit masih mudah dikendalikan, seperti pre-diabetes atau kolesterol tinggi.</li>
            <li>Mencegah Komplikasi Serius: Dengan mengontrol kondisi kronis, risiko komplikasi fatal seperti stroke, serangan jantung, atau kerusakan ginjal dapat diminimalisir secara signifikan.</li>
            <li>Memantau Kondisi Kesehatan: Memberikan data dasar (baseline) untuk membandingkan hasil dari tahun ke tahun, sehingga perubahan kecil pada tubuh dapat teridentifikasi.</li>
            <li>Meningkatkan Kesadaran Hidup Sehat: Hasil pemeriksaan seringkali memotivasi kita untuk menyesuaikan pola makan, meningkatkan aktivitas fisik, dan meninggalkan kebiasaan buruk.</li>
        </ul>
        <p>Jangan tunggu sampai sakit untuk memeriksakan diri. Jadwalkan pemeriksaan kesehatan rutin Anda sekarang sebagai bentuk kepedulian tertinggi terhadap diri sendiri.</p>',
        'image_path' => 'image/1.jpg',
    ],
    [
        'title' => 'Tips Menjaga Kesehatan Mental di Era Digital yang Serba Cepat 🧘‍♀️',
        'body' => '<p>Di era digital dan informasi yang serba cepat seperti sekarang, kita terus-menerus dibanjiri notifikasi dan informasi, yang dapat memicu stres dan kelelahan mental (burnout). Oleh karena itu, kesehatan mental menjadi salah satu aspek yang wajib diprioritaskan. Kesehatan mental yang baik bukan hanya tentang tidak adanya gangguan jiwa, tetapi juga kemampuan untuk mengelola stres, bekerja secara produktif, dan berkontribusi pada komunitas.</p>
        <p>Aspek penting dari menjaga kesehatan mental adalah menciptakan keseimbangan antara kehidupan daring (online) dan nyata (offline). Lakukan evaluasi rutin terhadap bagaimana penggunaan teknologi Anda memengaruhi suasana hati dan kualitas tidur.</p>
        <p> 5 Strategi Efektif Menjaga Kesehatan Mental:</p>
        <ol>
            <li>Batasi Waktu Penggunaan Gadget dan Scrolling: Terapkan aturan "puasa digital" beberapa jam sebelum tidur dan matikan notifikasi yang tidak penting untuk mengurangi distraksi dan kecemasan.</li>
            <li>Lakukan Olahraga Secara Teratur: Aktivitas fisik, bahkan jalan kaki ringan selama 30 menit, melepaskan endorfin (zat peningkat suasana hati) yang berfungsi sebagai pereda stres alami.</li>
            <li>Jaga Pola Tidur yang Cukup dan Berkualitas: Usahakan tidur 7-9 jam per malam. Tidur yang baik sangat penting untuk konsolidasi memori, regulasi emosi, dan pemulihan fisik.</li>
            <li>Luangkan Waktu untuk Bersosialisasi Nyata: Koneksi sosial yang kuat adalah penyangga emosional yang vital. Habiskan waktu berkualitas dengan keluarga dan teman tanpa terdistraksi gadget.</li>
            <li>Praktikkan Teknik Relaksasi: Coba meditasi mindfulness, latihan pernapasan dalam, atau menulis jurnal (journaling) untuk membantu menenangkan sistem saraf dan meningkatkan kesadaran diri.</li>
        </ol>
        <p>Ingatlah bahwa kesehatan mental yang baik akan berdampak positif pada produktivitas, hubungan interpersonal, dan kualitas hidup Anda secara keseluruhan. Jika Anda merasa kesulitan, jangan ragu mencari bantuan profesional dari psikolog atau psikiater.</p>',
        'image_path' => 'image/2.jpeg',
    ],
    [
        'title' => 'Mengenal Gejala, Faktor Risiko, dan Pencegahan Diabetes Tipe 2 🩸',
        'body' => '<p>Diabetes Melitus (DM), khususnya Diabetes Tipe 2, merupakan penyakit kronis yang ditandai dengan tingginya kadar gula (glukosa) dalam darah (hiperglikemia). Kondisi ini terjadi karena tubuh tidak dapat memproduksi atau menggunakan insulin secara efektif. Insulin adalah hormon yang bertugas membantu glukosa masuk ke sel untuk diubah menjadi energi. Jika dibiarkan, kadar gula darah tinggi dapat merusak organ vital seiring waktu.</p>
        <p>Beberapa faktor risiko utama yang dapat meningkatkan peluang seseorang terkena Diabetes Tipe 2 meliputi obesitas, kurangnya aktivitas fisik, riwayat keluarga dengan diabetes, dan usia di atas 45 tahun. Deteksi dini melalui tes gula darah adalah langkah kunci.</p>
        <p> Gejala Umum yang Perlu Diwaspadai:</p>
        <ul>
            <li>Poliuria (Sering Buang Air Kecil): Ginjal berusaha mengeluarkan kelebihan glukosa melalui urin.</li>
            <li>Polidipsi (Sering Merasa Haus): Akibat kehilangan cairan saat sering buang air kecil.</li>
            <li>Polifagia (Sering Merasa Lapar): Sel tubuh tidak mendapatkan glukosa yang dibutuhkan untuk energi.</li>
            <li>Penurunan Berat Badan Drastis: Tubuh mulai membakar lemak dan otot untuk energi.</li>
            <li>Lemas dan Mudah Lelah: Karena sel tidak mendapatkan pasokan energi yang efisien.</li>
            <li>Luka yang Lama Sembuh: Kadar gula darah tinggi mengganggu aliran darah dan fungsi saraf, menghambat proses penyembuhan.</li>
        </ul>
        <p>Pencegahan diabetes sebagian besar berfokus pada perubahan gaya hidup. Hal ini dapat dilakukan dengan menjaga pola makan sehat (tinggi serat, rendah gula dan lemak jenuh), olahraga teratur (minimal 150 menit aktivitas intensitas sedang per minggu), dan menjaga berat badan ideal. Hindari stres berlebihan dan pastikan istirahat cukup untuk mendukung regulasi hormon.</p>',
        'image_path' => 'image/3.jpg',
    ],
    [
        'title' => 'Strategi Komprehensif Pencegahan ISPA di Lingkungan Kerja dan Publik 😷',
        'body' => '<p>Infeksi Saluran Pernapasan Akut (ISPA) adalah penyakit yang disebabkan oleh infeksi virus atau bakteri yang menyerang hidung, tenggorokan, dan paru-paru. Di lingkungan kerja yang seringkali merupakan ruang tertutup dengan sirkulasi udara terbatas, penyebaran ISPA dapat terjadi sangat cepat, mengganggu produktivitas dan meningkatkan angka ketidakhadiran (absenteeism). Pencegahan yang efektif sangat penting untuk menciptakan lingkungan kerja yang aman dan sehat.</p>
        <p>ISPA dapat menular melalui droplet pernapasan yang dikeluarkan saat batuk, bersin, atau berbicara. Oleh karena itu, fokus pencegahan adalah pada higiene diri dan pengendalian lingkungan.</p>
        <p> 5 Cara Mencegah Penularan ISPA:</p>
        <ul>
            <li>Mencuci Tangan Secara Rutin dan Benar: Gunakan sabun dan air mengalir setidaknya 20 detik. Ini adalah cara paling efektif untuk menghilangkan kuman.</li>
            <li>Menggunakan Masker dengan Tepat: Terutama saat berada di tempat umum, transportasi publik, atau saat berinteraksi dengan orang yang sedang sakit, untuk mengurangi penyebaran dan penularan droplet.</li>
            <li>Menjaga Jarak Fisik dan Menghindari Kerumunan: Jaga jarak aman (minimal 1 meter) dengan orang yang menunjukkan gejala ISPA.</li>
            <li>Meningkatkan Daya Tahan Tubuh: Konsumsi makanan bergizi seimbang, tidur cukup, dan pertimbangkan asupan vitamin C dan D (sesuai anjuran).</li>
            <li>Menghindari Sentuhan Wajah: Jangan menyentuh mata, hidung, atau mulut dengan tangan yang belum dicuci, karena ini adalah pintu masuk utama virus dan bakteri.</li>
        </ul>
        <p>Selain itu, deteksi dini gejala (batuk, pilek, demam, sakit tenggorokan) dan anjuran untuk bekerja dari rumah bagi karyawan yang sakit adalah kebijakan krusial untuk mencegah penyebaran lebih lanjut di lingkungan kerja.</p>',
        'image_path' => 'image/4.jpeg',
    ],
];

        foreach ($posts as $post) {
            Post::create($post);
        }
    }
}
