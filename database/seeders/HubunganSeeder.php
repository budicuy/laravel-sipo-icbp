<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HubunganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama jika ada
        DB::table('hubungan')->delete();

        $hubungans = [
            ['kode_hubungan' => 'A', 'hubungan' => 'Diri Sendiri'],
            ['kode_hubungan' => 'B', 'hubungan' => 'Spouse'],
            ['kode_hubungan' => 'C', 'hubungan' => 'Anak 1'],
            ['kode_hubungan' => 'D', 'hubungan' => 'Anak 2'],
            ['kode_hubungan' => 'E', 'hubungan' => 'Anak 3'],
        ];

        DB::table('hubungan')->insert($hubungans);
    }
}
