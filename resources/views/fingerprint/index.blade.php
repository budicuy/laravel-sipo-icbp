@extends('layouts.app')

@section('title', 'Manajemen Fingerprint')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
/* Custom animations for fingerprint */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.fingerprint-pulse {
    animation: pulse 2s infinite;
}

/* Custom gradient backgrounds */
.bg-gradient-purple {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-green {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.bg-gradient-red {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
}

/* Custom transitions */
.transition-all-300 {
    transition: all 0.3s ease;
}

/* Hover effects */
.hover-lift:hover {
    transform: translateY(-5px);
}

.hover-lift-sm:hover {
    transform: translateY(-2px);
}

/* Custom shadows */
.shadow-glow {
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.shadow-hover:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="bg-white rounded-2xl shadow-lg hover-lift transition-all-300">
                <div class="bg-gradient-purple text-white p-6 rounded-t-2xl">
                    <h3 class="text-xl font-bold flex items-center">
                        <i class="fas fa-fingerprint mr-3"></i> Manajemen Fingerprint Keluarga
                    </h3>
                </div>
                <div class="p-6">
                    <!-- Tabs -->
                    <ul class="flex space-x-2 mb-6" id="fingerprintTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="px-6 py-3 rounded-t-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all-300 hover-lift-sm font-semibold active"
                                    id="enroll-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#enroll"
                                    type="button"
                                    role="tab"
                                    aria-controls="enroll"
                                    aria-selected="true">
                                <i class="fas fa-user-plus mr-2"></i> Pendaftaran Fingerprint
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="px-6 py-3 rounded-t-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all-300 hover-lift-sm font-semibold"
                                    id="verify-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#verify"
                                    type="button"
                                    role="tab"
                                    aria-controls="verify"
                                    aria-selected="false">
                                <i class="fas fa-check-circle mr-2"></i> Verifikasi Fingerprint
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div id="fingerprintTabsContent">
                        <!-- Enroll Tab -->
                        <div class="tab-pane fade show active" id="enroll" role="tabpanel" aria-labelledby="enroll-tab">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                                <div class="bg-white rounded-2xl shadow-lg hover-lift transition-all-300">
                                    <div class="bg-gradient-purple text-white p-4 rounded-t-2xl">
                                        <h5 class="text-lg font-bold flex items-center">
                                            <i class="fas fa-user-plus mr-2"></i> Daftar Fingerprint Baru
                                        </h5>
                                    </div>
                                    <div class="p-6">
                                        <form id="enrollForm">
                                            <div class="mb-4">
                                                <label for="id_keluarga" class="block text-sm font-medium text-gray-700 mb-2">Pilih Anggota Keluarga</label>
                                                <select class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all-300"
                                                        id="id_keluarga"
                                                        name="id_keluarga"
                                                        required>
                                                    <option value="">-- Pilih Anggota Keluarga --</option>
                                                    @foreach($allKeluarga as $keluarga)
                                                        <option value="{{ $keluarga->id_keluarga }}"
                                                                @if($keluarga->fingerprint_template) disabled @endif>
                                                            {{ $keluarga->nama_keluarga }}
                                                            ({{ $keluarga->hubungan->hubungan ?? 'Tidak diketahui' }})
                                                            @if($keluarga->fingerprint_template) - Sudah terdaftar @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-4 flex space-x-3">
                                                <button type="button"
                                                        id="captureBtn"
                                                        class="bg-gradient-purple text-white px-6 py-3 rounded-xl font-semibold hover-lift-sm transition-all-300 shadow-hover">
                                                    <i class="fas fa-fingerprint mr-2"></i> Capture Sidik Jari
                                                </button>
                                                <button type="submit"
                                                        id="enrollBtn"
                                                        class="bg-gradient-green text-white px-6 py-3 rounded-xl font-semibold hover-lift-sm transition-all-300 shadow-hover"
                                                        disabled>
                                                    <i class="fas fa-save mr-2"></i> Simpan Fingerprint
                                                </button>
                                            </div>
                                        </form>

                                        <div id="captureStatus" class="hidden bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl mb-4">
                                            <i class="fas fa-spinner fa-spin mr-2"></i> Menangkap sidik jari...
                                        </div>

                                        <div id="fingerprintPreview" class="hidden mt-4">
                                            <h6 class="text-sm font-medium text-gray-700 mb-2">Preview Sidik Jari:</h6>
                                            <img id="fingerprintImage" class="rounded-xl border-2 border-gray-200 max-w-xs">
                                            <div id="fingerprintInfo" class="mt-2 text-sm text-gray-600"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-2xl shadow-lg hover-lift transition-all-300">
                                    <div class="bg-gradient-purple text-white p-4 rounded-t-2xl">
                                        <h5 class="text-lg font-bold flex items-center">
                                            <i class="fas fa-list mr-2"></i> Daftar Fingerprint Terdaftar ({{ $keluargaList->count() }})
                                        </h5>
                                    </div>
                                    <div class="p-6">
                                        @if($keluargaList->isEmpty())
                                            <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-xl">
                                                <i class="fas fa-exclamation-triangle mr-2"></i> Belum ada fingerprint yang terdaftar
                                            </div>
                                        @else
                                            <div class="overflow-x-auto">
                                                <table class="w-full">
                                                    <thead>
                                                        <tr class="bg-gradient-purple text-white">
                                                            <th class="px-4 py-3 text-left text-sm font-medium">Nama</th>
                                                            <th class="px-4 py-3 text-left text-sm font-medium">Hubungan</th>
                                                            <th class="px-4 py-3 text-left text-sm font-medium">Tanggal Daftar</th>
                                                            <th class="px-4 py-3 text-left text-sm font-medium">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-200">
                                                        @foreach($keluargaList as $keluarga)
                                                            <tr class="hover:bg-gray-50 transition-colors">
                                                                <td class="px-4 py-3 text-sm">{{ $keluarga->nama_keluarga }}</td>
                                                                <td class="px-4 py-3 text-sm">{{ $keluarga->hubungan->hubungan ?? 'Tidak diketahui' }}</td>
                                                                <td class="px-4 py-3 text-sm">{{ $keluarga->fingerprint_enrolled_at ? $keluarga->fingerprint_enrolled_at->format('d/m/Y H:i') : '-' }}</td>
                                                                <td class="px-4 py-3 text-sm">
                                                                    <button type="button"
                                                                            class="bg-gradient-red text-white px-3 py-1 rounded-lg text-xs font-medium hover-lift-sm transition-all-300 shadow-hover btn-remove-fingerprint"
                                                                            data-id="{{ $keluarga->id_keluarga }}"
                                                                            data-name="{{ $keluarga->nama_keluarga }}">
                                                                        <i class="fas fa-trash mr-1"></i> Hapus
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Verify Tab -->
                        <div class="tab-pane fade" id="verify" role="tabpanel" aria-labelledby="verify-tab">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                                <div class="bg-white rounded-2xl shadow-lg hover-lift transition-all-300">
                                    <div class="bg-gradient-green text-white p-4 rounded-t-2xl">
                                        <h5 class="text-lg font-bold flex items-center">
                                            <i class="fas fa-check-circle mr-2"></i> Verifikasi Sidik Jari
                                        </h5>
                                    </div>
                                    <div class="p-6">
                                        <p class="text-gray-600 mb-6">Letakkan jari di scanner untuk memverifikasi identitas</p>

                                        <button type="button"
                                                id="verifyBtn"
                                                class="bg-gradient-green text-white px-8 py-4 rounded-xl font-semibold text-lg hover-lift-sm transition-all-300 shadow-hover">
                                            <i class="fas fa-fingerprint mr-3"></i> Verifikasi Sekarang
                                        </button>

                                        <div id="verifyStatus" class="hidden bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl mt-4">
                                            <i class="fas fa-spinner fa-spin mr-2"></i> Memverifikasi...
                                        </div>

                                        <div id="verifyResult" class="mt-4"></div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-2xl shadow-lg hover-lift transition-all-300">
                                    <div class="bg-gradient-green text-white p-4 rounded-t-2xl">
                                        <h5 class="text-lg font-bold flex items-center">
                                            <i class="fas fa-info-circle mr-2"></i> Hasil Verifikasi
                                        </h5>
                                    </div>
                                    <div class="p-6">
                                        <div id="verificationResult" class="text-center">
                                            <i class="fas fa-fingerprint text-6xl text-gray-400 mb-4 fingerprint-pulse"></i>
                                            <p class="text-gray-600">Belum ada verifikasi dilakukan</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for fingerprint template -->
<input type="hidden" id="fingerprintTemplate" name="fingerprintTemplate">

<!-- Confirmation Modal -->
<div class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" id="confirmModal">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4">
        <div class="bg-gradient-purple text-white p-6 rounded-t-2xl flex justify-between items-center">
            <h5 class="text-lg font-bold">Konfirmasi Hapus</h5>
            <button type="button" class="text-white hover:text-gray-200 transition-colors" onclick="document.getElementById('confirmModal').classList.add('hidden')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6">
            <p class="text-gray-700">Apakah Anda yakin ingin menghapus fingerprint untuk <strong id="confirmName"></strong>?</p>
        </div>
        <div class="bg-gray-50 px-6 py-4 rounded-b-2xl flex justify-end space-x-3">
            <button type="button"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors"
                    onclick="document.getElementById('confirmModal').classList.add('hidden')">
                Batal
            </button>
            <button type="button"
                    id="confirmDelete"
                    class="bg-gradient-red text-white px-4 py-2 rounded-xl hover-lift-sm transition-all-300 shadow-hover">
                Hapus
            </button>
        </div>
    </div>
</div>

<script>
let capturedTemplate = null;
let currentDeleteId = null;

// Tab switching functionality
document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
    tab.addEventListener('click', function() {
        // Remove active class from all tabs and panes
        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(t => {
            t.classList.remove('active', 'bg-gradient-purple', 'text-white');
            t.classList.add('bg-gray-100', 'text-gray-600');
        });
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('show', 'active');
        });

        // Add active class to clicked tab
        this.classList.add('active', 'bg-gradient-purple', 'text-white');
        this.classList.remove('bg-gray-100', 'text-gray-600');

        // Show corresponding pane
        const target = this.getAttribute('data-bs-target');
        document.querySelector(target).classList.add('show', 'active');
    });
});

