@extends('layouts.app')

@section('page-title', 'Edit Rekam Medis Emergency')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <div class="bg-gradient-to-r from-red-600 to-pink-600 p-3 rounded-lg shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Rekam Medis Emergency</h1>
                <p class="text-gray-600 mt-1">Perbarui data rekam medis pasien emergency</p>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
        <form action="{{ route('rekam-medis-emergency.update', $rekamMedisEmergency->id_emergency) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- External Employee -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Karyawan External <span class="text-red-500">*</span>
                    </label>
                    <select name="id_external_employee" id="id_external_employee" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            onchange="updateEmployeeInfo()">
                        <option value="">-- Pilih Karyawan External --</option>
                        @foreach($externalEmployees as $employee)
                            <option value="{{ $employee->id }}"
                                    data-nik="{{ $employee->nik_employee }}"
                                    data-nama="{{ $employee->nama_employee }}"
                                    data-rm="{{ $employee->kode_rm }}"
                                    data-jk="{{ $employee->jenis_kelamin }}"
                                    {{ old('id_external_employee', $rekamMedisEmergency->id_external_employee) == $employee->id ? 'selected' : '' }}>
                                {{ $employee->nama_employee }} - {{ $employee->nik_employee }} - {{ $employee->kode_rm }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_external_employee')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Employee Info Display -->
                <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg" id="employeeInfo">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Informasi Karyawan:</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">NIK:</span>
                            <p class="font-medium" id="displayNik">{{ $rekamMedisEmergency->externalEmployee->nik_employee ?? '-' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Nama:</span>
                            <p class="font-medium" id="displayNama">{{ $rekamMedisEmergency->externalEmployee->nama_employee ?? '-' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">No RM:</span>
                            <p class="font-medium" id="displayRm">{{ $rekamMedisEmergency->externalEmployee->kode_rm ?? '-' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Jenis Kelamin:</span>
                            <p class="font-medium" id="displayJk">{{ $rekamMedisEmergency->externalEmployee->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Tanggal Periksa -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Periksa <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_periksa" value="{{ old('tanggal_periksa', $rekamMedisEmergency->tanggal_periksa->format('Y-m-d')) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    @error('tanggal_periksa')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Waktu Periksa -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Waktu Periksa
                    </label>
                    <input type="time" name="waktu_periksa" value="{{ old('waktu_periksa', $rekamMedisEmergency->waktu_periksa ? $rekamMedisEmergency->waktu_periksa->format('H:i:s') : '') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    @error('waktu_periksa')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Rekam Medis -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Status Rekam Medis <span class="text-red-500">*</span>
                    </label>
                    <select name="status" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="On Progress" {{ old('status', $rekamMedisEmergency->status) == 'On Progress' ? 'selected' : '' }}>On Progress</option>
                        <option value="Close" {{ old('status', $rekamMedisEmergency->status) == 'Close' ? 'selected' : '' }}>Close</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Keluhan -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Keluhan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="keluhan" rows="3" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                              placeholder="Deskripsikan keluhan pasien">{{ old('keluhan', $rekamMedisEmergency->keluhan) }}</textarea>
                    @error('keluhan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Diagnosa Emergency -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Diagnosa Emergency <span class="text-red-500">*</span>
                    </label>
                    <select name="id_diagnosa_emergency" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="">-- Pilih Diagnosa Emergency --</option>
                        @foreach($diagnosaEmergency as $diagnosa)
                            <option value="{{ $diagnosa->id_diagnosa_emergency }}"
                                    {{ old('id_diagnosa_emergency', $rekamMedisEmergency->keluhans->first()->id_diagnosa_emergency ?? null) == $diagnosa->id_diagnosa_emergency ? 'selected' : '' }}>
                                {{ $diagnosa->nama_diagnosa_emergency }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_diagnosa_emergency')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Terapi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Terapi <span class="text-red-500">*</span>
                    </label>
                    <select name="terapi" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="">-- Pilih Terapi --</option>
                        <option value="Obat" {{ old('terapi', $rekamMedisEmergency->keluhans->first()->terapi ?? null) == 'Obat' ? 'selected' : '' }}>Obat</option>
                        <option value="Lab" {{ old('terapi', $rekamMedisEmergency->keluhans->first()->terapi ?? null) == 'Lab' ? 'selected' : '' }}>Lab</option>
                        <option value="Istirahat" {{ old('terapi', $rekamMedisEmergency->keluhans->first()->terapi ?? null) == 'Istirahat' ? 'selected' : '' }}>Istirahat</option>
                        <option value="Emergency" {{ old('terapi', $rekamMedisEmergency->keluhans->first()->terapi ?? null) == 'Emergency' ? 'selected' : '' }}>Emergency</option>
                    </select>
                    @error('terapi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Catatan -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan
                    </label>
                    <textarea name="catatan" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                              placeholder="Masukkan catatan tambahan (opsional)">{{ old('catatan', $rekamMedisEmergency->catatan) }}</textarea>
                    @error('catatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>


            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 mt-6">
                <a href="{{ route('rekam-medis-emergency.show', $rekamMedisEmergency->id_emergency) }}"
                   class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update Data Emergency
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let searchTimeout;

// Function to update employee info when selection changes
function updateEmployeeInfo() {
    const selectElement = document.getElementById('id_external_employee');
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    
    if (selectElement.value) {
        document.getElementById('displayNik').textContent = selectedOption.getAttribute('data-nik');
        document.getElementById('displayNama').textContent = selectedOption.getAttribute('data-nama');
        document.getElementById('displayRm').textContent = selectedOption.getAttribute('data-rm');
        document.getElementById('displayJk').textContent = selectedOption.getAttribute('data-jk') === 'L' ? 'Laki-laki' : 'Perempuan';
    } else {
        document.getElementById('displayNik').textContent = '-';
        document.getElementById('displayNama').textContent = '-';
        document.getElementById('displayRm').textContent = '-';
        document.getElementById('displayJk').textContent = '-';
    }
}

    clearTimeout(searchTimeout);
    const searchValue = this.value.trim();

    if (searchValue.length < 2) {
        document.getElementById('karyawan_search_results').classList.add('hidden');
        return;
    }

    searchTimeout = setTimeout(function() {
        // Filter local data instead of AJAX for now
        const employees = @json($externalEmployees);
        const filteredEmployees = employees.filter(employee =>
            employee.nik_employee.toLowerCase().includes(searchValue.toLowerCase()) ||
            employee.nama_employee.toLowerCase().includes(searchValue.toLowerCase())
        );

        const resultsDiv = document.getElementById('karyawan_search_results');

        if (filteredEmployees.length === 0) {
            resultsDiv.innerHTML = '<div class="px-4 py-3 text-gray-500 text-sm">Tidak ada karyawan external ditemukan</div>';
        } else {
            resultsDiv.innerHTML = filteredEmployees.map(employee => `
                <div class="px-4 py-3 hover:bg-red-50 cursor-pointer border-b border-gray-100 transition-colors" onclick="selectKaryawan(${JSON.stringify(employee).replace(/"/g, '"')})">
                    <div class="font-medium text-gray-900">${employee.nik_employee} - ${employee.nama_employee}</div>
                    <div class="text-sm text-gray-600">Vendor: ${employee.vendor ? employee.vendor.nama_vendor : 'Tidak ada vendor'} | Kategori: ${employee.kategori ? employee.kategori.nama_kategori : 'Tidak ada kategori'}</div>
                </div>
            `).join('');
        }

        resultsDiv.classList.remove('hidden');
    }, 300);
});

// Select karyawan external from dropdown
function selectKaryawan(employee) {
    console.log('Selecting employee:', employee); // Debug log
    
    // Set karyawan values
    document.getElementById('id_external_employee').value = employee.id; // Use primary key 'id' instead of 'id_external_employee'
    document.getElementById('search_karyawan').value = `${employee.nik_employee}-${employee.nama_employee}`;

    // Update info karyawan
    document.getElementById('info_nik').textContent = employee.nik_employee;
    document.getElementById('info_nama').textContent = employee.nama_employee;
    document.getElementById('info_vendor').textContent = employee.vendor ? employee.vendor.nama_vendor : 'Tidak ada vendor';
    document.getElementById('info_jk').textContent = employee.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    
    // Update form fields
    document.getElementById('kode_rm').value = employee.kode_rm || '';
    document.getElementById('nama_pasien').value = employee.nama_employee;
    document.getElementById('hubungan').value = 'Emergency';
    document.getElementById('jenis_kelamin').value = employee.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';

    // Clear any validation errors
    clearFieldError(document.getElementById('search_karyawan'));

    // Hide results
    document.getElementById('karyawan_search_results').classList.add('hidden');
    
    console.log('Employee selected successfully'); // Debug log
}

// Hide search results when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('#search_karyawan') && !e.target.closest('#karyawan_search_results')) {
        document.getElementById('karyawan_search_results').classList.add('hidden');
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Pre-fill with existing employee data
    const idEmployee = document.getElementById('id_external_employee').value;
    if (idEmployee) {
        updateEmployeeInfo();
    }

    // Add change event listener to update employee info when selection changes
    document.getElementById('id_external_employee').addEventListener('change', function() {
        updateEmployeeInfo();
    });
});
</script>
@endpush
@endsection
