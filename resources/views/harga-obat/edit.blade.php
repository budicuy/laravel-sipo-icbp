@extends('layouts.app')

@section('page-title', 'Edit Harga Obat')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('harga-obat.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-3 rounded-lg shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                Edit Harga Obat
            </h1>
        </div>
        <p class="text-gray-600 mt-2 ml-1">Ubah data harga obat untuk periode tertentu</p>
    </div>

    <!-- Error Message -->
    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Data Duplikat',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Mengerti',
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    }
                });
            });
        </script>
    @endif

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Edit Data Harga Obat
            </h2>
            <p class="text-sm text-gray-600 mt-1">Ubah data harga obat dengan benar</p>
        </div>

        <form action="{{ route('harga-obat.update', $hargaObat->id_harga_obat) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Obat -->
                <div>
                    <label for="id_obat" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Obat <span class="text-red-500">*</span>
                    </label>
                    <select name="id_obat" id="id_obat" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white shadow-sm">
                        <option value="">Pilih Obat</option>
                        @foreach($obats as $obat)
                            <option value="{{ $obat->id_obat }}" {{ $hargaObat->id_obat == $obat->id_obat ? 'selected' : '' }}>{{ $obat->nama_obat }} - {{ $obat->satuanObat->nama_satuan ?? '' }}</option>
                        @endforeach
                    </select>
                    @error('id_obat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Periode -->
                <div>
                    <label for="periode" class="block text-sm font-medium text-gray-700 mb-2">
                        Periode <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="periode" id="periode" value="{{ old('periode', $hargaObat->periode) }}" required
                           pattern="^(0[1-9]|1[0-2])-(0[1-9]|[1-9][0-9])$"
                           title="Format: MM-YY (contoh: 10-25). Bulan: 01-12, Tahun: 01-99"
                           placeholder="MM-YY (contoh: 10-25)"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm shadow-sm">
                    <p class="mt-1 text-xs text-gray-500">Format periode: MM-YY (contoh: 10-25 untuk Oktober 2025)</p>
                    @error('periode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jumlah per Kemasan -->
                <div>
                    <label for="jumlah_per_kemasan" class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah per Kemasan <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="jumlah_per_kemasan" id="jumlah_per_kemasan" value="{{ old('jumlah_per_kemasan', $hargaObat->jumlah_per_kemasan) }}" required min="1"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm shadow-sm">
                    @error('jumlah_per_kemasan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga per Kemasan -->
                <div>
                    <label for="harga_per_kemasan" class="block text-sm font-medium text-gray-700 mb-2">
                        Harga per Kemasan <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-sm">Rp</span>
                        </div>
                        <input type="number" name="harga_per_kemasan" id="harga_per_kemasan" value="{{ old('harga_per_kemasan', $hargaObat->harga_per_kemasan) }}" required min="0" step="0.01"
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm shadow-sm">
                    </div>
                    @error('harga_per_kemasan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga per Satuan -->
                <div>
                    <label for="harga_per_satuan" class="block text-sm font-medium text-gray-700 mb-2">
                        Harga per Satuan <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-sm">Rp</span>
                        </div>
                        <input type="number" name="harga_per_satuan" id="harga_per_satuan" value="{{ old('harga_per_satuan', $hargaObat->harga_per_satuan) }}" required min="0" step="0.01"
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm shadow-sm">
                    </div>
                    @error('harga_per_satuan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Obat -->
                <div id="infoObat" class="hidden md:col-span-2 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h3 class="text-sm font-semibold text-blue-900 mb-2">Informasi Obat</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-blue-800">Jenis Obat:</span>
                            <span id="infoJenisObat" class="text-blue-700 ml-1">-</span>
                        </div>
                        <div>
                            <span class="font-medium text-blue-800">Satuan:</span>
                            <span id="infoSatuanObat" class="text-blue-700 ml-1">-</span>
                        </div>
                        <div>
                            <span class="font-medium text-blue-800">Keterangan:</span>
                            <span id="infoKeterangan" class="text-blue-700 ml-1">-</span>
                        </div>
                    </div>
                </div>

                <!-- Info Periode -->
                <div class="md:col-span-2 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h3 class="text-sm font-semibold text-yellow-900 mb-2">Informasi Periode</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-yellow-800">Periode Saat Ini:</span>
                            <span class="text-yellow-700 ml-1">{{ $hargaObat->periode }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-yellow-800">Format Periode:</span>
                            <span class="text-yellow-700 ml-1">MM-YY (contoh: 10-25)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('harga-obat.index') }}" class="px-5 py-2.5 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const obatSelect = document.getElementById('id_obat');
    const infoObat = document.getElementById('infoObat');
    const infoJenisObat = document.getElementById('infoJenisObat');
    const infoSatuanObat = document.getElementById('infoSatuanObat');
    const infoKeterangan = document.getElementById('infoKeterangan');

    // Data obat
    const obatData = {
        @foreach($obats as $obat)
        '{{ $obat->id_obat }}': {
            'jenis_obat': '{{ $obat->jenisObat->nama_jenis_obat ?? '-' }}',
            'satuan': '{{ $obat->satuanObat->nama_satuan ?? '-' }}',
            'keterangan': '{{ $obat->keterangan ?? '-' }}'
        },
        @endforeach
    };

    // Show info for current selected obat
    const currentObatId = '{{ $hargaObat->id_obat }}';
    if (currentObatId && obatData[currentObatId]) {
        const obat = obatData[currentObatId];

        // Tampilkan info obat
        infoObat.classList.remove('hidden');
        infoJenisObat.textContent = obat.jenis_obat;
        infoSatuanObat.textContent = obat.satuan;
        infoKeterangan.textContent = obat.keterangan;
    }

    // Event listener untuk perubahan pilihan obat
    obatSelect.addEventListener('change', function() {
        const selectedObatId = this.value;

        if (selectedObatId && obatData[selectedObatId]) {
            const obat = obatData[selectedObatId];

            // Tampilkan info obat
            infoObat.classList.remove('hidden');
            infoJenisObat.textContent = obat.jenis_obat;
            infoSatuanObat.textContent = obat.satuan;
            infoKeterangan.textContent = obat.keterangan;
        } else {
            // Sembunyikan info obat
            infoObat.classList.add('hidden');
        }
    });

    // Auto calculate harga per satuan when harga per kemasan or jumlah per kemasan changes
    const hargaPerKemasanInput = document.getElementById('harga_per_kemasan');
    const hargaPerSatuanInput = document.getElementById('harga_per_satuan');
    const jumlahPerKemasanInput = document.getElementById('jumlah_per_kemasan');

    function calculateHargaPerSatuan() {
        const hargaPerKemasan = parseFloat(hargaPerKemasanInput.value) || 0;
        const jumlahPerKemasan = parseInt(jumlahPerKemasanInput.value) || 1;

        if (hargaPerKemasan > 0 && jumlahPerKemasan > 0) {
            const hargaPerSatuan = hargaPerKemasan / jumlahPerKemasan;
            hargaPerSatuanInput.value = hargaPerSatuan.toFixed(2);
        }
    }

    function calculateHargaPerKemasan() {
        const hargaPerSatuan = parseFloat(hargaPerSatuanInput.value) || 0;
        const jumlahPerKemasan = parseInt(jumlahPerKemasanInput.value) || 1;

        if (hargaPerSatuan > 0 && jumlahPerKemasan > 0) {
            const hargaPerKemasan = hargaPerSatuan * jumlahPerKemasan;
            hargaPerKemasanInput.value = hargaPerKemasan.toFixed(2);
        }
    }

    hargaPerKemasanInput.addEventListener('input', calculateHargaPerSatuan);
    jumlahPerKemasanInput.addEventListener('input', calculateHargaPerSatuan);
    hargaPerSatuanInput.addEventListener('input', calculateHargaPerKemasan);

    // Validasi periode
    const periodeInput = document.getElementById('periode');

    periodeInput.addEventListener('input', function() {
        validatePeriode(this.value);
    });

    periodeInput.addEventListener('blur', function() {
        validatePeriode(this.value);
    });

    function validatePeriode(value) {
        const periodeRegex = /^(0[1-9]|1[0-2])-(0[1-9]|[1-9][0-9])$/;

        if (value === '') {
            periodeInput.setCustomValidity('');
            return;
        }

        if (!periodeRegex.test(value)) {
            periodeInput.setCustomValidity('Format periode harus MM-YY (contoh: 10-25). Bulan: 01-12, Tahun: 01-99');
            periodeInput.classList.add('border-red-500');
            periodeInput.classList.remove('border-green-500');
        } else {
            periodeInput.setCustomValidity('');
            periodeInput.classList.remove('border-red-500');
            periodeInput.classList.add('border-green-500');
        }
    }

    // Validasi form sebelum submit
    document.querySelector('form').addEventListener('submit', function(e) {
        const periodeValue = periodeInput.value;

        if (periodeValue === '') {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: 'Periode wajib diisi',
                confirmButtonColor: '#dc2626'
            });
            return;
        }

        const periodeRegex = /^(0[1-9]|1[0-2])-(0[1-9]|[1-9][0-9])$/;

        if (!periodeRegex.test(periodeValue)) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: 'Format periode harus MM-YY (contoh: 10-25). Bulan: 01-12, Tahun: 01-99',
                confirmButtonColor: '#dc2626'
            });
            return;
        }
    });
});
</script>
@endsection