// Capture fingerprint
document.getElementById('captureBtn').addEventListener('click', async function() {
    const btn = this;
    const statusDiv = document.getElementById('captureStatus');
    const previewDiv = document.getElementById('fingerprintPreview');
    const enrollBtn = document.getElementById('enrollBtn');

    btn.disabled = true;
    statusDiv.classList.remove('hidden');
    previewDiv.classList.add('hidden');
    enrollBtn.disabled = true;

    try {
        const response = await fetch('/fingerprint/capture', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();

        if (data.success) {
            capturedTemplate = data.template;
            document.getElementById('fingerprintTemplate').value = data.template;
            document.getElementById('fingerprintImage').src = 'data:image/bmp;base64,' + data.image;
            document.getElementById('fingerprintInfo').innerHTML = `
                <span class="text-xs">
                    Kualitas: ${data.quality}, NFIQ: ${data.nfiq}
                </span>
            `;
            previewDiv.classList.remove('hidden');
            enrollBtn.disabled = false;

            showAlert('success', data.message);
        } else {
            showAlert('danger', data.message);
        }
    } catch (error) {
        showAlert('danger', 'Error: ' + error.message);
    } finally {
        btn.disabled = false;
        statusDiv.classList.add('hidden');
    }
});

// Enroll fingerprint
document.getElementById('enrollForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const id_keluarga = document.getElementById('id_keluarga').value;

    if (!capturedTemplate) {
        showAlert('warning', 'Silakan capture fingerprint terlebih dahulu!');
        return;
    }

    try {
        const response = await fetch('/fingerprint/enroll', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                id_keluarga: id_keluarga,
                fingerprint_template: capturedTemplate
            })
        });

        const data = await response.json();

        if (data.success) {
            showAlert('success', data.message);
            // Reset form
            this.reset();
            document.getElementById('fingerprintPreview').classList.add('hidden');
            document.getElementById('enrollBtn').disabled = true;
            capturedTemplate = null;

            // Reload page after 2 seconds
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showAlert('danger', data.message);
        }
    } catch (error) {
        showAlert('danger', 'Error: ' + error.message);
    }
});

