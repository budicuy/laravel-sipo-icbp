/**
 * Token Emergency Modal System
 * Integrasi SweetAlert2 dengan Alpine.js untuk validasi token emergency
 */
document.addEventListener('DOMContentLoaded', function() {
    // Fungsi untuk membuka modal token emergency
    window.showTokenModal = function() {
        Swal.fire({
            title: '',
            html: `
                <div class="text-center pb-4">
                    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-4 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Validasi Token Emergency</h3>
                    <p class="text-gray-600">Masukkan token yang valid untuk mengakses rekam medis emergency</p>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3 text-left">Token Emergency <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <input type="text"
                               id="swal-token-input"
                               class="w-full pl-12 pr-4 py-4 text-center text-2xl font-mono border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                               placeholder="123456"
                               maxlength="6"
                               pattern="[0-9]{4,6}"
                               autocomplete="off">
                    </div>
                    <p class="mt-2 text-sm text-gray-500 text-center">Masukkan token berupa angka 4-6 digit</p>
                </div>

                <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-xl">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-semibold text-amber-800">Perhatian:</h3>
                            <p class="text-sm text-amber-700 mt-1">Token hanya dapat digunakan sekali dan akan hangus setelah digunakan</p>
                        </div>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Validasi Token',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#7c3aed',
            cancelButtonColor: '#6b7280',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const token = document.getElementById('swal-token-input').value;

                if (!token) {
                    Swal.showValidationMessage('Token harus diisi');
                    return false;
                }

                if (!/^[0-9]{4,6}$/.test(token)) {
                    Swal.showValidationMessage('Token harus berupa angka 4-6 digit');
                    return false;
                }

                return token;
            },
            didOpen: () => {
                // Auto-focus dan select all text
                const tokenInput = document.getElementById('swal-token-input');
                tokenInput.focus();
                tokenInput.select();

                // Auto-format input
                tokenInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            },
            customClass: {
                popup: 'rounded-2xl shadow-2xl max-w-md w-full p-6',
                title: 'hidden',
                htmlContainer: 'p-0',
                actions: 'gap-3 mt-6',
                confirmButton: 'px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all',
                cancelButton: 'px-6 py-3 bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-semibold rounded-xl transition-all hover:shadow-md',
                validationMessage: 'text-sm text-red-600 mt-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                validateToken(result.value);
            }
        });
    };

    // Fungsi untuk validasi token via AJAX
    window.validateToken = function(token) {
        Swal.fire({
            title: '',
            html: `
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full mb-4">
                        <svg class="animate-spin h-8 w-8 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Memvalidasi Token...</h3>
                    <p class="text-gray-600">Mohon tunggu, sistem sedang memvalidasi token Anda</p>
                </div>
            `,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: {
                popup: 'rounded-2xl shadow-2xl max-w-md w-full p-6',
                title: 'hidden',
                htmlContainer: 'p-0'
            }
        });

        // Kirim request ke server untuk validasi token
        fetch('/token-emergency/validate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                token: token
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Token Berhasil Divalidasi!',
                    text: 'Halaman akan direfresh untuk memperbarui menu emergency.',
                    showConfirmButton: true,
                    confirmButtonText: 'Lanjutkan',
                    confirmButtonColor: '#10b981',
                    timer: 3500,
                    timerProgressBar: true
                }).then(() => {
                    // Redirect to create emergency medical record page
                    window.location.href = data.redirect_url || '/rekam-medis-emergency/create';
                });
            } else {
                // Tampilkan pesan error yang berbeda berdasarkan status code
                let iconType = 'error';
                let titleText = 'Token Tidak Valid!';
                let additionalInfo = '';

                if (data.message && data.message.includes('bukan milik Anda')) {
                    iconType = 'warning';
                    titleText = 'Akses Ditolak!';
                    additionalInfo = `
                        <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                            <p class="text-sm text-amber-800">
                                <strong>Info:</strong> Token emergency bersifat pribadi dan hanya dapat digunakan oleh pemiliknya.
                            </p>
                        </div>
                    `;
                }

                // Show normal single icon for all pages
                Swal.fire({
                    icon: iconType,
                    title: titleText,
                    text: data.message || 'Token yang Anda masukkan tidak valid atau sudah digunakan.',
                    confirmButtonColor: iconType === 'warning' ? '#f59e0b' : '#ef4444',
                    confirmButtonText: iconType === 'warning' ? 'Mengerti' : 'Coba Lagi'
                });

                // Show additional info if exists
                if (additionalInfo) {
                    Swal.showValidationMessage(additionalInfo.replace(/<[^>]*>/g, ''));
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan!',
                text: 'Gagal memvalidasi token. Silakan coba lagi.',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'OK'
            });
        });
    };

    // Fungsi untuk update sidebar emergency menu
    window.updateEmergencySidebar = function(showEmergency) {
        // Trigger Alpine.js reactive update
        if (window.Alpine) {
            window.Alpine.store('emergency', {
                hasValidToken: showEmergency
            });
        }

        // Refresh halaman untuk update sidebar
        setTimeout(() => {
            window.location.reload();
        }, 100);
    };

    // Auto-bind untuk semua link dengan class .token-emergency-trigger
    document.querySelectorAll('.token-emergency-trigger').forEach(element => {
        element.addEventListener('click', function(e) {
            e.preventDefault();
            showTokenModal();
        });
    });
});

// Alpine.js Component untuk Token Emergency
document.addEventListener('alpine:init', () => {
    Alpine.data('tokenEmergency', () => ({
        showModal: false,
        token: '',
        processing: false,

        openModal() {
            this.showModal = true;
            this.$nextTick(() => {
                const input = document.getElementById('token-input');
                if (input) {
                    input.focus();
                    input.select();
                }
            });
        },

        closeModal() {
            this.showModal = false;
            this.token = '';
        },

        async validateToken() {
            if (!this.token || !/^[0-9]{4,6}$/.test(this.token)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Token Tidak Valid!',
                    text: 'Token harus berupa angka 4-6 digit',
                    confirmButtonColor: '#f59e0b',
                    confirmButtonText: 'Mengerti'
                });
                return;
            }

            this.processing = true;

            try {
                const response = await fetch('/token-emergency/validate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        token: this.token
                    })
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Token Berhasil Divalidasi!',
                        text: 'Halaman akan direfresh untuk memperbarui menu emergency.',
                        showConfirmButton: true,
                        confirmButtonText: 'Lanjutkan',
                        confirmButtonColor: '#10b981',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        // Redirect to create emergency medical record page
                        window.location.href = data.redirect_url || '/rekam-medis-emergency/create';
                    });
                } else {
                    // Tampilkan pesan error yang berbeda berdasarkan jenis error
                    let iconType = 'error';
                    let titleText = 'Token Tidak Valid!';
                    let confirmText = 'Coba Lagi';
                    let confirmColor = '#ef4444';

                    if (data.message && data.message.includes('bukan milik Anda')) {
                        iconType = 'warning';
                        titleText = 'Akses Ditolak!';
                        confirmText = 'Mengerti';
                        confirmColor = '#f59e0b';
                    }

                    Swal.fire({
                        icon: iconType,
                        title: titleText,
                        text: data.message || 'Token tidak valid atau sudah digunakan',
                        confirmButtonColor: confirmColor,
                        confirmButtonText: confirmText
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    text: 'Gagal memvalidasi token. Silakan coba lagi.',
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'OK'
                });
            } finally {
                this.processing = false;
            }
        },

        formatToken() {
            this.token = this.token.replace(/[^0-9]/g, '');
        }
    }));
});

