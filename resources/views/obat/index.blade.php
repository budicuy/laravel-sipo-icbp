@extends('layouts.app')

@section('page-title', 'Data Obat')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 p-3 rounded-lg shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
            </div>
            Data Obat
        </h1>
        <p class="text-gray-600 mt-2 ml-1">Manajemen data obat dan persediaan farmasi</p>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            });
        </script>
    @endif

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Action Buttons Section -->
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-green-50">
            <div class="flex flex-wrap gap-3 items-center">
                <a href="{{ route('obat.create') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Obat
                </a>

                <button type="button" onclick="submitBulkDelete()" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus Terpilih
                </button>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <h3 class="text-sm font-semibold text-gray-800">Filter & Pencarian</h3>
            </div>

            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Nama Obat</label>
                    <div class="flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" class="flex-1 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm" placeholder="Masukkan nama obat...">
                        <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Filter
                        </button>
                        <a href="{{ route('obat.index') }}" class="px-5 py-2.5 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">Reset</a>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Obat</label>
                    <select name="jenis_obat" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm bg-white shadow-sm">
                        <option value="">Semua Jenis</option>
                        @foreach($jenisObats as $jenis)
                            <option value="{{ $jenis->id_jenis_obat }}" {{ request('jenis_obat') == $jenis->id_jenis_obat ? 'selected' : '' }}>{{ $jenis->nama_jenis }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Satuan Obat</label>
                    <select name="satuan_obat" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm bg-white shadow-sm">
                        <option value="">Semua Satuan</option>
                        @foreach($satuanObats as $satuan)
                            <option value="{{ $satuan->id_satuan }}" {{ request('satuan_obat') == $satuan->id_satuan ? 'selected' : '' }}>{{ $satuan->nama_satuan }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <!-- Table Controls -->
        <div class="p-5 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white">
            <div class="text-sm text-gray-600">
                Total: <span class="font-semibold text-gray-900">{{ $obats->total() }}</span> obat
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gradient-to-r from-green-700 to-emerald-700">
                        <th class="px-4 py-4 text-left">
                            <input type="checkbox" onclick="toggleAll(this)" class="rounded border-gray-400 text-green-600 focus:ring-2 focus:ring-green-500">
                        </th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">No</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Nama Obat</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Jenis Obat</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Satuan</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Jml/Kemasan</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Harga/Kemasan</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Harga/Satuan</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Stok Awal</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Stok Masuk</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Stok Keluar</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Stok Akhir</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Keterangan</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Tanggal Update</th>
                        <th class="px-4 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($obats as $index => $obat)
                        <tr class="hover:bg-green-50 transition-colors">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="selected_ids[]" value="{{ $obat->id_obat }}" class="row-checkbox rounded border-gray-300 text-green-600 focus:ring-2 focus:ring-green-500">
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $obats->firstItem() + $index }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $obat->nama_obat }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $obat->jenisObat->nama_jenis ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $obat->satuanObat->nama_satuan ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $obat->jumlah_per_kemasan }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">Rp {{ number_format($obat->harga_per_kemasan, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">Rp {{ number_format($obat->harga_per_satuan, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $obat->stok_awal }}</td>
                            <td class="px-4 py-3 text-sm text-green-600 text-center">{{ $obat->stok_masuk }}</td>
                            <td class="px-4 py-3 text-sm text-red-600 text-center">{{ $obat->stok_keluar }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-center">{{ $obat->stok_akhir }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">{{ $obat->keterangan ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $obat->tanggal_update ? $obat->tanggal_update->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('obat.edit', $obat->id_obat) }}" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white text-xs font-medium rounded-md shadow-sm hover:shadow transition-all">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </a>
                                    <button onclick="deleteObat({{ $obat->id_obat }}, '{{ $obat->nama_obat }}')" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-xs font-medium rounded-md shadow-sm hover:shadow transition-all">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="px-4 py-8 text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="text-lg font-medium">Tidak ada data obat</p>
                                <p class="text-sm mt-1">Mulai dengan menambahkan obat baru</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $obats->links() }}
        </div>
    </div>
</div>

<script>
function toggleAll(checkbox) {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
}

function deleteObat(id, nama) {
    Swal.fire({
        title: 'Hapus Data Obat?',
        html: `Apakah Anda yakin ingin menghapus obat <strong>${nama}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/obat/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    }).then(() => {
                        location.reload();
                    });
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data', 'error');
            });
        }
    });
}

function submitBulkDelete() {
    const checkboxes = document.querySelectorAll('.row-checkbox:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value);

    if (ids.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak Ada Data Dipilih',
            text: 'Pilih minimal satu data untuk dihapus',
            confirmButtonColor: '#16a34a'
        });
        return;
    }

    Swal.fire({
        title: 'Hapus Data Terpilih?',
        html: `Apakah Anda yakin ingin menghapus <strong>${ids.length}</strong> obat yang dipilih?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus Semua!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/obat/bulk-delete', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids: ids })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    }).then(() => {
                        location.reload();
                    });
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data', 'error');
            });
        }
    });
}
</script>
@endsection