// Verify fingerprint
document.getElementById('verifyBtn').addEventListener('click', async function() {
    const btn = this;
    const statusDiv = document.getElementById('verifyStatus');
    const resultDiv = document.getElementById('verifyResult');
    const verificationResult = document.getElementById('verificationResult');

    btn.disabled = true;
    statusDiv.classList.remove('hidden');
    resultDiv.innerHTML = '';

    try {
        // First capture fingerprint
        const captureResponse = await fetch('/fingerprint/capture', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const captureData = await captureResponse.json();

        if (!captureData.success) {
            showAlert('danger', captureData.message);
            return;
        }

        // Then verify
        const verifyResponse = await fetch('/fingerprint/verify', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                fingerprint_template: captureData.template
            })
        });

        const verifyData = await verifyResponse.json();

        if (verifyData.success) {
            resultDiv.innerHTML = `
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                    <h6 class="font-medium"><i class="fas fa-check-circle mr-2"></i> ${verifyData.message}</h6>
                </div>
            `;

            verificationResult.innerHTML = `
                <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-8 rounded-xl">
                    <i class="fas fa-user-check text-5xl mb-4"></i>
                    <h5 class="text-lg font-bold mb-2">${verifyData.keluarga.nama_keluarga}</h5>
                    <p class="text-sm"><strong>Hubungan:</strong> ${verifyData.keluarga.hubungan?.hubungan || 'Tidak diketahui'}</p>
                    <p class="text-sm"><strong>Karyawan:</strong> ${verifyData.keluarga.karyawan?.nama_karyawan || 'Tidak diketahui'}</p>
                    <p class="text-sm"><strong>Score:</strong> ${verifyData.score}/199</p>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                    <h6 class="font-medium"><i class="fas fa-times-circle mr-2"></i> ${verifyData.message}</h6>
                </div>
            `;

            verificationResult.innerHTML = `
                <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-8 rounded-xl">
                    <i class="fas fa-user-times text-5xl mb-4"></i>
                    <p class="text-sm">Sidik jari tidak dikenali</p>
                    <p class="text-sm"><strong>Score tertinggi:</strong> ${verifyData.score}/199</p>
                </div>
            `;
        }
    } catch (error) {
        showAlert('danger', 'Error: ' + error.message);
        resultDiv.innerHTML = `
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                Error: ${error.message}
            </div>
        `;
    } finally {
        btn.disabled = false;
        statusDiv.classList.add('hidden');
    }
});

