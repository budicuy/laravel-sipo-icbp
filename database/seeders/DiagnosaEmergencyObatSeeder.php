<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\DiagnosaEmergency;
use App\Models\Obat;

class DiagnosaEmergencyObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate pivot table first
        DB::table('diagnosa_emergency_obat')->truncate();

        // Get all diagnosa emergency and obat
        $diagnosaEmergencies = DiagnosaEmergency::all();
        $obats = Obat::all();

        if ($diagnosaEmergencies->isEmpty() || $obats->isEmpty()) {
            $this->command->warn('No diagnosa emergency or obat data found. Skipping pivot table seeding.');
            return;
        }

        // Define common obat for each diagnosa emergency
        $diagnosaObatMapping = [
            'Luka Ringan' => ['Betadine', 'Kasa Steril', 'Plaster'],
            'Luka Berat' => ['Betadine', 'Kasa Steril', 'Jarum Jahit', 'Benang Jahit', 'Painkiller'],
            'Pingsan/Sinkop' => ['Ammonia', 'Glukosa', 'Oksigen'],
            'Demam Tinggi' => ['Paracetamol', 'Ibuprofen', 'Kompres Dingin'],
            'Nyeri Dada' => ['Aspirin', 'Nitrogliserin', 'Oksigen'],
            'Sesak Napas' => ['Salbutamol', 'Oksigen', 'Kortikosteroid'],
            'Migrain Akut' => ['Sumatriptan', 'Paracetamol', 'Ibuprofen'],
            'Cedera Trauma' => ['Painkiller', 'Anti-inflamasi', 'Kasa Steril'],
            'Keracunan' => ['Arang Aktif', 'Antidotum', 'Infus'],
            'Alergi Akut' => ['Antihistamin', 'Epinefrin', 'Kortikosteroid'],
        ];

        $pivotData = [];
        $now = now();

        foreach ($diagnosaEmergencies as $diagnosa) {
            $diagnosaName = $diagnosa->nama_diagnosa_emergency;
            
            if (isset($diagnosaObatMapping[$diagnosaName])) {
                $obatNames = $diagnosaObatMapping[$diagnosaName];
                
                foreach ($obatNames as $obatName) {
                    $obat = $obats->firstWhere('nama_obat', $obatName);
                    
                    if ($obat) {
                        $pivotData[] = [
                            'id_diagnosa_emergency' => $diagnosa->id_diagnosa_emergency,
                            'id_obat' => $obat->id_obat,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }
        }

        // Insert data in batches
        if (!empty($pivotData)) {
            $chunks = array_chunk($pivotData, 100);
            foreach ($chunks as $chunk) {
                DB::table('diagnosa_emergency_obat')->insert($chunk);
            }
            
            $this->command->info('Diagnosa Emergency - Obat pivot table seeded successfully!');
            $this->command->info('Total relationships created: ' . count($pivotData));
        } else {
            $this->command->warn('No relationships were created. Check if obat names match.');
        }
    }
}