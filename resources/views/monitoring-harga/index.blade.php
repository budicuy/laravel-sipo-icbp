@extends('layouts.app')

@section('title', 'Monitoring Harga Obat')

@section('page-title', 'Monitoring Harga Obat')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <div class="bg-gradient-to-r from-amber-600 to-orange-600 p-3 rounded-lg shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2a2 2 0 002 2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            Monitoring Harga Obat
        </h1>
        <p class="text-gray-600 mt-2 ml-1">Monitoring dan validasi harga obat untuk memastikan kelengkapan data</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6 border border-gray-100">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            <h3 class="text-lg font-semibold text-gray-800">Filter Monitoring</h3>
        </div>

        <form method="GET" action="{{ route('monitoring.harga.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Maksimal Bulan Tidak Update</label>
                    <div class="relative">
                        <select name="months" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent appearance-none bg-white pr-10">
                            <option value="1" {{ $months == '1' ? 'selected' : '' }}>1 Bulan</option>
                            <option value="3" {{ $months == '3' ? 'selected' : '' }}>3 Bulan</option>
                            <option value="6" {{ $months == '6' ? 'selected' : '' }}>6 Bulan</option>
                            <option value="12" {{ $months == '12' ? 'selected' : '' }}>12 Bulan</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full px-6 py-2.5 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white font-medium rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Filter Data
                    </button>
                </div>

                @if(auth()->user()->role === 'Super Admin')
                <div class="flex items-end">
                    <a href="{{ route('monitoring.harga.export', ['months' => $months]) }}" class="w-full px-6 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export CSV
                    </a>
                </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Obat -->
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 rounded-xl p-6 text-white">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
            <div class="relative">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-2 h-2 bg-blue-200 rounded-full animate-pulse"></div>
                    <p class="text-blue-100 text-sm font-medium">Total Obat</p>
                </div>
                <h3 class="text-3xl font-bold mb-1">{{ $stats['total_obat'] }}</h3>
                <p class="text-blue-200 text-xs">Semua obat terdaftar</p>
            </div>
        </div>

        <!-- Harga Terkini -->
        <div class="relative overflow-hidden bg-gradient-to-br from-green-500 via-green-600 to-green-700 rounded-xl p-6 text-white">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
            <div class="relative">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-2 h-2 bg-green-200 rounded-full animate-pulse"></div>
                    <p class="text-green-100 text-sm font-medium">Harga Terkini</p>
                </div>
                <h3 class="text-3xl font-bold mb-1">{{ $stats['obat_with_current_harga'] }}</h3>
                <p class="text-green-200 text-xs">Periode {{ $stats['current_periode'] }}</p>
            </div>
        </div>

        <!-- Harga Kadaluarsa -->
        <div class="relative overflow-hidden bg-gradient-to-br from-amber-500 via-amber-600 to-amber-700 rounded-xl p-6 text-white">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
            <div class="relative">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-2 h-2 bg-amber-200 rounded-full animate-pulse"></div>
                    <p class="text-amber-100 text-sm font-medium">Harga Kadaluarsa</p>
                </div>
                <h3 class="text-3xl font-bold mb-1">{{ $stats['obat_with_stale_harga'] }}</h3>
                <p class="text-amber-200 text-xs">Lebih dari {{ $months }} bulan</p>
            </div>
        </div>

    </div>

    <!-- Obat dengan Harga Kadaluarsa -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6 border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <div class="bg-gradient-to-r from-amber-600 to-orange-600 p-2 rounded-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Obat dengan Harga Kadaluarsa</h3>
                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                    {{ $obatStaleHarga->count() }} obat
                </span>
            </div>

            <button onclick="generateRecommendations()" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm font-medium rounded-lg transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Generate Rekomendasi
            </button>
        </div>

        <div class="overflow-hidden border border-gray-200 rounded-lg shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-800 to-gray-900">
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">ID Obat</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Nama Obat</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Periode Terakhir Harga</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Status</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($obatStaleHarga as $obat)
                        <tr class="hover:bg-amber-50 transition-colors">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900">{{ $obat->id_obat }}</span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900">{{ $obat->nama_obat }}</span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-600">{{ $obat->last_harga_periode }}</span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                                    Kadaluarsa
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <button onclick="viewHargaHistory({{ $obat->id_obat }})" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-all mr-2">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Histori
                                </button>
                                <a href="{{ route('harga-obat.edit', $obat->id_obat) }}" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition-all">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Update
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm font-medium">Tidak ada obat dengan harga kadaluarsa</span>
                                    <span class="text-xs text-gray-400 mt-1">Semua obat memiliki harga yang terkini</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal for Recommendations -->
<div id="recommendationsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Rekomendasi Harga Obat</h3>
                <button onclick="closeRecommendationsModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="recommendationsContent" class="space-y-4">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Modal for Harga History -->
