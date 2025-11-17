<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Pentingnya Pemeriksaan Kesehatan Rutin',
                'body' => '<p>Pemeriksaan kesehatan rutin merupakan langkah penting untuk menjaga kesehatan tubuh. Dengan melakukan pemeriksaan secara berkala, kita dapat mendeteksi dini berbagai penyakit yang mungkin berkembang tanpa gejala yang jelas.</p><p>Beberapa manfaat pemeriksaan kesehatan rutin antara lain:</p><ul><li>Mendeteksi penyakit secara dini</li><li>Mencegah komplikasi yang lebih serius</li><li>Memantau kondisi kesehatan secara keseluruhan</li><li>Meningkatkan kesadaran akan pentingnya hidup sehat</li></ul><p>Jangan tunggu sampai sakit untuk memeriksakan diri. Lakukan pemeriksaan kesehatan rutin minimal 1 tahun sekali.</p>',
                'image_path' => null,
            ],
            [
                'title' => 'Tips Menjaga Kesehatan Mental di Era Digital',
                'body' => '<p>Di era digital seperti sekarang, kesehatan mental menjadi salah satu aspek yang sering terabaikan. Padahal, kesehatan mental yang baik sama pentingnya dengan kesehatan fisik.</p><p>Beberapa tips menjaga kesehatan mental:</p><ol><li>Batasi waktu penggunaan gadget</li><li>Lakukan olahraga secara teratur</li><li>Jaga pola tidur yang cukup</li><li>Luangkan waktu untuk bersosialisasi</li><li>Praktikkan teknik relaksasi seperti meditasi</li></ol><p>Ingatlah bahwa kesehatan mental yang baik akan berdampak positif pada produktivitas dan kualitas hidup Anda.</p>',
                'image_path' => null,
            ],
            [
                'title' => 'Mengenal Gejala dan Pencegahan Diabetes',
                'body' => '<p>Diabetes merupakan penyakit kronis yang disebabkan oleh kadar gula darah yang tinggi dalam tubuh. Penyakit ini dapat menimbulkan berbagai komplikasi jika tidak ditangani dengan baik.</p><p>Gejala diabetes yang umum terjadi:</p><ul><li>Sering merasa haus dan lapar</li><li>Sering buang air kecil</li><li>Penurunan berat badan drastis</li><li>Lemas dan mudah lelah</li><li>Luka yang lama sembuh</li></ul><p>Pencegahan diabetes dapat dilakukan dengan menjaga pola makan sehat, olahraga teratur, dan menghindari stres berlebihan.</p>',
                'image_path' => null,
            ],
            [
                'title' => 'Manfaat Vaksinasi untuk Kesehatan Masyarakat',
                'body' => '<p>Vaksinasi merupakan salah satu cara efektif untuk mencegah penyebaran penyakit menular. Dengan vaksinasi, sistem kekebalan tubuh akan belajar mengenali dan melawan virus atau bakteri penyebab penyakit.</p><p>Manfaat vaksinasi:</p><ul><li>Melindungi diri sendiri dari penyakit</li><li>Melindungi orang lain (herd immunity)</li><li>Mengurangi beban sistem kesehatan</li><li>Mencegah epidemi dan pandemi</li></ul><p>Vaksinasi rutin sesuai jadwal yang direkomendasikan sangat penting untuk menjaga kesehatan masyarakat.</p>',
                'image_path' => null,
            ],
        ];

        foreach ($posts as $post) {
            Post::create($post);
        }
    }
}
