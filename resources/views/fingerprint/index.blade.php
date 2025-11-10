@extends('layouts.app')

@section('title', 'Manajemen Fingerprint')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen" x-data="fingerprintSystem()">
    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-3 rounded-lg shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                </svg>
            </div>
            Sistem Sidik Jari SecuGen
        </h1>
        <p class="text-gray-600 mt-2 ml-1">Manajemen fingerprint data keluarga karyawan</p>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button @click="activeTab = 'enroll'"
                    :class="activeTab === 'enroll' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-6 text-sm font-medium border-b-2 transition-colors duration-200 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Pendaftaran Fingerprint
                </button>
                <button @click="activeTab = 'verify'"
                    :class="activeTab === 'verify' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-6 text-sm font-medium border-b-2 transition-colors duration-200 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Verifikasi Fingerprint
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Enroll Tab -->
            <div x-show="activeTab === 'enroll'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Enrollment Form -->
                    <div class="border rounded-lg p-6 bg-gray-50">
                        <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            Daftar Fingerprint Baru
                        </h2>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Anggota Keluarga</label>
                            <select x-model="selectedFamilyMember" @change="onFamilyMemberChange()"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Pilih Anggota Keluarga --</option>
                                <template x-for="member in familyMembers" :key="member.id_keluarga">
                                    <option :value="member.id_keluarga" x-text="`${member.nama_keluarga} - ${member.karyawan?.nama_karyawan || ''}`"></option>
                                </template>
                            </select>
                        </div>

                        <div x-show="selectedFamilyMember" class="mb-4 p-3 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <strong>Terpilih:</strong> <span x-text="getSelectedMemberName()"></span>
                            </p>
                        </div>

                        <button @click="enrollFingerprint()"
                            :disabled="!selectedFamilyMember || isCapturing"
                            class="w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 disabled:bg-gray-400 font-semibold transition-colors duration-200 flex items-center justify-center gap-2">
                            <svg x-show="!isCapturing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <svg x-show="isCapturing" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span x-text="isCapturing ? 'Menangkap Sidik Jari...' : 'Capture & Daftar'"></span>
                        </button>
                    </div>

                    <!-- Fingerprint Preview -->
                    <div class="border rounded-lg p-6 bg-gray-50">
                        <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Preview Sidik Jari
                        </h2>

                        <div x-show="!lastCaptured" class="text-center py-8 text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                            </svg>
                            <p>Belum ada sidik jari yang ditangkap</p>
                        </div>

                        <div x-show="lastCaptured" class="space-y-4">
                            <img :src="`data:image/bmp;base64,${lastCaptured.image}`"
                                alt="Fingerprint" class="w-full border rounded-lg">

                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div class="bg-white p-3 rounded border">
                                    <span class="font-semibold">Kualitas:</span>
                                    <span x-text="lastCaptured.quality" class="ml-2"></span>
                                </div>
                                <div class="bg-white p-3 rounded border">
                                    <span class="font-semibold">NFIQ:</span>
                                    <span x-text="lastCaptured.nfiq" class="ml-2"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Verify Tab -->
            <div x-show="activeTab === 'verify'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="max-w-2xl mx-auto">
                    <div class="border rounded-lg p-6 bg-gray-50">
                        <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Verifikasi Sidik Jari
                        </h2>

                        <p class="text-gray-600 mb-6">Letakkan jari di scanner untuk memverifikasi identitas</p>

                        <button @click="verifyFingerprint()"
                            :disabled="isCapturing || fingerprintTemplates.length === 0"
                            class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 disabled:bg-gray-400 font-semibold transition-colors duration-200 flex items-center justify-center gap-2">
                            <svg x-show="!isCapturing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <svg x-show="isCapturing" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span x-text="isCapturing ? 'Memverifikasi...' : 'Verifikasi Sekarang'"></span>
                        </button>

                        <div x-show="fingerprintTemplates.length === 0" class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-sm text-yellow-800">
                                <strong>Perhatian:</strong> Belum ada data fingerprint yang terdaftar. Silakan daftarkan fingerprint terlebih dahulu.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message Display -->
        <div x-show="message" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            class="m-6 p-4 rounded-lg"
            :class="messageType === 'success' ? 'bg-green-100 text-green-800' :
                    messageType === 'error' ? 'bg-red-100 text-red-800' :
                    'bg-blue-100 text-blue-800'">
            <div class="flex items-center gap-2">
                <svg x-show="messageType === 'success'" class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <svg x-show="messageType === 'error'" class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <svg x-show="messageType === 'info'" class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span x-text="message"></span>
            </div>
        </div>

        <!-- Registered Fingerprints List -->
        <div class="border-t border-gray-200 p-6">
            <h2 class="text-xl font-semibold mb-4">Data Fingerprint Terdaftar ({{ count($keluargas) }})</h2>

            <div x-show="{{ count($keluargas) === 0 }}" class="text-center py-8 text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                </svg>
                <p>Belum ada data fingerprint yang terdaftar</p>
            </div>

            <div x-show="{{ count($keluargas) > 0 }}" class="space-y-3">
                <template x-for="keluarga in keluargas" :key="keluarga.id_keluarga">
                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div>
                            <p class="font-semibold text-gray-800" x-text="keluarga.nama_keluarga"></p>
                            <p class="text-sm text-gray-500">
                                Karyawan: <span x-text="keluarga.karyawan?.nama_karyawan || '-'"></span>
                            </p>
                            <p class="text-sm text-gray-500">
                                Terdaftar: <span x-text="new Date(keluarga.fingerprint_enrolled_at).toLocaleString('id-ID')"></span>
                            </p>
                        </div>
                        <button @click="deleteFingerprint(keluarga.id_keluarga)"
                            class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors duration-200">
                            Hapus
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Info Card -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-6">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <strong class="text-yellow-800">Catatan Penting:</strong>
                <ul class="text-sm text-yellow-700 mt-2 space-y-1">
                    <li>• Pastikan SGIBIOSRV berjalan di localhost:8443</li>
                    <li>• Fingerprint reader harus terhubung dengan benar</li>
                    <li>• Pastikan koneksi SSL ke SecuGen API berjalan dengan baik</li>
                    <li>• Kualitas fingerprint minimal 50 untuk pendaftaran</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function fingerprintSystem() {
    return {
        activeTab: 'enroll',
        familyMembers: [],
        fingerprintTemplates: [],
        selectedFamilyMember: '',
        isCapturing: false,
        message: '',
        messageType: 'info',
        lastCaptured: null,
        keluargas: @json($keluargas),

        init() {
            this.loadFamilyMembers();
            this.loadFingerprintTemplates();
        },

        async loadFamilyMembers() {
            try {
                const response = await fetch('/fingerprint/family-members');
                this.familyMembers = await response.json();
            } catch (error) {
                console.error('Error loading family members:', error);
                this.showMessage('Gagal memuat data anggota keluarga', 'error');
            }
        },

        async loadFingerprintTemplates() {
            try {
                const response = await fetch('/fingerprint/templates');
                this.fingerprintTemplates = await response.json();
            } catch (error) {
                console.error('Error loading fingerprint templates:', error);
            }
        },

        onFamilyMemberChange() {
            // Reset last captured when family member changes
            this.lastCaptured = null;
        },

        getSelectedMemberName() {
            const member = this.familyMembers.find(m => m.id_keluarga == this.selectedFamilyMember);
            return member ? `${member.nama_keluarga} - ${member.karyawan?.nama_karyawan || ''}` : '';
        },

        async captureFingerprint() {
            this.isCapturing = true;
            this.showMessage('Menangkap sidik jari...', 'info');

            try {
                const params = new URLSearchParams({
                    Timeout: '10000',
                    Quality: '50',
                    licstr: '',
                    templateFormat: 'ISO',
                    imageWSQRate: '0.75'
                });

                const response = await fetch('https://localhost:8443/SGIFPCapture', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: params.toString()
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const data = await response.json();

                if (data.ErrorCode === 0) {
                    this.lastCaptured = {
                        template: data.TemplateBase64,
                        image: data.BMPBase64,
                        quality: data.ImageQuality,
                        nfiq: data.NFIQ
                    };
                    this.showMessage(`Sidik jari berhasil ditangkap! Kualitas: ${data.ImageQuality}, NFIQ: ${data.NFIQ}`, 'success');
                    return data.TemplateBase64;
                } else {
                    this.showMessage(`Error: ${data.ErrorCode} - ${this.getErrorDescription(data.ErrorCode)}`, 'error');
                    return null;
                }
            } catch (error) {
                this.showMessage(`Error koneksi: ${error.message}. Pastikan SGIBIOSRV berjalan di port 8443`, 'error');
                return null;
            } finally {
                this.isCapturing = false;
            }
        },

        getErrorDescription(code) {
            const errors = {
                51: 'System file load failure',
                52: 'Sensor chip initialization failed',
                53: 'Device not found',
                54: 'Fingerprint image capture timeout',
                55: 'No device available',
                56: 'Driver load failed',
                57: 'Wrong Image',
                58: 'Lack of bandwidth',
                59: 'Device Busy',
                60: 'Cannot get serial number',
                61: 'Unsupported device',
                63: 'SgiBioSrv tidak berjalan'
            };
            return errors[code] || 'Unknown error';
        },

        async enrollFingerprint() {
            if (!this.selectedFamilyMember) {
                this.showMessage('Pilih anggota keluarga terlebih dahulu!', 'error');
                return;
            }

            const template = await this.captureFingerprint();
            if (template) {
                try {
                    const response = await fetch('/fingerprint/save', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            id_keluarga: this.selectedFamilyMember,
                            fingerprint_template: template
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.showMessage(result.message, 'success');
                        this.selectedFamilyMember = '';
                        this.lastCaptured = null;
                        this.loadFingerprintTemplates();
                        // Reload page to show updated data
                        setTimeout(() => window.location.reload(), 2000);
                    } else {
                        this.showMessage('Gagal menyimpan fingerprint', 'error');
                    }
                } catch (error) {
                    console.error('Error saving fingerprint:', error);
                    this.showMessage('Gagal menyimpan fingerprint', 'error');
                }
            }
        },

        async verifyFingerprint() {
            if (this.fingerprintTemplates.length === 0) {
                this.showMessage('Belum ada fingerprint terdaftar!', 'error');
                return;
            }

            const template = await this.captureFingerprint();
            if (!template) return;

            this.showMessage('Memverifikasi...', 'info');
            let bestMatch = null;
            let bestScore = 0;

            for (const user of this.fingerprintTemplates) {
                try {
                    const params = new URLSearchParams({
                        Template1: template,
                        Template2: user.fingerprint_template,
                        licstr: '',
                        templateFormat: 'ISO'
                    });

                    const response = await fetch('https://localhost:8443/SGIMatchScore', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: params.toString()
                    });

                    const data = await response.json();

                    if (data.ErrorCode === 0 && data.MatchingScore > bestScore) {
                        bestScore = data.MatchingScore;
                        bestMatch = user;
                    }
                } catch (error) {
                    console.error('Error matching:', error);
                    this.showMessage(`Error matching: ${error.message}`, 'error');
                    return;
                }
            }

            if (bestScore > 100 && bestMatch) {
                this.showMessage(`✓ Verifikasi Berhasil! ${bestMatch.nama_keluarga} - ${bestMatch.karyawan?.nama_karyawan || ''} (Score: ${bestScore}/199)`, 'success');
            } else {
                this.showMessage(`✗ Sidik jari tidak cocok. Score tertinggi: ${bestScore}/199`, 'error');
            }
        },

        async deleteFingerprint(id_keluarga) {
            if (!confirm('Apakah Anda yakin ingin menghapus fingerprint ini?')) {
                return;
            }

            try {
                const response = await fetch('/fingerprint/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ id_keluarga })
                });

                const result = await response.json();

                if (result.success) {
                    this.showMessage(result.message, 'success');
                    // Reload page to show updated data
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    this.showMessage('Gagal menghapus fingerprint', 'error');
                }
            } catch (error) {
                console.error('Error deleting fingerprint:', error);
                this.showMessage('Gagal menghapus fingerprint', 'error');
            }
        },

        showMessage(msg, type = 'info') {
            this.message = msg;
            this.messageType = type;

            // Auto hide message after 5 seconds
            setTimeout(() => {
                this.message = '';
            }, 5000);
        }
    }
}
</script>
@endpush
@endsection