<div id="historyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Histori Harga Obat</h3>
                <button onclick="closeHistoryModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="historyContent" class="space-y-4">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function generateRecommendations() {
    const targetPeriode = prompt('Masukkan periode target (format MM-YY):', '{{ now()->format("m-y") }}');

    if (!targetPeriode) return;

    fetch(`/monitoring/harga/recommendations?target_periode=${targetPeriode}`)
        .then(response => response.json())
        .then(data => {
            let content = '<div class="mb-4"><p class="text-sm text-gray-600">Rekomendasi harga untuk periode <strong>' + targetPeriode + '</strong></p></div>';

            if (data.length === 0) {
                content += '<div class="text-center py-8 text-gray-500"><p>Tidak ada rekomendasi harga untuk periode ini.</p></div>';
            } else {
                content += '<form id="bulkCreateForm">';
                content += '<div class="space-y-2 max-h-96 overflow-y-auto">';

                data.forEach((item, index) => {
                    content += `
                        <div class="border border-gray-200 rounded-lg p-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900">${item.nama_obat}</h4>
                                    <p class="text-sm text-gray-600">Harga terakhir: ${item.last_periode}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">Rp ${parseFloat(item.recommended_harga).toLocaleString('id-ID')}</p>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="recommendations[${index}][id_obat]" value="${item.id_obat}" checked class="form-checkbox h-4 w-4 text-blue-600">
                                        <span class="ml-2 text-sm text-gray-700">Gunakan</span>
                                    </label>
                                </div>
                            </div>
                            <input type="hidden" name="recommendations[${index}][harga_per_satuan]" value="${item.recommended_harga}">
                            <input type="hidden" name="recommendations[${index}][harga_per_kemasan]" value="${item.recommended_kemasan}">
                            <input type="hidden" name="recommendations[${index}][jumlah_per_kemasan]" value="${item.recommended_jumlah}">
                        </div>
                    `;
                });

                content += '</div>';
                content += '<div class="mt-4 flex justify-end gap-2">';
                content += '<button type="button" onclick="closeRecommendationsModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 text-sm font-medium rounded-lg transition-all">Batal</button>';
                content += '<button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-all">Buat Harga</button>';
                content += '</div>';
                content += '<input type="hidden" name="target_periode" value="' + targetPeriode + '">';
                content += '</form>';
            }

            document.getElementById('recommendationsContent').innerHTML = content;
            document.getElementById('recommendationsModal').classList.remove('hidden');

            // Handle form submission
            document.getElementById('bulkCreateForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const recommendations = [];

                // Collect checked recommendations
                for (let i = 0; i < data.length; i++) {
                    const checkbox = formData.get(`recommendations[${i}][id_obat]`);
                    if (checkbox) {
                        recommendations.push({
                            id_obat: parseInt(checkbox),
                            harga_per_satuan: parseFloat(formData.get(`recommendations[${i}][harga_per_satuan]`)),
                            harga_per_kemasan: parseFloat(formData.get(`recommendations[${i}][harga_per_kemasan]`)),
                            jumlah_per_kemasan: parseInt(formData.get(`recommendations[${i}][jumlah_per_kemasan]`))
                        });
                    }
                }

                if (recommendations.length === 0) {
                    alert('Pilih setidaknya satu rekomendasi harga.');
                    return;
                }

                // Submit to server
                fetch('/monitoring/harga/bulk-create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        target_periode: targetPeriode,
                        recommendations: recommendations
                    })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert(result.message);
                        closeRecommendationsModal();
                        location.reload();
                    } else {
                        alert('Terjadi kesalahan: ' + result.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat membuat harga.');
                });
            });
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengambil rekomendasi.');
        });
}

function closeRecommendationsModal() {
    document.getElementById('recommendationsModal').classList.add('hidden');
}

function viewHargaHistory(idObat) {
    fetch(`/monitoring/harga/history/${idObat}`)
        .then(response => response.json())
        .then(data => {
            let content = '<div class="overflow-x-auto">';
            content += '<table class="min-w-full divide-y divide-gray-200">';
            content += '<thead class="bg-gray-50"><tr>';
            content += '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>';
            content += '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Harga Satuan</th>';
            content += '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Harga Kemasan</th>';
            content += '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Updated</th>';
            content += '</tr></thead><tbody class="bg-white divide-y divide-gray-200">';

            if (data.length === 0) {
                content += '<tr><td colspan="4" class="px-4 py-4 text-center text-gray-500">Tidak ada histori harga</td></tr>';
            } else {
                data.forEach(item => {
                    content += `
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-900">${item.periode_format}</td>
                            <td class="px-4 py-2 text-sm text-gray-900">Rp ${parseFloat(item.harga_per_satuan).toLocaleString('id-ID')}</td>
                            <td class="px-4 py-2 text-sm text-gray-900">Rp ${parseFloat(item.harga_per_kemasan).toLocaleString('id-ID')}</td>
                            <td class="px-4 py-2 text-sm text-gray-600">${item.updated_at}</td>
                        </tr>
                    `;
                });
            }

            content += '</tbody></table></div>';

            document.getElementById('historyContent').innerHTML = content;
            document.getElementById('historyModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengambil histori harga.');
        });
}

function closeHistoryModal() {
    document.getElementById('historyModal').classList.add('hidden');
}

</script>
@endpush
@endsection
