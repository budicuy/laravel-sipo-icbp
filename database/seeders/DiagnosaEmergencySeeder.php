<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiagnosaEmergencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $diagnosaEmergency = [
            [
                'nama_diagnosa_emergency' => 'Luka Ringan',
                'deskripsi' => 'Luka superficial yang tidak memerlukan penjahitan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_diagnosa_emergency' => 'Luka Berat',
                'deskripsi' => 'Luka dalam yang memerlukan penjahitan atau perawatan intensif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_diagnosa_emergency' => 'Pingsan/Sinkop',
                'deskripsi' => 'Kehilangan kesadaran sementara',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_diagnosa_emergency' => 'Demam Tinggi',
                'deskripsi' => 'Suhu tubuh di atas 38.5°C',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_diagnosa_emergency' => 'Nyeri Dada',
                'deskripsi' => 'Nyeri pada area dada yang memerlukan evaluasi segera',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_diagnosa_emergency' => 'Sesak Napas',
                'deskripsi' => 'Kesulitan bernapas yang memerlukan intervensi segera',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_diagnosa_emergency' => 'Migrain Akut',
                'deskripsi' => 'Sakit kepala severe yang memerlukan penanganan segera',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_diagnosa_emergency' => 'Cedera Trauma',
                'deskripsi' => 'Cedera akibat kecelakaan atau trauma fisik',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_diagnosa_emergency' => 'Keracunan',
                'deskripsi' => 'Keracunan makanan, obat, atau zat lain',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_diagnosa_emergency' => 'Alergi Akut',
                'deskripsi' => 'Reaksi alergi yang memerlukan penanganan segera',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('diagnosa_emergency')->insert($diagnosaEmergency);
    }
}