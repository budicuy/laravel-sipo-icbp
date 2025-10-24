@extends('layouts.app')

@section('title', 'Detail Stok Obat - ' . $obat->nama_obat)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('stok.index') }}">Manajemen Stok</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $obat->nama_obat }}</li>
                </ol>
            </nav>

            <!-- Info Obat -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-pills"></i> Informasi Obat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nama Obat:</strong></td>
                                    <td>{{ $obat->nama_obat }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Satuan:</strong></td>
                                    <td>{{ $obat->satuanObat->nama_satuan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Stok Awal:</strong></td>
                                    <td>{{ number_format($obat->stok_awal) }} {{ $obat->satuanObat->nama_satuan ?? '' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Sisa Stok Saat Ini:</strong></td>
                                    <td>
                                        <span class="fw-bold {{ $sisaStok <= 0 ? 'text-danger' : ($sisaStok <= 10 ? 'text-warning' : 'text-success') }} fs-5">
                                            {{ number_format($sisaStok) }} {{ $obat->satuanObat->nama_satuan ?? '' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status Stok:</strong></td>
                                    <td>
                                        @if($sisaStok <= 0)
                                            <span class="badge bg-danger fs-6">Habis</span>
                                        @elseif($sisaStok <= 10)
                                            <span class="badge bg-warning fs-6">Rendah</span>
                                        @else
                                            <span class="badge bg-success fs-6">Tersedia</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($obat->keterangan)
                                <tr>
                                    <td><strong>Keterangan:</strong></td>
                                    <td>{{ $obat->keterangan }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Tambah Stok Masuk -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle"></i> Tambah Stok Masuk Bulan Ini
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('stok.masuk.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="obat_id" value="{{ $obat->id_obat }}">

                        <div class="row">
                            <div class="col-md-4">
                                <label for="jumlah_stok_masuk" class="form-label">Jumlah Stok Masuk</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="jumlah_stok_masuk"
                                           name="jumlah_stok_masuk" min="1" required>
                                    <span class="input-group-text">{{ $obat->satuanObat->nama_satuan ?? '' }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label><br>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Tambah Stok
                                </button>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Periode Saat Ini:</label>
                                <p class="form-control-plaintext fw-bold">
                                    {{ date('F Y') }}
                                </p>
                            </div>
                        </div>

                        @if($stokBulananIni && $stokBulananIni->stok_masuk > 0)
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i>
                            Stok masuk untuk bulan ini: <strong>{{ number_format($stokBulananIni->stok_masuk) }} {{ $obat->satuanObat->nama_satuan ?? '' }}</strong>
                        </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Riwayat Stok Bulanan -->
            <div class="card">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-history"></i> History Stok Obat Per Bulan
                    </h5>
                    <span class="badge bg-light text-dark">{{ $riwayatStok->count() }} periode</span>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Rumus Perhitungan:</strong> Stok Akhir = Stok Awal + Total Stok Masuk - Total Stok Pakai
                    </div>

                    @forelse($riwayatStok as $index => $stok)
                        @php
                            // Hitung stok akhir bulanan dengan rumus: stok_awal + total_stok_masuk - total_stok_pakai
                            $totalStokMasuk = \App\Models\StokBulanan::where('obat_id', $obat->id_obat)
                                                    ->where(function($query) use ($stok) {
                                                        $query->where('tahun', '<', $stok->tahun)
                                                              ->orWhere(function($query) use ($stok) {
                                                                  $query->where('tahun', $stok->tahun)
                                                                         ->where('bulan', '<=', $stok->bulan);
                                                              });
                                                    })
                                                    ->sum('stok_masuk');

                            $totalStokPakai = \App\Models\StokBulanan::where('obat_id', $obat->id_obat)
                                                    ->where(function($query) use ($stok) {
                                                        $query->where('tahun', '<', $stok->tahun)
                                                              ->orWhere(function($query) use ($stok) {
                                                                  $query->where('tahun', $stok->tahun)
                                                                         ->where('bulan', '<=', $stok->bulan);
                                                              });
                                                    })
                                                    ->sum('stok_pakai');

                            $stokAkhirBulanan = $obat->stok_awal + $totalStokMasuk - $totalStokPakai;
                        @endphp

                        <div class="card mb-3 {{ $stokAkhirBulanan <= 0 ? 'border-danger' : ($stokAkhirBulanan <= 10 ? 'border-warning' : '') }}">
                            <div class="card-header d-flex justify-content-between align-items-center {{ $stokAkhirBulanan <= 0 ? 'bg-danger text-white' : ($stokAkhirBulanan <= 10 ? 'bg-warning text-dark' : 'bg-light') }}">
                                <h6 class="mb-0 fw-bold">{{ $stok->periode }}</h6>
                                <div>
                                    <span class="badge {{ $stokAkhirBulanan <= 0 ? 'bg-light text-dark' : ($stokAkhirBulanan <= 10 ? 'bg-dark text-white' : 'bg-primary text-white') }}">
                                        Sisa: {{ number_format($stokAkhirBulanan) }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 text-center">
                                        <div class="p-3 rounded bg-light">
                                            <h5 class="mb-1 text-success">{{ number_format($stok->stok_masuk) }}</h5>
                                            <small class="text-muted">Stok Masuk</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-center">
                                        <div class="p-3 rounded bg-light">
                                            <h5 class="mb-1 text-danger">{{ number_format($stok->stok_pakai) }}</h5>
                                            <small class="text-muted">Stok Pakai</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-center">
                                        <div class="p-3 rounded bg-light">
                                            <h5 class="mb-1 {{ $stokAkhirBulanan <= 0 ? 'text-danger' : ($stokAkhirBulanan <= 10 ? 'text-warning' : 'text-success') }}">
                                                {{ number_format($stokAkhirBulanan) }}
                                            </h5>
                                            <small class="text-muted">Stok Akhir</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-center">
                                        <div class="p-3 rounded bg-light">
                                            <div>
                                                @if($stok->stok_masuk > 0)
                                                    <span class="badge bg-success">Stok Ditambah</span>
                                                @endif
                                                @if($stok->stok_pakai > 0)
                                                    <span class="badge bg-danger">Stok Terpakai</span>
                                                @endif
                                                @if($stok->stok_masuk == 0 && $stok->stok_pakai == 0)
                                                    <span class="text-muted">Tidak ada aktivitas</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Progress bar untuk visualisasi stok -->
                                <div class="mt-3">
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar"
                                             style="width: {{ $stok->stok_masuk > 0 ? min(($stok->stok_masuk / max($stok->stok_masuk, $stok->stok_pakai, 1)) * 100, 100) : 0 }}%"
                                             title="Stok Masuk: {{ number_format($stok->stok_masuk) }}">
                                        </div>
                                        <div class="progress-bar bg-danger" role="progressbar"
                                             style="width: {{ $stok->stok_pakai > 0 ? min(($stok->stok_pakai / max($stok->stok_masuk, $stok->stok_pakai, 1)) * 100, 100) : 0 }}%"
                                             title="Stok Pakai: {{ number_format($stok->stok_pakai) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-history fa-5x text-muted mb-4"></i>
                            <h5 class="text-muted">Belum ada riwayat stok untuk obat ini</h5>
                            <p class="text-muted">Riwayat stok akan muncul setelah ada aktivitas stok masuk atau stok pakai</p>
                        </div>
                    @endforelse

                    @if($riwayatStok->count() > 0)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">Ringkasan Stok</h5>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="card border-0 bg-white shadow-sm">
                                                <div class="card-body text-center">
                                                    <h4 class="mb-0 text-primary">{{ number_format($obat->stok_awal) }}</h4>
                                                    <p class="mb-0 text-muted">Stok Awal</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card border-0 bg-white shadow-sm">
                                                <div class="card-body text-center">
                                                    <h4 class="mb-0 text-success">{{ number_format($riwayatStok->sum('stok_masuk')) }}</h4>
                                                    <p class="mb-0 text-muted">Total Stok Masuk</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card border-0 bg-white shadow-sm">
                                                <div class="card-body text-center">
                                                    <h4 class="mb-0 text-danger">{{ number_format($riwayatStok->sum('stok_pakai')) }}</h4>
                                                    <p class="mb-0 text-muted">Total Stok Pakai</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card border-0 bg-white shadow-sm">
                                                <div class="card-body text-center">
                                                    <h4 class="mb-0 {{ $sisaStok <= 0 ? 'text-danger' : ($sisaStok <= 10 ? 'text-warning' : 'text-success') }}">
                                                        {{ number_format($sisaStok) }}
                                                    </h4>
                                                    <p class="mb-0 text-muted">Sisa Stok Akhir</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Chart untuk visualisasi trend stok -->
                                    <div class="mt-4">
                                        <h6>Trend Stok (6 Bulan Terakhir)</h6>
                                        <div class="row">
                                            @php
                                                $lastSixMonths = $riwayatStok->take(6)->reverse();
                                                $maxStok = max($obat->stok_awal, $sisaStok, 1);
                                            @endphp

                                            @foreach($lastSixMonths as $stok)
                                                @php
                                                    // Hitung stok akhir untuk setiap bulan
                                                    $totalMasuk = \App\Models\StokBulanan::where('obat_id', $obat->id_obat)
                                                                    ->where(function($query) use ($stok) {
                                                                        $query->where('tahun', '<', $stok->tahun)
                                                                              ->orWhere(function($query) use ($stok) {
                                                                                  $query->where('tahun', $stok->tahun)
                                                                                         ->where('bulan', '<=', $stok->bulan);
                                                                              });
                                                                    })
                                                                    ->sum('stok_masuk');

                                                    $totalPakai = \App\Models\StokBulanan::where('obat_id', $obat->id_obat)
                                                                    ->where(function($query) use ($stok) {
                                                                        $query->where('tahun', '<', $stok->tahun)
                                                                              ->orWhere(function($query) use ($stok) {
                                                                                  $query->where('tahun', $stok->tahun)
                                                                                         ->where('bulan', '<=', $stok->bulan);
                                                                              });
                                                                    })
                                                                    ->sum('stok_pakai');

                                                    $stokAkhir = $obat->stok_awal + $totalMasuk - $totalPakai;
                                                @endphp

                                                <div class="col-md-2 text-center">
                                                    <small class="text-muted d-block">{{ \Carbon\Carbon::parse($stok->tahun . '-' . $stok->bulan . '-01')->format('M') }}</small>
                                                    <div class="progress" style="height: 100px;">
                                                        <div class="progress-bar {{ $stokAkhir <= 0 ? 'bg-danger' : ($stokAkhir <= 10 ? 'bg-warning' : 'bg-success') }}"
                                                             role="progressbar"
                                                             style="width: 100%; height: {{ ($stokAkhir / $maxStok) * 100 }}px; display: flex; align-items: flex-end;"
                                                             title="{{ $stok->periode }}: {{ number_format($stokAkhir) }}">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted d-block">{{ number_format($stokAkhir) }}</small>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Aksi -->
            <div class="row mt-4">
                <div class="col-12">
                    <a href="{{ route('stok.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Stok
                    </a>
                    <a href="{{ route('obat.edit', $obat->id_obat) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Obat
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus pada input jumlah stok masuk
    document.getElementById('jumlah_stok_masuk')?.focus();

    // Konfirmasi sebelum submit form
    const form = document.querySelector('form[action*="stok.masuk.store"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            const jumlah = document.getElementById('jumlah_stok_masuk').value;
            if (jumlah && jumlah > 0) {
                if (!confirm(`Apakah Anda yakin ingin menambah stok sebesar ${jumlah} {{ $obat->satuanObat->nama_satuan ?? '' }}?`)) {
                    e.preventDefault();
                }
            }
        });
    }
});
</script>
@endsection
