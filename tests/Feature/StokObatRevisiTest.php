<?php

namespace Tests\Feature;

use App\Models\Obat;
use App\Models\StokObat;
use App\Models\Keluhan;
use App\Models\RekamMedis;
use App\Models\Keluarga;
use App\Models\Karyawan;
use App\Models\SatuanObat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StokObatRevisiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup test environment
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Buat data satuan obat
        $satuanObat = SatuanObat::create([
            'nama_satuan' => 'Tablet'
        ]);
        
        // Buat data obat
        $this->obat = Obat::create([
            'nama_obat' => 'Paracetamol',
            'keterangan' => 'Obat untuk mengurangi demam',
            'id_satuan' => $satuanObat->id_satuan,
            'tanggal_update' => now(),
        ]);
    }

    /**
     * Test membuat stok awal pertama kali
     */
    public function test_buat_stok_awal_pertama(): void
    {
        $periode = '10-24';
        $jumlahStok = 100;
        
        // Buat stok awal pertama kali
        $stokObat = StokObat::buatStokAwalPertama($this->obat->id_obat, $periode, $jumlahStok);
        
        // Assertions
        $this->assertInstanceOf(StokObat::class, $stokObat);
        $this->assertEquals($this->obat->id_obat, $stokObat->id_obat);
        $this->assertEquals($periode, $stokObat->periode);
        $this->assertEquals(0, $stokObat->stok_awal);
        $this->assertEquals($jumlahStok, $stokObat->stok_masuk);
        $this->assertEquals(0, $stokObat->stok_pakai);
        $this->assertEquals($jumlahStok, $stokObat->stok_akhir);
        $this->assertTrue($stokObat->is_initial_stok);
        
        // Test apakah obat sudah memiliki stok awal
        $this->assertTrue(StokObat::hasInitialStok($this->obat->id_obat));
    }

    /**
     * Test menambah stok masuk
     */
    public function test_tambah_stok_masuk(): void
    {
        // Buat stok awal pertama
        StokObat::buatStokAwalPertama($this->obat->id_obat, '09-24', 50);
        
        $periode = '10-24';
        $jumlahStok = 30;
        $keterangan = 'Stok masuk dari supplier';
        
        // Tambah stok masuk
        $stokObat = StokObat::tambahStokMasuk($this->obat->id_obat, $periode, $jumlahStok, $keterangan);
        
        // Assertions
        $this->assertInstanceOf(StokObat::class, $stokObat);
        $this->assertEquals($this->obat->id_obat, $stokObat->id_obat);
        $this->assertEquals($periode, $stokObat->periode);
        $this->assertEquals(50, $stokObat->stok_awal); // Diambil dari stok akhir bulan sebelumnya
        $this->assertEquals($jumlahStok, $stokObat->stok_masuk);
        $this->assertEquals($keterangan, $stokObat->keterangan);
        $this->assertFalse($stokObat->is_initial_stok);
    }

    /**
     * Test menghitung stok pakai dari keluhan
     */
    public function test_hitung_stok_pakai_dari_keluhan(): void
    {
        // Buat data yang diperlukan
        $karyawan = Karyawan::factory()->create();
        $keluarga = Keluarga::factory()->create(['id_karyawan' => $karyawan->id_karyawan]);
        $rekamMedis = RekamMedis::factory()->create([
            'id_keluarga' => $keluarga->id_keluarga,
            'tanggal_periksa' => now()->format('Y-m-d')
        ]);
        
        // Buat keluhan dengan obat
        Keluhan::create([
            'id_rekam' => $rekamMedis->id_rekam,
            'id_obat' => $this->obat->id_obat,
            'jumlah_obat' => 10,
            'terapi' => 'Obat',
            'keterangan' => 'Demam',
        ]);
        
        Keluhan::create([
            'id_rekam' => $rekamMedis->id_rekam,
            'id_obat' => $this->obat->id_obat,
            'jumlah_obat' => 5,
            'terapi' => 'Obat',
            'keterangan' => 'Sakit kepala',
        ]);
        
        // Hitung stok pakai
        $stokPakai = StokObat::hitungStokPakaiDariKeluhan($this->obat->id_obat, now()->format('m-y'));
        
        // Assertions
        $this->assertEquals(15, $stokPakai);
    }

    /**
     * Test menghitung stok akhir
     */
    public function test_hitung_stok_akhir(): void
    {
        $stokAwal = 50;
        $stokPakai = 20;
        $stokMasuk = 30;
        
        // Hitung stok akhir
        $stokAkhir = StokObat::hitungStokAkhir($stokAwal, $stokPakai, $stokMasuk);
        
        // Assertions
        $this->assertEquals(60, $stokAkhir); // 50 + 30 - 20
    }

    /**
     * Test mendapatkan stok akhir bulan sebelumnya
     */
    public function test_get_stok_akhir_bulan_sebelumnya(): void
    {
        // Buat stok untuk bulan sebelumnya
        StokObat::create([
            'id_obat' => $this->obat->id_obat,
            'periode' => '09-24',
            'stok_awal' => 0,
            'stok_masuk' => 100,
            'stok_pakai' => 30,
            'stok_akhir' => 70,
            'is_initial_stok' => true,
        ]);
        
        // Ambil stok akhir bulan sebelumnya
        $stokAkhir = StokObat::getStokAkhirBulanSebelumnya($this->obat->id_obat, '10-24');
        
        // Assertions
        $this->assertEquals(70, $stokAkhir);
    }

    /**
     * Test update stok awal dari bulan sebelumnya
     */
    public function test_update_stok_awal_from_previous_month(): void
    {
        // Buat stok untuk bulan sebelumnya
        StokObat::create([
            'id_obat' => $this->obat->id_obat,
            'periode' => '09-24',
            'stok_awal' => 0,
            'stok_masuk' => 100,
            'stok_pakai' => 30,
            'stok_akhir' => 70,
            'is_initial_stok' => true,
        ]);
        
        // Update stok awal dari bulan sebelumnya
        $stokAwal = StokObat::updateStokAwalFromPreviousMonth($this->obat->id_obat, '10-24');
        
        // Assertions
        $this->assertEquals(70, $stokAwal);
        
        // Verifikasi stok yang diupdate
        $stokObat = StokObat::where('id_obat', $this->obat->id_obat)
                           ->where('periode', '10-24')
                           ->first();
        
        $this->assertNotNull($stokObat);
        $this->assertEquals(70, $stokObat->stok_awal);
    }

    /**
     * Test validasi konsistensi data stok
     */
    public function test_validate_stok_consistency(): void
    {
        // Buat stok dengan data yang konsisten
        $stokObat = StokObat::create([
            'id_obat' => $this->obat->id_obat,
            'periode' => '10-24',
            'stok_awal' => 50,
            'stok_masuk' => 30,
            'stok_pakai' => 20,
            'stok_akhir' => 60, // 50 + 30 - 20
        ]);
        
        // Validasi konsistensi
        $validation = $stokObat->validateStokConsistency();
        
        // Assertions
        $this->assertTrue($validation['is_valid']);
        $this->assertEquals(60, $validation['expected_stok_akhir']);
        $this->assertEquals(60, $validation['actual_stok_akhir']);
        $this->assertEquals(0, $validation['difference']);
    }

    /**
     * Test validasi konsistensi data stok yang tidak konsisten
     */
    public function test_validate_inconsistent_stok_consistency(): void
    {
        // Buat stok dengan data yang tidak konsisten
        $stokObat = StokObat::create([
            'id_obat' => $this->obat->id_obat,
            'periode' => '10-24',
            'stok_awal' => 50,
            'stok_masuk' => 30,
            'stok_pakai' => 20,
            'stok_akhir' => 70, // Seharusnya 60, bukan 70
        ]);
        
        // Validasi konsistensi
        $validation = $stokObat->validateStokConsistency();
        
        // Assertions
        $this->assertFalse($validation['is_valid']);
        $this->assertEquals(60, $validation['expected_stok_akhir']);
        $this->assertEquals(70, $validation['actual_stok_akhir']);
        $this->assertEquals(10, $validation['difference']);
    }

    /**
     * Test update stok pakai per periode
     */
    public function test_update_stok_pakai_per_periode(): void
    {
        // Buat data yang diperlukan
        $karyawan = Karyawan::factory()->create();
        $keluarga = Keluarga::factory()->create(['id_karyawan' => $karyawan->id_karyawan]);
        $rekamMedis = RekamMedis::factory()->create([
            'id_keluarga' => $keluarga->id_keluarga,
            'tanggal_periksa' => now()->format('Y-m-d')
        ]);
        
        // Buat keluhan dengan obat
        Keluhan::create([
            'id_rekam' => $rekamMedis->id_rekam,
            'id_obat' => $this->obat->id_obat,
            'jumlah_obat' => 10,
            'terapi' => 'Obat',
            'keterangan' => 'Demam',
        ]);
        
        // Buat stok obat
        $stokObat = StokObat::create([
            'id_obat' => $this->obat->id_obat,
            'periode' => now()->format('m-y'),
            'stok_awal' => 50,
            'stok_masuk' => 30,
            'stok_pakai' => 0, // Akan diupdate
            'stok_akhir' => 80, // Akan diupdate
        ]);
        
        // Update stok pakai per periode
        $updatedCount = StokObat::updateStokPakaiPerPeriode(now()->format('m-y'));
        
        // Assertions
        $this->assertEquals(1, $updatedCount);
        
        // Refresh data dan verifikasi
        $stokObat->refresh();
        $this->assertEquals(10, $stokObat->stok_pakai);
        $this->assertEquals(70, $stokObat->stok_akhir); // 50 + 30 - 10
    }
}