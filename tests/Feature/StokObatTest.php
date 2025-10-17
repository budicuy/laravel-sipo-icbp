<?php

namespace Tests\Feature;

use App\Models\Obat;
use App\Models\StokObat;
use App\Models\JenisObat;
use App\Models\SatuanObat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StokObatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $jenisObat = JenisObat::create([
            'id_jenis_obat' => 1,
            'nama_jenis_obat' => 'Test Jenis'
        ]);

        $satuanObat = SatuanObat::create([
            'id_satuan' => 1,
            'nama_satuan' => 'Test Satuan'
        ]);
    }

    /**
     * Test getStokAkhirBulanSebelumnya function
     */
    public function test_get_stok_akhir_bulan_sebelumnya()
    {
        // Create test obat
        $obat = Obat::create([
            'nama_obat' => 'Test Obat',
            'id_jenis_obat' => 1,
            'id_satuan' => 1,
            'jumlah_per_kemasan' => 10,
            'harga_per_kemasan' => 10000,
            'harga_per_satuan' => 1000
        ]);

        // Create stok for previous month (09-24)
        StokObat::create([
            'id_obat' => $obat->id_obat,
            'periode' => '09-24',
            'stok_awal' => 100,
            'stok_pakai' => 20,
            'stok_masuk' => 30,
            'stok_akhir' => 110
        ]);

        // Test getting stok akhir from previous month
        $stokAkhir = StokObat::getStokAkhirBulanSebelumnya($obat->id_obat, '10-24');
        $this->assertEquals(110, $stokAkhir);

        // Test when no previous month data exists
        $stokAkhir = StokObat::getStokAkhirBulanSebelumnya($obat->id_obat, '11-24');
        $this->assertEquals(0, $stokAkhir);
    }

    /**
     * Test hitungStokAkhir function
     */
    public function test_hitung_stok_akhir()
    {
        // Test normal calculation with new formula: Stok Awal + Stok Masuk - Stok Pakai
        $stokAkhir = StokObat::hitungStokAkhir(100, 20, 30);
        $this->assertEquals(110, $stokAkhir); // 100 + 30 - 20 = 110

        // Test with zero values
        $stokAkhir = StokObat::hitungStokAkhir(0, 0, 0);
        $this->assertEquals(0, $stokAkhir);

        // Test with negative result (stok habis)
        $stokAkhir = StokObat::hitungStokAkhir(50, 60, 0);
        $this->assertEquals(-10, $stokAkhir); // 50 + 0 - 60 = -10
    }

    /**
     * Test validateStokConsistency function
     */
    public function test_validate_stok_consistency()
    {
        // Create test obat
        $obat = Obat::create([
            'nama_obat' => 'Test Obat 2',
            'id_jenis_obat' => 1,
            'id_satuan' => 1,
            'jumlah_per_kemasan' => 10,
            'harga_per_kemasan' => 10000,
            'harga_per_satuan' => 1000
        ]);

        // Create consistent stok data with new formula
        $stok = StokObat::create([
            'id_obat' => $obat->id_obat,
            'periode' => '10-24',
            'stok_awal' => 100,
            'stok_pakai' => 20,
            'stok_masuk' => 30,
            'stok_akhir' => 110 // 100 + 30 - 20 = 110
        ]);

        $validation = $stok->validateStokConsistency();
        $this->assertTrue($validation['is_valid']);
        $this->assertEquals(110, $validation['expected_stok_akhir']);
        $this->assertEquals(110, $validation['actual_stok_akhir']);
        $this->assertEquals(0, $validation['difference']);

        // Create inconsistent stok data with new formula
        $stok2 = StokObat::create([
            'id_obat' => $obat->id_obat,
            'periode' => '11-24',
            'stok_awal' => 100,
            'stok_pakai' => 20,
            'stok_masuk' => 30,
            'stok_akhir' => 100 // Should be 110 (100 + 30 - 20)
        ]);

        $validation2 = $stok2->validateStokConsistency();
        $this->assertFalse($validation2['is_valid']);
        $this->assertEquals(110, $validation2['expected_stok_akhir']);
        $this->assertEquals(100, $validation2['actual_stok_akhir']);
        $this->assertEquals(-10, $validation2['difference']);
    }

    /**
     * Test updateStokAwalFromPreviousMonth function
     */
    public function test_update_stok_awal_from_previous_month()
    {
        // Create test obat
        $obat = Obat::create([
            'nama_obat' => 'Test Obat 3',
            'id_jenis_obat' => 1,
            'id_satuan' => 1,
            'jumlah_per_kemasan' => 10,
            'harga_per_kemasan' => 10000,
            'harga_per_satuan' => 1000
        ]);

        // Create stok for previous month
        StokObat::create([
            'id_obat' => $obat->id_obat,
            'periode' => '09-24',
            'stok_awal' => 100,
            'stok_pakai' => 20,
            'stok_masuk' => 30,
            'stok_akhir' => 110
        ]);

        // Update stok awal for new month
        $stokAwal = StokObat::updateStokAwalFromPreviousMonth($obat->id_obat, '10-24');
        $this->assertEquals(110, $stokAwal);

        // Verify the stok was created/updated
        $stok = StokObat::where('id_obat', $obat->id_obat)
                        ->where('periode', '10-24')
                        ->first();
        $this->assertNotNull($stok);
        $this->assertEquals(110, $stok->stok_awal);
    }

    /**
     * Test stok calculation across multiple months
     */
    public function test_stok_calculation_across_months()
    {
        // Create test obat
        $obat = Obat::create([
            'nama_obat' => 'Test Obat 4',
            'id_jenis_obat' => 1,
            'id_satuan' => 1,
            'jumlah_per_kemasan' => 10,
            'harga_per_kemasan' => 10000,
            'harga_per_satuan' => 1000
        ]);

        // Month 1: Initial stok
        StokObat::create([
            'id_obat' => $obat->id_obat,
            'periode' => '08-24',
            'stok_awal' => 0,
            'stok_pakai' => 0,
            'stok_masuk' => 100,
            'stok_akhir' => 100
        ]);

        // Month 2: Use previous month's ending stock
        $stokAwal = StokObat::getStokAkhirBulanSebelumnya($obat->id_obat, '09-24');
        $this->assertEquals(100, $stokAwal);

        StokObat::create([
            'id_obat' => $obat->id_obat,
            'periode' => '09-24',
            'stok_awal' => 100,
            'stok_pakai' => 20,
            'stok_masuk' => 10,
            'stok_akhir' => 90 // 100 - 20 + 10 = 90
        ]);

        // Month 3: Use previous month's ending stock
        $stokAwal = StokObat::getStokAkhirBulanSebelumnya($obat->id_obat, '10-24');
        $this->assertEquals(90, $stokAwal);

        StokObat::create([
            'id_obat' => $obat->id_obat,
            'periode' => '10-24',
            'stok_awal' => 90,
            'stok_pakai' => 30,
            'stok_masuk' => 20,
            'stok_akhir' => 80 // 90 - 30 + 20 = 80
        ]);

        // Verify consistency
        $stoks = StokObat::where('id_obat', $obat->id_obat)->get();
        foreach ($stoks as $stok) {
            $validation = $stok->validateStokConsistency();
            $this->assertTrue($validation['is_valid'],
                "Stok consistency failed for periode {$stok->periode}: " .
                "Expected {$validation['expected_stok_akhir']}, got {$validation['actual_stok_akhir']}"
            );
        }
    }

    /**
     * Test year transition (December to January)
     */
    public function test_year_transition()
    {
        // Create test obat
        $obat = Obat::create([
            'nama_obat' => 'Test Obat 5',
            'id_jenis_obat' => 1,
            'id_satuan' => 1,
            'jumlah_per_kemasan' => 10,
            'harga_per_kemasan' => 10000,
            'harga_per_satuan' => 1000
        ]);

        // December 2024
        StokObat::create([
            'id_obat' => $obat->id_obat,
            'periode' => '12-24',
            'stok_awal' => 100,
            'stok_pakai' => 20,
            'stok_masuk' => 10,
            'stok_akhir' => 90
        ]);

        // January 2025 should get December's ending stock
        $stokAwal = StokObat::getStokAkhirBulanSebelumnya($obat->id_obat, '01-25');
        $this->assertEquals(90, $stokAwal);
    }
}
