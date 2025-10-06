@extends('layouts.app')

@section('page-title', 'Data Karyawan')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <div class="bg-gradient-to-r from-blue-600 to-cyan-600 p-3 rounded-lg shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            Data Karyawan
        </h1>
        <p class="text-gray-600 mt-2 ml-1">Manajemen data karyawan perusahaan</p>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Action Buttons Section -->
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-blue-50">
            <div class="flex flex-wrap gap-3 items-center">
                <a href="{{ route('karyawan.create') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Karyawan
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
        <div class="p-6 bg-gradient-to-r from-blue-50 to-cyan-50 border-b border-gray-200">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <h3 class="text-sm font-semibold text-gray-800">Filter Data</h3>
            </div>

            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Departemen</label>
                    <div class="relative">
                        <select name="departemen" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none bg-white pr-10">
                            <option value="">-- Semua Departemen --</option>
                            @isset($departemens)
                                @foreach($departemens as $dept)
                                    <option value="{{ $dept->id_departemen }}" {{ request('departemen') == $dept->id_departemen ? 'selected' : '' }}>{{ $dept->nama_departemen }}</option>
                                @endforeach
                            @endisset
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="md:col-span-2 flex items-end gap-2">
                    <input type="text" name="q" value="{{ request('q') }}" class="flex-1 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" placeholder="Cari karyawan...">
                    <button class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('karyawan.index') }}" class="px-5 py-2.5 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">Reset</a>
                </div>
            </form>
        </div>

        <!-- Table Controls -->
        <div class="p-5 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Tampilkan</label>
                <select class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm bg-white">
                    <option>50</option>
                    <option>100</option>
                    <option>200</option>
                </select>
                <span class="text-sm font-medium text-gray-700">data</span>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Pencarian:</label>
                <div class="relative">
                    <input type="text" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm w-64" placeholder="Cari karyawan...">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-800 to-gray-900">
                        <th class="px-4 py-4 text-left">
                            <input type="checkbox" onclick="toggleAll(this)" class="rounded border-gray-400 text-blue-600 focus:ring-2 focus:ring-blue-500">
                        </th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">No</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">NIK</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Jenis Kelamin</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Departemen</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">No HP</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Tanggal Lahir</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Alamat</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($karyawans as $karyawan)
                    <tr class="hover:bg-blue-50 transition-colors">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <input name="ids[]" value="{{ $karyawan->id_karyawan }}" type="checkbox" class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500" data-id="{{ $karyawan->id_karyawan }}">
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ ($karyawans->currentPage() - 1) * $karyawans->perPage() + $loop->iteration }}</td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="text-sm font-semibold text-blue-600">{{ $karyawan->nik_karyawan }}</span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(Str::of($karyawan->nama_karyawan)->explode(' ')->map(fn($p) => Str::substr($p,0,1))->take(2)->implode('')) }}
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $karyawan->nama_karyawan }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $karyawan->jenis_kelamin }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ optional($karyawan->departemen)->nama_departemen }}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $karyawan->no_hp }}</td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-1 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ optional($karyawan->tanggal_lahir)->format('d-m-Y') }}
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-900 max-w-xs truncate" title="{{ $karyawan->alamat }}">{{ $karyawan->alamat }}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('karyawan.edit', $karyawan->id_karyawan) }}" class="inline-flex items-center justify-center w-9 h-9 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-all shadow-sm hover:shadow-md" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('karyawan.destroy', $karyawan->id_karyawan) }}" method="POST" onsubmit="return confirm('Hapus karyawan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inline-flex items-center justify-center w-9 h-9 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all shadow-sm hover:shadow-md" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @isset($karyawans)
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="text-sm text-gray-700 font-medium">
                Menampilkan <span class="font-semibold text-gray-900">{{ $karyawans->firstItem() }}</span> sampai <span class="font-semibold text-gray-900">{{ $karyawans->lastItem() }}</span> dari <span class="font-semibold text-gray-900">{{ $karyawans->total() }}</span> data
            </div>
            <div>
                {{ $karyawans->withQueryString()->links() }}
            </div>
        </div>
        @endisset
    </div>
</div>
@push('scripts')
<script>
function toggleAll(source) {
  const checkboxes = document.querySelectorAll('.row-checkbox');
  checkboxes.forEach(cb => cb.checked = source.checked);
}

function submitBulkDelete() {
  const ids = getSelectedIds();
  if (ids.length === 0) {
    alert('Pilih minimal satu karyawan untuk dihapus');
    return;
  }

  if (!confirm(`Apakah Anda yakin ingin menghapus ${ids.length} karyawan yang dipilih?`)) {
    return;
  }

  // Create form dynamically
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '{{ route("karyawan.bulkDelete") }}';

  // Add CSRF token
  const csrfInput = document.createElement('input');
  csrfInput.type = 'hidden';
  csrfInput.name = '_token';
  csrfInput.value = '{{ csrf_token() }}';
  form.appendChild(csrfInput);

  // Add IDs
  ids.forEach(id => {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'ids[]';
    input.value = id;
    form.appendChild(input);
  });

  // Append to body and submit
  document.body.appendChild(form);
  form.submit();
}

function getSelectedIds() {
  const nodes = Array.from(document.querySelectorAll('.row-checkbox:checked'));
  return nodes.map(n => n.value);
}

</script>
@endpush
@endsection
