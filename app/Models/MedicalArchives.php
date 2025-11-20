window.KondisiKesehatanHandler = (function() {
    let kondisiKesehatanList = [];
    let fieldCounter = 0;
    
    function init(kondisiList) {
        kondisiKesehatanList = kondisiList || [];
        console.log('KondisiKesehatanHandler initialized with', kondisiKesehatanList.length, 'items');
    }
    
    function initializeSelect2(selectElement) {
        if ($(selectElement).length && !$(selectElement).hasClass('select2-hidden-accessible')) {
            $(selectElement).select2({
                placeholder: 'Pilih atau ketik kondisi kesehatan',
                allowClear: true,
                width: '100%',
                theme: 'default',                @extends('layouts.app')
                
                @section('page-title', 'Medical Check Up')
                
                @push('styles')
                <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
                <style>
                    /* Custom styling for Select2 to match your theme */
                    .select2-container--default .select2-selection--single {
                        border: 1px solid #d1d5db;
                        border-radius: 0.375rem;
                        height: 42px;
                        padding: 0.5rem 0.75rem;
                    }
                    
                    .select2-container--default .select2-selection--single .select2-selection__rendered {
                        line-height: 26px;
                        padding-left: 0;
                    }
                    
                    .select2-container--default .select2-selection--single .select2-selection__arrow {
                        height: 40px;
                    }
                    
                    .select2-container--default.select2-container--focus .select2-selection--single {
                        border-color: #10b981;
                        outline: none;
                        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
                    }
                    
                    .select2-dropdown {
                        border: 1px solid #d1d5db;
                        border-radius: 0.375rem;
                    }
                    
                    .select2-search--dropdown .select2-search__field {
                        border: 1px solid #d1d5db;
                        border-radius: 0.375rem;
                        padding: 0.5rem;
                    }
                    
                    .select2-results__option--highlighted[aria-selected] {
                        background-color: #10b981 !important;
                    }
                    
                    /* Make Select2 work properly in SweetAlert2 */
                    .swal2-container {
                        z-index: 10000;
                    }
                    
                    .select2-container {
                        z-index: 10001;
                    }
                </style>
                @endpush
                
                @push('scripts')
                <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
                <script src="{{ asset('js/medical-checkup-kondisi-handler.js') }}"></script>
                @endpush
                
                // ...existing code...                @extends('layouts.app')
                
                @section('page-title', 'Medical Check Up')
                
                @push('styles')
                <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
                <style>
                    /* Custom styling for Select2 to match your theme */
                    .select2-container--default .select2-selection--single {
                        border: 1px solid #d1d5db;
                        border-radius: 0.375rem;
                        height: 42px;
                        padding: 0.5rem 0.75rem;
                    }
                    
                    .select2-container--default .select2-selection--single .select2-selection__rendered {
                        line-height: 26px;
                        padding-left: 0;
                    }
                    
                    .select2-container--default .select2-selection--single .select2-selection__arrow {
                        height: 40px;
                    }
                    
                    .select2-container--default.select2-container--focus .select2-selection--single {
                        border-color: #10b981;
                        outline: none;
                        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
                    }
                    
                    .select2-dropdown {
                        border: 1px solid #d1d5db;
                        border-radius: 0.375rem;
                    }
                    
                    .select2-search--dropdown .select2-search__field {
                        border: 1px solid #d1d5db;
                        border-radius: 0.375rem;
                        padding: 0.5rem;
                    }
                    
                    .select2-results__option--highlighted[aria-selected] {
                        background-color: #10b981 !important;
                    }
                    
                    /* Make Select2 work properly in SweetAlert2 */
                    .swal2-container {
                        z-index: 10000;
                    }
                    
                    .select2-container {
                        z-index: 10001;
                    }
                </style>
                @endpush
                
                @push('scripts')
                <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
                <script src="{{ asset('js/medical-checkup-kondisi-handler.js') }}"></script>
                @endpush
                
                // ...existing code...                @extends('layouts.app')
                
                @section('page-title', 'Medical Check Up')
                
                @push('styles')
                <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
                <style>
                    /* Custom styling for Select2 to match your theme */
                    .select2-container--default .select2-selection--single {
                        border: 1px solid #d1d5db;
                        border-radius: 0.375rem;
                        height: 42px;
                        padding: 0.5rem 0.75rem;
                    }
                    
                    .select2-container--default .select2-selection--single .select2-selection__rendered {
                        line-height: 26px;
                        padding-left: 0;
                    }
                    
                    .select2-container--default .select2-selection--single .select2-selection__arrow {
                        height: 40px;
                    }
                    
                    .select2-container--default.select2-container--focus .select2-selection--single {
                        border-color: #10b981;
                        outline: none;
                        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
                    }
                    
                    .select2-dropdown {
                        border: 1px solid #d1d5db;
                        border-radius: 0.375rem;
                    }
                    
                    .select2-search--dropdown .select2-search__field {
                        border: 1px solid #d1d5db;
                        border-radius: 0.375rem;
                        padding: 0.5rem;
                    }
                    
                    .select2-results__option--highlighted[aria-selected] {
                        background-color: #10b981 !important;
                    }
                    
                    /* Make Select2 work properly in SweetAlert2 */
                    .swal2-container {
                        z-index: 10000;
                    }
                    
                    .select2-container {
                        z-index: 10001;
                    }
                </style>
                @endpush
                
                @push('scripts')
                <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
                <script src="{{ asset('js/medical-checkup-kondisi-handler.js') }}"></script>
                @endpush
                
                // ...existing code...                // Di dalam fungsi openUploadModal, setelah SweetAlert.fire
                setTimeout(() => {
                    if (typeof window.KondisiKesehatanHandler !== 'undefined') {
                        const kondisiKesehatanList = @json($kondisiKesehatanList ?? []);
                        window.KondisiKesehatanHandler.init(kondisiKesehatanList);
                        window.KondisiKesehatanHandler.setupCreateForm();
                    }
                }, 500);
                
                // Di dalam fungsi editMedicalCheckUp, setelah SweetAlert.fire dengan data edit
                setTimeout(() => {
                    if (typeof window.KondisiKesehatanHandler !== 'undefined') {
                        const kondisiKesehatanList = @json($kondisiKesehatanList ?? []);
                        window.KondisiKesehatanHandler.init(kondisiKesehatanList);
                        window.KondisiKesehatanHandler.setupEditForm(existingKondisiIds);
                    }
                }, 500);                // ...existing code...
                
                @push('styles')
                <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
                <style>
                    .select2-container--default .select2-selection--single {
                        border: 1px solid #d1d5db;
                        border-radius: 0.375rem;
                        height: 42px;
                        padding: 0.5rem 0.75rem;
                    }
                    .select2-container--default .select2-selection--single .select2-selection__rendered {
                        line-height: 26px;
                        padding-left: 0;
                    }
                    .select2-container--default .select2-selection--single .select2-selection__arrow {
                        height: 40px;
                    }
                    .select2-container--default.select2-container--focus .select2-selection--single {
                        border-color: #10b981;
                        outline: none;
                        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
                    }
                    .swal2-container { z-index: 10000; }
                    .select2-container { z-index: 10001; }
                </style>
                @endpush
                
                @push('scripts')
                <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
                <script src="{{ asset('js/medical-checkup-dropdown.js') }}"></script>
                <script>
                    // Use the enhanced functions from medical-checkup-dropdown.js
                    function openUploadModal() {
                        const kondisiKesehatanList = @json($kondisiKesehatanList ?? []);
                        
                        // Your existing Swal.fire code...
                        
                        // After SweetAlert opens, setup Select2
                        setTimeout(() => {
                            if (typeof window.MedicalCheckupDropdown !== 'undefined') {
                                window.MedicalCheckupDropdown.setupCreateFormWithSelect2(kondisiKesehatanList);
                            }
                        }, 500);
                    }
                    
                    function editMedicalCheckUp(checkupId) {
                        const kondisiKesehatanList = @json($kondisiKesehatanList ?? []);
                        
                        // Your existing edit code...
                        
                        // After edit modal opens, setup Select2
                        setTimeout(() => {
                            if (typeof window.MedicalCheckupDropdown !== 'undefined') {
                                window.MedicalCheckupDropdown.setupEditFormWithSelect2(kondisiKesehatanList);
                            }
                        }, 500);
                    }
                </script>
                @endpush
                language: {
                    noResults: function() {
                        return "Tidak ada hasil yang ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });
        }
    }
    
    function addKondisiKesehatanField(containerId, fieldPrefix) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.error('Container not found:', containerId);
            return;
        }
        
        const currentFields = container.querySelectorAll('select').length;
        
        if (currentFields >= 5) {
            if (typeof Swal !== 'undefined') {
                Swal.showValidationMessage('Maksimal 5 kondisi kesehatan');
            } else {
                alert('Maksimal 5 kondisi kesehatan');
            }
            return;
        }
        
        fieldCounter++;
        const fieldDiv = document.createElement('div');
        fieldDiv.className = 'mb-2';
        
        let options = '<option value="">Pilih Kondisi Kesehatan</option>';
        kondisiKesehatanList.forEach(kondisi => {
            options += `<option value="${kondisi.id}">${kondisi.nama_kondisi}</option>`;
        });
        
        const fieldNumber = currentFields + 1;
        const uniqueId = `${fieldPrefix}KondisiKesehatan${fieldCounter}`;
        
        fieldDiv.innerHTML = `
            <select name="id_kondisi_kesehatan[]" id="${uniqueId}"
                    class="kondisi-kesehatan-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                ${options}
            </select>
        `;
        
        container.appendChild(fieldDiv);
        
        // Initialize Select2 on the new field
        setTimeout(() => {
            initializeSelect2(`#${uniqueId}`);
        }, 100);
        
        console.log('Added kondisi kesehatan field:', uniqueId);
    }
    
    function removeKondisiKesehatanField(containerId) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.error('Container not found:', containerId);
            return;
        }
        
        const fields = container.querySelectorAll('div');
        
        if (fields.length <= 1) {
            if (typeof Swal !== 'undefined') {
                Swal.showValidationMessage('Minimal harus ada 1 kondisi kesehatan');
            } else {
                alert('Minimal harus ada 1 kondisi kesehatan');
            }
            return;
        }
        
        const removedField = fields[fields.length - 1];
        const select = removedField.querySelector('select');
        
        // Destroy Select2 before removing
        if ($(select).hasClass('select2-hidden-accessible')) {
            $(select).select2('destroy');
        }
        
        container.removeChild(removedField);
        console.log('Removed kondisi kesehatan field');
    }
    
    function setupCreateForm() {
        setTimeout(() => {
            const addBtn = document.getElementById('addKondisiBtn');
            const removeBtn = document.getElementById('removeKondisiBtn');
            
            if (addBtn) {
                addBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    addKondisiKesehatanField('kondisiKesehatanContainer', 'swal');
                });
            }
            
            if (removeBtn) {
                removeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    removeKondisiKesehatanField('kondisiKesehatanContainer');
                });
            }
            
            // Initialize Select2 on first field
            const firstSelect = document.querySelector('#kondisiKesehatanContainer select');
            if (firstSelect) {
                initializeSelect2(firstSelect);
            }
        }, 300);
    }
    
    function setupEditForm(existingKondisiIds) {
        setTimeout(() => {
            const addBtn = document.getElementById('editAddKondisiBtn');
            const removeBtn = document.getElementById('editRemoveKondisiBtn');
            
            if (addBtn) {
                addBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    addKondisiKesehatanField('editKondisiKesehatanContainer', 'swalEdit');
                });
            }
            
            if (removeBtn) {
                removeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    removeKondisiKesehatanField('editKondisiKesehatanContainer');
                });
            }
            
            // Initialize Select2 on existing fields
            const existingSelects = document.querySelectorAll('#editKondisiKesehatanContainer select');
            existingSelects.forEach(select => {
                initializeSelect2(select);
            });
        }, 300);
    }
    
    return {
        init: init,
        addKondisiKesehatanField: addKondisiKesehatanField,
        removeKondisiKesehatanField: removeKondisiKesehatanField,
        setupCreateForm: setupCreateForm,
        setupEditForm: setupEditForm,
        initializeSelect2: initializeSelect2
    };
})();window.KondisiKesehatanHandler = (function() {
    let kondisiKesehatanList = [];
    let fieldCounter = 0;
    
    function init(kondisiList) {
        kondisiKesehatanList = kondisiList || [];
        console.log('KondisiKesehatanHandler initialized with', kondisiKesehatanList.length, 'items');
    }
    
    function initializeSelect2(selectElement) {
        if ($(selectElement).length && !$(selectElement).hasClass('select2-hidden-accessible')) {
            $(selectElement).select2({
                placeholder: 'Pilih atau ketik kondisi kesehatan',
                allowClear: true,
                width: '100%',
                theme: 'default',
                language: {
                    noResults: function() {
                        return "Tidak ada hasil yang ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });
        }
    }
    
    function addKondisiKesehatanField(containerId, fieldPrefix) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.error('Container not found:', containerId);
            return;
        }
        
        const currentFields = container.querySelectorAll('select').length;
        
        if (currentFields >= 5) {
            if (typeof Swal !== 'undefined') {
                Swal.showValidationMessage('Maksimal 5 kondisi kesehatan');
            } else {
                alert('Maksimal 5 kondisi kesehatan');
            }
            return;
        }
        
        fieldCounter++;
        const fieldDiv = document.createElement('div');
        fieldDiv.className = 'mb-2';
        
        let options = '<option value="">Pilih Kondisi Kesehatan</option>';
        kondisiKesehatanList.forEach(kondisi => {
            options += `<option value="${kondisi.id}">${kondisi.nama_kondisi}</option>`;
        });
        
        const fieldNumber = currentFields + 1;
        const uniqueId = `${fieldPrefix}KondisiKesehatan${fieldCounter}`;
        
        fieldDiv.innerHTML = `
            <select name="id_kondisi_kesehatan[]" id="${uniqueId}"
                    class="kondisi-kesehatan-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                ${options}
            </select>
        `;
        
        container.appendChild(fieldDiv);
        
        // Initialize Select2 on the new field
        setTimeout(() => {
            initializeSelect2(`#${uniqueId}`);
        }, 100);
        
        console.log('Added kondisi kesehatan field:', uniqueId);
    }
    
    function removeKondisiKesehatanField(containerId) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.error('Container not found:', containerId);
            return;
        }
        
        const fields = container.querySelectorAll('div');
        
        if (fields.length <= 1) {
            if (typeof Swal !== 'undefined') {
                Swal.showValidationMessage('Minimal harus ada 1 kondisi kesehatan');
            } else {
                alert('Minimal harus ada 1 kondisi kesehatan');
            }
            return;
        }
        
        const removedField = fields[fields.length - 1];
        const select = removedField.querySelector('select');
        
        // Destroy Select2 before removing
        if ($(select).hasClass('select2-hidden-accessible')) {
            $(select).select2('destroy');
        }
        
        container.removeChild(removedField);
        console.log('Removed kondisi kesehatan field');
    }
    
    function setupCreateForm() {
        setTimeout(() => {
            const addBtn = document.getElementById('addKondisiBtn');
            const removeBtn = document.getElementById('removeKondisiBtn');
            
            if (addBtn) {
                addBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    addKondisiKesehatanField('kondisiKesehatanContainer', 'swal');
                });
            }
            
            if (removeBtn) {
                removeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    removeKondisiKesehatanField('kondisiKesehatanContainer');
                });
            }
            
            // Initialize Select2 on first field
            const firstSelect = document.querySelector('#kondisiKesehatanContainer select');
            if (firstSelect) {
                initializeSelect2(firstSelect);
            }
        }, 300);
    }
    
    function setupEditForm(existingKondisiIds) {
        setTimeout(() => {
            const addBtn = document.getElementById('editAddKondisiBtn');
            const removeBtn = document.getElementById('editRemoveKondisiBtn');
            
            if (addBtn) {
                addBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    addKondisiKesehatanField('editKondisiKesehatanContainer', 'swalEdit');
                });
            }
            
            if (removeBtn) {
                removeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    removeKondisiKesehatanField('editKondisiKesehatanContainer');
                });
            }
            
            // Initialize Select2 on existing fields
            const existingSelects = document.querySelectorAll('#editKondisiKesehatanContainer select');
            existingSelects.forEach(select => {
                initializeSelect2(select);
            });
        }, 300);
    }
    
    return {
        init: init,
        addKondisiKesehatanField: addKondisiKesehatanField,
        removeKondisiKesehatanField: removeKondisiKesehatanField,
        setupCreateForm: setupCreateForm,
        setupEditForm: setupEditForm,
        initializeSelect2: initializeSelect2
    };
})();{{-- Add this in the head section or before closing body tag --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>{{-- Add this in the head section or before closing body tag --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>{{-- Add this in the head section or before closing body tag --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>{{-- Add this in the head section or before closing body tag --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>window.KondisiKesehatanHandler = (function() {
    let kondisiKesehatanList = [];
    let fieldCounter = 0;
    
    function init(kondisiList) {
        kondisiKesehatanList = kondisiList || [];
        console.log('KondisiKesehatanHandler initialized with', kondisiKesehatanList.length, 'items');
    }
    
    function initializeSelect2(selectElement) {
        if ($(selectElement).length && !$(selectElement).hasClass('select2-hidden-accessible')) {
            $(selectElement).select2({
                placeholder: 'Pilih atau ketik kondisi kesehatan',
                allowClear: true,
                width: '100%',
                theme: 'default',
                language: {
                    noResults: function() {
                        return "Tidak ada hasil yang ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });
        }
    }
    
    function addKondisiKesehatanField(containerId, fieldPrefix) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.error('Container not found:', containerId);
            return;
        }
        
        const currentFields = container.querySelectorAll('select').length;
        
        if (currentFields >= 5) {
            if (typeof Swal !== 'undefined') {
                Swal.showValidationMessage('Maksimal 5 kondisi kesehatan');
            } else {
                alert('Maksimal 5 kondisi kesehatan');
            }
            return;
        }
        
        fieldCounter++;
        const fieldDiv = document.createElement('div');
        fieldDiv.className = 'mb-2';
        
        let options = '<option value="">Pilih Kondisi Kesehatan</option>';
        kondisiKesehatanList.forEach(kondisi => {
            options += `<option value="${kondisi.id}">${kondisi.nama_kondisi}</option>`;
        });
        
        const fieldNumber = currentFields + 1;
        const uniqueId = `${fieldPrefix}KondisiKesehatan${fieldCounter}`;
        
        fieldDiv.innerHTML = `
            <select name="id_kondisi_kesehatan[]" id="${uniqueId}"
                    class="kondisi-kesehatan-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                ${options}
            </select>
        `;
        
        container.appendChild(fieldDiv);
        
        // Initialize Select2 on the new field
        setTimeout(() => {
            initializeSelect2(`#${uniqueId}`);
        }, 100);
        
        console.log('Added kondisi kesehatan field:', uniqueId);
    }
    
    function removeKondisiKesehatanField(containerId) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.error('Container not found:', containerId);
            return;
        }
        
        const fields = container.querySelectorAll('div');
        
        if (fields.length <= 1) {
            if (typeof Swal !== 'undefined') {
                Swal.showValidationMessage('Minimal harus ada 1 kondisi kesehatan');
            } else {
                alert('Minimal harus ada 1 kondisi kesehatan');
            }
            return;
        }
        
        const removedField = fields[fields.length - 1];
        const select = removedField.querySelector('select');
        
        // Destroy Select2 before removing
        if ($(select).hasClass('select2-hidden-accessible')) {
            $(select).select2('destroy');
        }
        
        container.removeChild(removedField);
        console.log('Removed kondisi kesehatan field');
    }
    
    function setupCreateForm() {
        setTimeout(() => {
            const addBtn = document.getElementById('addKondisiBtn');
            const removeBtn = document.getElementById('removeKondisiBtn');
            
            if (addBtn) {
                addBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    addKondisiKesehatanField('kondisiKesehatanContainer', 'swal');
                });
            }
            
            if (removeBtn) {
                removeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    removeKondisiKesehatanField('kondisiKesehatanContainer');
                });
            }
            
            // Initialize Select2 on first field
            const firstSelect = document.querySelector('#kondisiKesehatanContainer select');
            if (firstSelect) {
                initializeSelect2(firstSelect);
            }
        }, 300);
    }
    
    function setupEditForm(existingKondisiIds) {
        setTimeout(() => {
            const addBtn = document.getElementById('editAddKondisiBtn');
            const removeBtn = document.getElementById('editRemoveKondisiBtn');
            
            if (addBtn) {
                addBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    addKondisiKesehatanField('editKondisiKesehatanContainer', 'swalEdit');
                });
            }
            
            if (removeBtn) {
                removeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    removeKondisiKesehatanField('editKondisiKesehatanContainer');
                });
            }
            
            // Initialize Select2 on existing fields
            const existingSelects = document.querySelectorAll('#editKondisiKesehatanContainer select');
            existingSelects.forEach(select => {
                initializeSelect2(select);
            });
        }, 300);
    }
    
    return {
        init: init,
        addKondisiKesehatanField: addKondisiKesehatanField,
        removeKondisiKesehatanField: removeKondisiKesehatanField,
        setupCreateForm: setupCreateForm,
        setupEditForm: setupEditForm,
        initializeSelect2: initializeSelect2
    };
})();<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class MedicalArchives extends Model
{
    // Since this is a virtual model that combines multiple tables,
    // we won't use traditional Eloquent methods
    // Instead, we'll use custom queries to get the data
    
    protected $table = 'karyawan';
    protected $primaryKey = 'id_karyawan';
    
    /**
     * Get medical archives for employees only (status = 'aktif')
     * with their family members and medical history
     */
    public static function getEmployeeMedicalRecords($perPage = 50, $search = null, $departmentFilter = null, $statusFilter = null)
    {
        $query = DB::table('karyawan as k')
            ->select([
                'k.id_karyawan',
                'k.nik_karyawan',
                'k.nama_karyawan',
                'k.status as karyawan_status',
                'd.nama_departemen',
                'kl.id_keluarga',
                'kl.nama_keluarga',
                'kl.no_rm',
                'kl.kode_hubungan',
                'h.hubungan as hubungan_nama',
                'rm.id_rekam',
                'rm.tanggal_periksa',
                'rm.status as rekam_status',
                'u.nama_lengkap as petugas'
            ])
            ->leftJoin('departemen as d', 'k.id_departemen', '=', 'd.id_departemen')
            ->leftJoin('keluarga as kl', 'k.id_karyawan', '=', 'kl.id_karyawan')
            ->leftJoin('hubungan as h', 'kl.kode_hubungan', '=', 'h.kode_hubungan')
            ->leftJoin('rekam_medis as rm', 'kl.id_keluarga', '=', 'rm.id_keluarga')
            ->leftJoin('user as u', 'rm.id_user', '=', 'u.id_user')
            ->where('k.status', 'aktif')
            ->whereNotNull('kl.no_rm');
            
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('k.nik_karyawan', 'like', "%{$search}%")
                  ->orWhere('k.nama_karyawan', 'like', "%{$search}%")
                  ->orWhere('kl.nama_keluarga', 'like', "%{$search}%")
                  ->orWhere('kl.no_rm', 'like', "%{$search}%");
            });
        }
        
        // Apply department filter
        if ($departmentFilter) {
            $query->where('d.id_departemen', $departmentFilter);
        }
        
        // Apply status filter (employee status)
        if ($statusFilter) {
            $query->where('k.status', $statusFilter);
        }
        
        // Group by employee and family member to avoid duplicates
        $results = $query->orderBy('k.nama_karyawan')
                         ->orderBy('kl.nama_keluarga')
                         ->get()
                         ->groupBy('id_karyawan');
        
        // Transform the results to match the required format
        $medicalArchives = collect();
        $counter = 1;
        
        foreach ($results as $employeeId => $familyMembers) {
            $employee = $familyMembers->first();
            
            // Generate RM code (NIK-Kode Hubungan)
            $rmCode = $employee->nik_karyawan . '-' . $employee->kode_hubungan;
            
            $medicalArchives->push([
                'id' => $counter++,
                'nik_karyawan' => $employee->nik_karyawan,
                'nama_karyawan' => $employee->nama_karyawan,
                'nama_departemen' => $employee->nama_departemen,
                'rm_code' => $rmCode,
                'nama_pasien' => $employee->nama_keluarga,
                'no_rm' => $employee->no_rm,
                'status' => $employee->karyawan_status, // Use employee status instead of medical record status
                'id_keluarga' => $employee->id_keluarga,
                'id_karyawan' => $employee->id_karyawan,
                'latest_visit_date' => $familyMembers
                    ->where('id_rekam', '!==', null)
                    ->sortByDesc('tanggal_periksa')
                    ->first()->tanggal_periksa ?? null,
                'petugas' => $familyMembers
                    ->where('id_rekam', '!==', null)
                    ->sortByDesc('tanggal_periksa')
                    ->first()->petugas ?? null,
                'hubungan' => $employee->hubungan_nama,
                'family_members' => $familyMembers->map(function($member) {
                    return [
                        'id_keluarga' => $member->id_keluarga,
                        'nama_keluarga' => $member->nama_keluarga,
                        'no_rm' => $member->no_rm,
                        'hubungan' => $member->hubungan_nama,
                        'kode_hubungan' => $member->kode_hubungan
                    ];
                })->unique('id_keluarga')->values()
            ]);
        }
        
        // Apply pagination
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $total = $medicalArchives->count();
        $items = $medicalArchives->slice($offset, $perPage)->values();
        
        // Create a LengthAwarePaginator
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );
        
        return $paginator;
    }
    
    /**
     * Get detailed medical history for a specific employee
     */
    public static function getEmployeeMedicalHistory($id_karyawan)
    {
        return DB::table('karyawan as k')
            ->select([
                'k.id_karyawan',
                'k.nik_karyawan',
                'k.nama_karyawan',
                'k.status as karyawan_status',
                'd.nama_departemen',
                'kl.id_keluarga',
                'kl.nama_keluarga',
                'kl.no_rm',
                'kl.kode_hubungan',
                'h.hubungan as hubungan_nama',
                'rm.id_rekam',
                'rm.tanggal_periksa',
                'rm.status as rekam_status',
                'rm.jumlah_keluhan',
                'u.nama_lengkap as petugas',
                'rm.created_at'
            ])
            ->leftJoin('departemen as d', 'k.id_departemen', '=', 'd.id_departemen')
            ->leftJoin('keluarga as kl', 'k.id_karyawan', '=', 'kl.id_karyawan')
            ->leftJoin('hubungan as h', 'kl.kode_hubungan', '=', 'h.kode_hubungan')
            ->leftJoin('rekam_medis as rm', 'kl.id_keluarga', '=', 'rm.id_keluarga')
            ->leftJoin('user as u', 'rm.id_user', '=', 'u.id_user')
            ->where('k.id_karyawan', $id_karyawan)
            ->where('k.status', 'aktif')
            ->whereNotNull('kl.no_rm')
            ->orderBy('rm.tanggal_periksa', 'desc')
            ->orderBy('kl.nama_keluarga')
            ->get();
    }
    
    /**
     * Get medical archive details for a specific family member
     */
    public static function getFamilyMemberDetails($id_keluarga)
    {
        return DB::table('keluarga as kl')
            ->select([
                'kl.id_keluarga',
                'kl.nama_keluarga',
                'kl.no_rm',
                'kl.kode_hubungan',
                'kl.tanggal_lahir',
                'kl.jenis_kelamin',
                'kl.alamat',
                'kl.bpjs_id',
                'k.id_karyawan',
                'k.nik_karyawan',
                'k.nama_karyawan',
                'd.nama_departemen',
                'h.hubungan as hubungan_nama'
            ])
            ->join('karyawan as k', 'kl.id_karyawan', '=', 'k.id_karyawan')
            ->leftJoin('departemen as d', 'k.id_departemen', '=', 'd.id_departemen')
            ->leftJoin('hubungan as h', 'kl.kode_hubungan', '=', 'h.kode_hubungan')
            ->where('kl.id_keluarga', $id_keluarga)
            ->first();
    }
    
    /**
     * Get departments for filter dropdown
     */
    public static function getDepartments()
    {
        return DB::table('departemen')
            ->select('id_departemen', 'nama_departemen')
            ->orderBy('nama_departemen')
            ->get();
    }
    
    /**
     * Get medical check up records for a specific employee
     */
    public static function getMedicalCheckUp($id_karyawan, $id_keluarga = null)
    {
        $query = DB::table('medical_check_up as mc')
            ->select([
                'mc.id',
                'mc.periode',
                'mc.tanggal',
                'mc.dikeluarkan_oleh',
                'mc.kesimpulan_medis',
                'mc.bmi',
                'mc.imt',
                'mc.rekomendasi',
                'mc.file_name',
                'mc.file_size',
                'mc.mime_type',
                'mc.created_at',
                'u.nama_lengkap as created_by_name'
            ])
            ->leftJoin('user as u', 'mc.id_user', '=', 'u.id_user')
            ->where('mc.id_karyawan', $id_karyawan);
            
        if ($id_keluarga) {
            $query->where('mc.id_keluarga', $id_keluarga);
        }
        
        return $query->orderBy('mc.tanggal', 'desc')
                    ->orderBy('mc.created_at', 'desc')
                    ->get();
    }
    
    /**
     * Get specific medical check up record by ID
     */
    public static function getMedicalCheckUpById($id)
    {
        return DB::table('medical_check_up as mc')
            ->select([
                'mc.id',
                'mc.id_karyawan',
                'mc.id_keluarga',
                'mc.periode',
                'mc.tanggal',
                'mc.dikeluarkan_oleh',
                'mc.kesimpulan_medis',
                'mc.bmi',
                'mc.imt',
                'mc.rekomendasi',
                'mc.file_path',
                'mc.file_name',
                'mc.file_size',
                'mc.mime_type',
                'mc.id_user',
                'mc.created_at',
                'mc.updated_at',
                'u.nama_lengkap as created_by_name'
            ])
            ->leftJoin('user as u', 'mc.id_user', '=', 'u.id_user')
            ->where('mc.id', $id)
            ->first();
    }
    
    /**
     * Get available BMI options
     */
    public static function getBmiOptions()
    {
        return [
            'Underweight',
            'Normal',
            'Overweight',
            'Obesitas Tk 1',
            'Obesitas Tk 2',
            'Obesitas Tk 3'
        ];
    }
    
    /**
     * Get available IMT options
     */
    public static function getImtOptions()
    {
        return [
            'Kurus',
            'Normal',
            'Gemuk',
            'Obesitas'
        ];
    }
}