// Remove fingerprint
document.querySelectorAll('.btn-remove-fingerprint').forEach(btn => {
    btn.addEventListener('click', function() {
        currentDeleteId = this.dataset.id;
        document.getElementById('confirmName').textContent = this.dataset.name;
        document.getElementById('confirmModal').classList.remove('hidden');
        document.getElementById('confirmModal').classList.add('flex');
    });
});

document.getElementById('confirmDelete').addEventListener('click', async function() {
    if (!currentDeleteId) return;

    try {
        const response = await fetch(`/fingerprint/remove/${currentDeleteId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();

        if (data.success) {
            showAlert('success', data.message);
            document.getElementById('confirmModal').classList.add('hidden');
            document.getElementById('confirmModal').classList.remove('flex');

            // Reload page after 2 seconds
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showAlert('danger', data.message);
        }
    } catch (error) {
        showAlert('danger', 'Error: ' + error.message);
    }
});

// Helper function to show alerts
function showAlert(type, message) {
    const colors = {
        success: 'bg-green-50 border-green-200 text-green-700',
        danger: 'bg-red-50 border-red-200 text-red-700',
        warning: 'bg-yellow-50 border-yellow-200 text-yellow-700'
    };

    const alertDiv = document.createElement('div');
    alertDiv.className = `${colors[type]} border px-4 py-3 rounded-xl mb-4 flex justify-between items-center`;
    alertDiv.innerHTML = `
        <span>${message}</span>
        <button type="button" class="ml-4 text-current hover:opacity-75" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;

    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);

    // Auto dismiss after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Initialize tooltips and other Bootstrap components
document.addEventListener('DOMContentLoaded', function() {
    // Add loading animation to buttons
    document.querySelectorAll('button').forEach(button => {
        button.addEventListener('click', function() {
            if (!this.disabled && this.id !== 'confirmDelete') {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>' + originalText;
                this.disabled = true;

                // Re-enable after 3 seconds if not manually handled
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 3000);
            }
        });
    });

    // Add smooth scroll to top when tabs change
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('click', function (e) {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
});

// Enhanced error handling for fingerprint operations
window.addEventListener('unhandledrejection', function(event) {
    console.error('Unhandled promise rejection:', event.reason);
    showAlert('danger', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.');
});

// Add keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+C for capture
    if (e.ctrlKey && e.key === 'c' && document.activeElement.tagName !== 'INPUT') {
        e.preventDefault();
        document.getElementById('captureBtn')?.click();
    }

    // Ctrl+V for verify
    if (e.ctrlKey && e.key === 'v' && document.activeElement.tagName !== 'INPUT') {
        e.preventDefault();
        document.getElementById('verifyBtn')?.click();
    }

    // Escape to close modals
    if (e.key === 'Escape') {
        const modal = document.getElementById('confirmModal');
        if (modal && !modal.classList.contains('hidden')) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
});
</script>
@endsection
