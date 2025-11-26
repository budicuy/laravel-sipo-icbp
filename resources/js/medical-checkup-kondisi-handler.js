/**
 * Medical Check Up Kondisi Kesehatan Handler
 * Handles dynamic addition and removal of kondisi kesehatan fields
 */

// Global variables to store functions and data
window.KondisiKesehatanHandler = {
    kondisiKesehatanList: [],
    maxFields: 5,
    minFields: 1,
    
    /**
     * Initialize the handler with kondisi kesehatan list
     */
    init: function(kondisiKesehatanList) {
        this.kondisiKesehatanList = kondisiKesehatanList;
        console.log('Kondisi Kesehatan Handler initialized with:', kondisiKesehatanList);
    },
    
    /**
     * Add new kondisi kesehatan field
     */
    addKondisiKesehatanField: function(containerId, fieldPrefix) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.error('Container not found:', containerId);
            return;
        }
        
        const currentFields = container.querySelectorAll('input[name="kondisi_kesehatan_text[]"]').length;
        
        if (currentFields >= this.maxFields) {
            if (typeof Swal !== 'undefined') {
                Swal.showValidationMessage(`Maksimal ${this.maxFields} kondisi kesehatan`);
            } else {
                alert(`Maksimal ${this.maxFields} kondisi kesehatan`);
            }
            return;
        }
        
        const fieldDiv = document.createElement('div');
        fieldDiv.className = 'mb-2 relative';
        
        const fieldNumber = currentFields + 1;
        fieldDiv.innerHTML = `
            <input type="text"
                   name="kondisi_kesehatan_text[]"
                   id="${fieldPrefix}KondisiKesehatan${fieldNumber}"
                   placeholder="Ketik nama gangguan kesehatan..."
                   autocomplete="off"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
            <input type="hidden" name="id_kondisi_kesehatan[]" id="${fieldPrefix}KondisiKesehatan${fieldNumber}_hidden" value="">
            <div id="${fieldPrefix}KondisiKesehatan${fieldNumber}_dropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto hidden"></div>
        `;
        
        container.appendChild(fieldDiv);
        
        // Setup autocomplete for the new field
        if (typeof setupKondisiKesehatanAutocomplete === 'function') {
            setupKondisiKesehatanAutocomplete(
                `${fieldPrefix}KondisiKesehatan${fieldNumber}`,
                `${fieldPrefix}KondisiKesehatan${fieldNumber}_dropdown`,
                `${fieldPrefix}KondisiKesehatan${fieldNumber}_hidden`,
                this.kondisiKesehatanList
            );
        }
        
        console.log('Added kondisi kesehatan field:', fieldNumber);
    },
    
    /**
     * Remove last kondisi kesehatan field
     */
    removeKondisiKesehatanField: function(containerId) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.error('Container not found:', containerId);
            return;
        }
        
        const fields = container.querySelectorAll('div.mb-2');
        
        if (fields.length <= this.minFields) {
            if (typeof Swal !== 'undefined') {
                Swal.showValidationMessage(`Minimal harus ada ${this.minFields} kondisi kesehatan`);
            } else {
                alert(`Minimal harus ada ${this.minFields} kondisi kesehatan`);
            }
            return;
        }
        
        const removedField = fields[fields.length - 1];
        container.removeChild(removedField);
        console.log('Removed kondisi kesehatan field');
    },
    
    /**
     * Setup event listeners for create form
     */
    setupCreateForm: function() {
        const self = this;
        
        // Use a more reliable approach with direct onclick assignment
        setTimeout(function() {
            const addBtn = document.getElementById('addKondisiBtn');
            const removeBtn = document.getElementById('removeKondisiBtn');
            
            console.log('Setting up create form event listeners...');
            console.log('Add button found:', !!addBtn);
            console.log('Remove button found:', !!removeBtn);
            
            if (addBtn && removeBtn) {
                // Direct onclick assignment - most reliable approach
                addBtn.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Add button clicked via direct onclick');
                    self.addKondisiKesehatanField('kondisiKesehatanContainer', 'swal');
                };
                
                removeBtn.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Remove button clicked via direct onclick');
                    self.removeKondisiKesehatanField('kondisiKesehatanContainer');
                };
                
                // Also add event listeners as backup
                addBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Add button clicked via event listener');
                    self.addKondisiKesehatanField('kondisiKesehatanContainer', 'swal');
                });
                
                removeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Remove button clicked via event listener');
                    self.removeKondisiKesehatanField('kondisiKesehatanContainer');
                });
                
                console.log('Create form event listeners setup completed');
            } else {
                console.error('Create form buttons not found');
            }
        }, 100);
    },
    
    /**
     * Setup event listeners for edit form
     */
    setupEditForm: function(existingKondisiIds = []) {
        const self = this;
        
        // Generate initial fields first
        const container = document.getElementById('editKondisiKesehatanContainer');
        if (container) {
            container.innerHTML = '';
            
            const initialCount = existingKondisiIds.length > 0 ? existingKondisiIds.length : 1;
            for (let i = 0; i < initialCount; i++) {
                const existingValue = existingKondisiIds[i] || '';
                this.addKondisiKesehatanField('editKondisiKesehatanContainer', 'swalEdit');
                
                // Set the value if exists
                if (existingValue) {
                    const input = document.getElementById(`swalEditKondisiKesehatan${i + 1}`);
                    const hiddenInput = document.getElementById(`swalEditKondisiKesehatan${i + 1}_hidden`);
                    
                    if (input && hiddenInput) {
                        // Find the kondisi name from the list
                        const kondisi = this.kondisiKesehatanList.find(k => k.id === existingValue);
                        if (kondisi) {
                            input.value = kondisi.nama_kondisi;
                            hiddenInput.value = kondisi.id;
                        }
                    }
                }
            }
        }
        
        // Use a more reliable approach with direct onclick assignment
        setTimeout(function() {
            const addBtn = document.getElementById('editAddKondisiBtn');
            const removeBtn = document.getElementById('editRemoveKondisiBtn');
            
            console.log('Setting up edit form event listeners...');
            console.log('Edit add button found:', !!addBtn);
            console.log('Edit remove button found:', !!removeBtn);
            console.log('Existing kondisi IDs:', existingKondisiIds);
            
            if (addBtn && removeBtn) {
                // Direct onclick assignment - most reliable approach
                addBtn.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Edit add button clicked via direct onclick');
                    self.addKondisiKesehatanField('editKondisiKesehatanContainer', 'swalEdit');
                };
                
                removeBtn.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Edit remove button clicked via direct onclick');
                    self.removeKondisiKesehatanField('editKondisiKesehatanContainer');
                };
                
                // Also add event listeners as backup
                addBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Edit add button clicked via event listener');
                    self.addKondisiKesehatanField('editKondisiKesehatanContainer', 'swalEdit');
                });
                
                removeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Edit remove button clicked via event listener');
                    self.removeKondisiKesehatanField('editKondisiKesehatanContainer');
                });
                
                console.log('Edit form event listeners setup completed with existing IDs:', existingKondisiIds);
            } else {
                console.error('Edit form buttons not found');
            }
        }, 100);
    },
    
    /**
     * Handle add button click for create form
     */
    handleAddClick: function(event) {
        event.preventDefault();
        console.log('Add button clicked via event listener');
        this.addKondisiKesehatanField('kondisiKesehatanContainer', 'swal');
    },
    
    /**
     * Handle remove button click for create form
     */
    handleRemoveClick: function(event) {
        event.preventDefault();
        console.log('Remove button clicked via event listener');
        this.removeKondisiKesehatanField('kondisiKesehatanContainer');
    },
    
    /**
     * Handle add button click for edit form
     */
    handleEditAddClick: function(event) {
        event.preventDefault();
        console.log('Edit add button clicked via event listener');
        this.addKondisiKesehatanField('editKondisiKesehatanContainer', 'swalEdit');
    },
    
    /**
     * Handle remove button click for edit form
     */
    handleEditRemoveClick: function(event) {
        event.preventDefault();
        console.log('Edit remove button clicked via event listener');
        this.removeKondisiKesehatanField('editKondisiKesehatanContainer');
    },
    
    /**
     * Setup autocomplete for a specific field
     */
    setupAutocomplete: function(inputId, dropdownId, hiddenInputId) {
        const input = document.getElementById(inputId);
        const dropdown = document.getElementById(dropdownId);
        const hiddenInput = document.getElementById(hiddenInputId);
        
        if (!input || !dropdown || !hiddenInput) {
            console.error('Autocomplete elements not found:', { inputId, dropdownId, hiddenInputId });
            return;
        }
        
        console.log('Setting up autocomplete for:', inputId, 'with', this.kondisiKesehatanList.length, 'items');
        
        input.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            dropdown.innerHTML = '';
            dropdown.classList.add('hidden');
            
            if (query.length < 1) {
                hiddenInput.value = '';
                return;
            }
            
            const matches = window.KondisiKesehatanHandler.kondisiKesehatanList.filter(kondisi =>
                kondisi.nama_kondisi.toLowerCase().includes(query)
            );
            
            console.log('Query:', query, 'Matches:', matches.length);
            
            if (matches.length > 0) {
                dropdown.classList.remove('hidden');
                matches.forEach(kondisi => {
                    const item = document.createElement('div');
                    item.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer text-sm';
                    item.textContent = kondisi.nama_kondisi;
                    
                    item.addEventListener('click', function() {
                        input.value = kondisi.nama_kondisi;
                        hiddenInput.value = kondisi.id;
                        dropdown.innerHTML = '';
                        dropdown.classList.add('hidden');
                        console.log('Selected:', kondisi.nama_kondisi, 'ID:', kondisi.id);
                    });
                    
                    dropdown.appendChild(item);
                });
            } else {
                dropdown.classList.add('hidden');
            }
        });
        
        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    }
};

// Global autocomplete setup function
function setupKondisiKesehatanAutocomplete(inputId, dropdownId, hiddenInputId, kondisiList) {
    if (typeof window.KondisiKesehatanHandler !== 'undefined') {
        if (kondisiList) {
            window.KondisiKesehatanHandler.kondisiKesehatanList = kondisiList;
        }
        window.KondisiKesehatanHandler.setupAutocomplete(inputId, dropdownId, hiddenInputId);
    } else {
        console.error('KondisiKesehatanHandler not found');
    }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Check if we have kondisi kesehatan data from blade
    if (typeof window.kondisiKesehatanList !== 'undefined') {
        window.KondisiKesehatanHandler.init(window.kondisiKesehatanList);
    }
});