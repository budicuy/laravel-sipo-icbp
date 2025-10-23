@extends('layouts.app')

@section('title', 'Manajemen Stok Obat')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-pills"></i> Manajemen Stok Obat
                    </h5>
                    <div>
                        <a href="{{ route('obat.create') }}" class="btn btn-light btn-sm me-2">
                            <i class="fas fa-plus"></i> Tambah Obat Baru
                        </a>
                        <button class="btn btn-light btn-sm" id="refreshBtn">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter dan Search -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="searchInput"
                                       placeholder="Cari nama obat..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="stokStatusFilter">
                                <option value="">Semua Status</option>
                                <option value="habis" {{ request('stok_status') == 'habis' ? 'selected' : '' }}>
                                    Stok Habis (≤ 0)
                                </option>
                                <option value="rendah" {{ request('stok_status') == 'rendah' ? 'selected' : '' }}>
                                    Stok Rendah (1-10)
                                </option>
                                <option value="tersedia" {{ request('stok_status') == 'tersedia' ? 'selected' : '' }}>
                                    Stok Tersedia (> 10)
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="sortBy">
                                <option value="nama_obat" {{ request('sort') == 'nama_obat' ? 'selected' : '' }}>
                                    Urutkan: Nama Obat
                                </option>
                                <option value="sisa_stok" {{ request('sort') == 'sisa_stok' ? 'selected' : '' }}>
                                    Urutkan: Sisa Stok
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="btn-group w-100" role="group">
                                <button class="btn btn-outline-secondary" id="tableViewBtn" title="Tabel View">
                                    <i class="fas fa-table"></i>
                                </button>
                                <button class="btn btn-outline-secondary" id="cardViewBtn" title="Card View">
                                    <i class="fas fa-th-large"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Stok Obat -->
                    <div class="table-responsive" id="tableView">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>
                                        <a href="{{ route('stok.index', array_merge(request()->query(), ['sort' => 'nama_obat', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                           class="text-white text-decoration-none">
                                            Nama Obat
                                            @if(request('sort') == 'nama_obat')
                                                <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Satuan</th>
                                    <th>Stok Awal</th>
                                    <th>
                                        <a href="{{ route('stok.index', array_merge(request()->query(), ['sort' => 'sisa_stok', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                           class="text-white text-decoration-none">
                                            Sisa Stok
                                            @if(request('sort') == 'sisa_stok')
                                                <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($obatsWithStok as $index => $obat)
                                    <tr class="stok-row"
                                        data-obat-name="{{ strtolower($obat->nama_obat) }}"
                                        data-stok-status="{{ $obat->sisa_stok <= 0 ? 'habis' : ($obat->sisa_stok <= 10 ? 'rendah' : 'tersedia') }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-bold">{{ $obat->nama_obat }}</div>
                                            @if($obat->keterangan)
                                                <small class="text-muted">{{ $obat->keterangan }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $obat->satuanObat->nama_satuan ?? '-' }}</td>
                                        <td>{{ number_format($obat->stok_awal) }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-bold {{ $obat->sisa_stok <= 0 ? 'text-danger' : ($obat->sisa_stok <= 10 ? 'text-warning' : 'text-success') }} me-2">
                                                    {{ number_format($obat->sisa_stok) }}
                                                </span>
                                                <div class="progress" style="width: 100px; height: 8px;">
                                                    <div class="progress-bar {{ $obat->sisa_stok <= 0 ? 'bg-danger' : ($obat->sisa_stok <= 10 ? 'bg-warning' : 'bg-success') }}"
                                                         role="progressbar"
                                                         style="width: {{ min(($obat->sisa_stok / max($obat->stok_awal, 1)) * 100, 100) }}%">
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($obat->sisa_stok <= 0)
                                                <span class="badge bg-danger">Habis</span>
                                            @elseif($obat->sisa_stok <= 10)
                                                <span class="badge bg-warning">Rendah</span>
                                            @else
                                                <span class="badge bg-success">Tersedia</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('stok.show', $obat->id_obat) }}"
                                                   class="btn btn-sm btn-outline-primary" title="Detail Stok">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('obat.edit', $obat->id_obat) }}"
                                                   class="btn btn-sm btn-outline-secondary" title="Edit Obat">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>Tidak ada data obat yang ditemukan.</p>
                                            <a href="{{ route('obat.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Tambah Obat Baru
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Cards untuk Stok Obat (Hidden by default) -->
                    <div class="row" id="cardView" style="display: none;">
                        @forelse($obatsWithStok as $obat)
                            <div class="col-md-6 col-lg-4 mb-4 obat-card"
                                 data-obat-name="{{ strtolower($obat->nama_obat) }}"
                                 data-stok-status="{{ $obat->sisa_stok <= 0 ? 'habis' : ($obat->sisa_stok <= 10 ? 'rendah' : 'tersedia') }}">
                                <div class="card h-100 {{ $obat->sisa_stok <= 0 ? 'border-danger' : ($obat->sisa_stok <= 10 ? 'border-warning' : '') }}">
                                    <div class="card-header d-flex justify-content-between align-items-center {{ $obat->sisa_stok <= 0 ? 'bg-danger text-white' : ($obat->sisa_stok <= 10 ? 'bg-warning text-dark' : 'bg-light') }}">
                                        <h6 class="mb-0 fw-bold">{{ $obat->nama_obat }}</h6>
                                        <span class="badge {{ $obat->sisa_stok <= 0 ? 'bg-light text-dark' : ($obat->sisa_stok <= 10 ? 'bg-dark text-white' : 'bg-primary text-white') }}">
                                            {{ $obat->satuanObat->nama_satuan ?? '-' }}
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <h2 class="display-4 fw-bold {{ $obat->sisa_stok <= 0 ? 'text-danger' : ($obat->sisa_stok <= 10 ? 'text-warning' : 'text-success') }}">
                                                {{ number_format($obat->sisa_stok) }}
                                            </h2>
                                            <p class="text-muted">Sisa Stok</p>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <small class="text-muted">Stok Awal:</small>
                                                <p class="mb-0 fw-bold">{{ number_format($obat->stok_awal) }}</p>
                                            </div>
                                            <div>
                                                <small class="text-muted">Status:</small>
                                                <p class="mb-0">
                                                    @if($obat->sisa_stok <= 0)
                                                        <span class="badge bg-danger">Habis</span>
                                                    @elseif($obat->sisa_stok <= 10)
                                                        <span class="badge bg-warning">Rendah</span>
                                                    @else
                                                        <span class="badge bg-success">Tersedia</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        @if($obat->keterangan)
                                            <div class="mb-3">
                                                <small class="text-muted">Keterangan:</small>
                                                <p class="mb-0 small">{{ $obat->keterangan }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <div class="btn-group w-100" role="group">
                                            <a href="{{ route('stok.show', $obat->id_obat) }}"
                                               class="btn btn-primary flex-fill">
                                                <i class="fas fa-chart-line"></i> Detail Stok
                                            </a>
                                            <a href="{{ route('obat.edit', $obat->id_obat) }}"
                                               class="btn btn-outline-secondary flex-fill">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <i class="fas fa-pills fa-5x text-muted mb-4"></i>
                                    <h4 class="text-muted">Tidak ada data obat yang ditemukan</h4>
                                    <p class="text-muted">Tambahkan obat baru untuk memulai manajemen stok</p>
                                    <a href="{{ route('obat.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Tambah Obat Baru
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Summary -->
                    @if($obatsWithStok->count() > 0)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Ringkasan Stok Obat</h5>
                                        <div class="row text-center">
                                            <div class="col-md-3">
                                                <div class="card border-0 bg-white shadow-sm">
                                                    <div class="card-body">
                                                        <h3 class="mb-0 text-primary">{{ $obatsWithStok->count() }}</h3>
                                                        <p class="mb-0 text-muted">Total Obat</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card border-0 bg-white shadow-sm">
                                                    <div class="card-body">
                                                        <h3 class="mb-0 text-danger">{{ $obatsWithStok->where('sisa_stok', '<=', 0)->count() }}</h3>
                                                        <p class="mb-0 text-muted">Stok Habis</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card border-0 bg-white shadow-sm">
                                                    <div class="card-body">
                                                        <h3 class="mb-0 text-warning">{{ $obatsWithStok->where('sisa_stok', '>', 0)->where('sisa_stok', '<=', 10)->count() }}</h3>
                                                        <p class="mb-0 text-muted">Stok Rendah</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card border-0 bg-white shadow-sm">
                                                    <div class="card-body">
                                                        <h3 class="mb-0 text-success">{{ $obatsWithStok->where('sisa_stok', '>', 10)->count() }}</h3>
                                                        <p class="mb-0 text-muted">Stok Tersedia</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Toggle between table and card view
    $('#tableViewBtn').on('click', function() {
        $('#tableView').show();
        $('#cardView').hide();
        $(this).removeClass('btn-outline-secondary').addClass('btn-secondary');
        $('#cardViewBtn').removeClass('btn-secondary').addClass('btn-outline-secondary');
    });

    $('#cardViewBtn').on('click', function() {
        $('#tableView').hide();
        $('#cardView').show();
        $(this).removeClass('btn-outline-secondary').addClass('btn-secondary');
        $('#tableViewBtn').removeClass('btn-secondary').addClass('btn-outline-secondary');
    });

    // Fungsi untuk filter dan search
    function filterObats() {
        const searchTerm = $('#searchInput').val().toLowerCase();
        const statusFilter = $('#stokStatusFilter').val();

        $('.stok-row, .obat-card').each(function() {
            const obatName = $(this).data('obat-name');
            const stokStatus = $(this).data('stok-status');

            const matchesSearch = obatName.includes(searchTerm);
            const matchesStatus = !statusFilter || stokStatus === statusFilter;

            if (matchesSearch && matchesStatus) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    // Event listeners
    $('#searchInput').on('keyup', filterObats);
    $('#stokStatusFilter').on('change', filterObats);

    // Sort functionality
    $('#sortBy').on('change', function() {
        const sortBy = $(this).val();
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('sort', sortBy);
        window.location.href = currentUrl.toString();
    });

    // Refresh button
    $('#refreshBtn').on('click', function() {
        $(this).find('i').addClass('fa-spin');
        window.location.reload();
    });

    // Auto-refresh setiap 60 detik untuk update stok real-time
    setInterval(function() {
        $('#refreshBtn').find('i').addClass('fa-spin');
        window.location.reload();
    }, 60000);
});
</script>
@endsection
