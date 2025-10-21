<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vendor;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = [
            ['nama_vendor' => 'PT. Tropis Service'],
            ['nama_vendor' => 'PT. Fadanara Berkah Bersama'],
            ['nama_vendor' => 'PT. Garda Bhakti Nusantara'],
            ['nama_vendor' => 'CV. Wafaiza Bati-Bati'],
            ['nama_vendor' => 'Guest'],
            ['nama_vendor' => 'PT. Rentokill Indonesia'],
            ['nama_vendor' => 'CV. Venus'],
            ['nama_vendor' => 'CV. Rahmat Agung'],
            ['nama_vendor' => 'KOPKAR PJA'],
        ];

        foreach ($vendors as $vendor) {
            Vendor::create($vendor);
        }
    }
}