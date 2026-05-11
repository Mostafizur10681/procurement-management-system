<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <h2 class="mb-4 header-text">Procurement Method Range Form</h2>
        
        <!-- Form Card -->
        <div class="card shadow-lg mb-4">
          <div class="card-body">
              <form @submit.prevent="saveAmountRange" ref="amountRangeForm">
                <!-- Alert Messages -->
                <div v-if="message" class="alert" :class="messageClass" role="alert">
                  {{ message }}
                  <button type="button" class="close" @click="clearMessage" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="required text-dark">Procurement Method</label>
                      <select 
                        v-model="form.l_procurement_method_id" 
                        class="form-control l_procurement_method_id" 
                        :disabled="readonly"
                        @change="handleProcurementMethodChange"
                        required
                      >
                        <option value="">Select procurement method</option>
                        <option 
                          v-for="method in procurementMethods" 
                          :key="method.id" 
                          :value="method.id"
                        >
                          {{ method.procurement_method }}
                        </option>
                      </select>
                    </div>
                  </div>
                  
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="required text-dark">Procurement Type</label>
                      <select 
                        v-model="form.type_id" 
                        class="form-control type_id" 
                        :disabled="readonly"
                        required
                      >
                        <option value="">Select procurement type</option>
                        <option 
                          v-for="type in procurementTypes" 
                          :key="type.procurement_type_id" 
                          :value="type.procurement_type_id"
                        >
                          {{ type.procurement_type_name }}
                        </option>
                      </select>
                    </div>
                  </div>
                  
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="text-dark">Amount From</label>
                      <div class="input-group">
                        <input 
                          type="text" 
                          v-model="form.amount_from" 
                          autocomplete="off" 
                          class="form-control"
                          :readonly="readonly"
                        />
                      </div>
                      <span class="text-danger" v-if="errors.amount_from">{{ errors.amount_from }}</span>
                    </div>
                  </div>
                  
                  <div class="col-md-4 mt-3">
                    <div class="form-group">
                      <label class="text-dark">Amount To</label>
                      <div class="input-group">
                        <input 
                          type="text" 
                          v-model="form.amount_to" 
                          autocomplete="off" 
                          class="form-control"
                          :readonly="readonly"
                        />
                      </div>
                      <span class="text-danger" v-if="errors.amount_to">{{ errors.amount_to }}</span>
                    </div>
                  </div>
                  
                  <div class="col-md-4 mt-3">
                    <div class="form-group">
                      <label class="text-dark">Active</label>
                      <div class="switch-toggle">
                        <label class="switch" :class="{ 'switch-red': isSwitchRed }">
                          <input
                            type="checkbox"
                            v-model="isActive"
                            @change="toggleActiveStatus"
                          />
                          <span class="slider"></span>
                          <span class="switch-icon">
                            <i class="fas fa-check" v-if="isActive && !isSwitchRed"></i>
                          </span>
                        </label>
                        <span class="switch-label">{{ isActive && !isSwitchRed ? 'Active' : 'Inactive' }}</span>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="row mt-3">
                  <div class="col-md-12 d-flex justify-content-end">
                    <button 
                      type="submit" 
                      class="btn btn-success shadow me-2 mb-1" 
                      :disabled="loading"
                    >
                      <i class="bx bx-save"></i> {{ editMode ? 'Update' : 'Save' }}
                    </button>
                    <button 
                      type="button" 
                      @click="cancelForm" 
                      class="btn btn-danger mb-1"
                    >
                      <i class="fas fa-times"></i> Cancel
                    </button>
                  </div>
                </div>
              </form>
          </div>
        </div>
        
        <!-- List Card -->
        <div class="card shadow-lg mb-4">
          <div class="card-body">
            <h4 class="card-title text-bold-400 text-dark">Procurement Method Wise Amount List</h4>
            <div class="table-responsive">
              <table class="table table-sm" ref="dataTable">
                <thead>
                  <tr>
                    <th>SL</th>
                    <th>Procurement Method</th>
                    <th>Amount Range</th>
                    <th>Active Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in amountRanges" :key="item.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ item.procurement_method }}</td>
                    <td>{{ formatAmountRange(item.amount_from, item.amount_to) }}</td>
                    <td>
                      <span class="badge" :class="item.active_yn === 'Y' ? 'badge-success' : 'badge-danger'">
                        {{ item.active_yn === 'Y' ? 'Active' : 'Inactive' }}
                      </span>
                    </td>
                    <td>
                      <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" @click="editAmountRange(item)">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" @click="deleteAmountRange(item)">
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'

const loading = ref(false)
const editMode = ref(false)
const readonly = ref(false)
const message = ref('')
const messageClass = ref('alert-danger')

// Switch toggle state
const isActive = computed({
  get: () => form.active_yn === 'Y',
  set: (value) => {
    form.active_yn = value ? 'Y' : 'N'
  }
})

// Switch state for styling (red when clicked)
const isSwitchRed = ref(false)

// Toggle button methods
const toggleActiveStatus = () => {
  if (isSwitchRed.value) {
    // If switch was red and clicked again, make it green
    isActive.value = true
    isSwitchRed.value = false
  } else {
    // First click - make it red
    isActive.value = false
    isSwitchRed.value = true
  }
}

// Form data
const form = reactive({
  id: '',
  l_procurement_method_id: '',
  type_id: '',
  amount_from: '',
  amount_to: '',
  active_yn: 'Y'
})

// Error handling
const errors = reactive({})

// Data arrays
const procurementMethods = ref([])
const procurementTypes = ref([])
const amountRanges = ref([])

// Handle procurement method change
const handleProcurementMethodChange = async () => {
  const methodId = form.l_procurement_method_id
  
  if (methodId) {
    try {
      const response = await fetch(`/api/setup/get-procurement-type-by-method-id/${methodId}`)
      const data = await response.json()
      
      procurementTypes.value = data.types || []
      form.type_id = '' // Reset procurement type selection
    } catch (error) {
      console.error('Failed to load procurement types:', error)
      procurementTypes.value = []
    }
  } else {
    procurementTypes.value = []
    form.type_id = ''
  }
}

// Format amount range display
const formatAmountRange = (from, to) => {
  if (from && to) {
    return `${from} - ${to}`
  } else if (from) {
    return `From ${from}`
  } else if (to) {
    return `Up to ${to}`
  }
  return 'Not specified'
}

// Clear message
const clearMessage = () => {
  message.value = ''
  messageClass.value = 'alert-danger'
}

// Show message
const showMessage = (msg, type = 'success') => {
  message.value = msg
  messageClass.value = type === 'success' ? 'alert-success' : 'alert-danger'
  
  // Auto clear after 5 seconds
  setTimeout(() => {
    clearMessage()
  }, 5000)
}

// Cancel form
const cancelForm = () => {
  // Clear all form data
  Object.keys(form).forEach(key => {
    if (key === 'active_yn') {
      form[key] = 'Y'
    } else {
      form[key] = ''
    }
  })
  
  // Clear errors
  Object.keys(errors).forEach(key => {
    delete errors[key]
  })
  
  // Reset mode
  editMode.value = false
  readonly.value = false
  
  // Clear procurement types
  procurementTypes.value = []
  
  showMessage('Form has been cleared', 'info')
}

// Validate form
const validateForm = () => {
  const newErrors = {}
  
  if (!form.l_procurement_method_id) {
    newErrors.l_procurement_method_id = 'Procurement method is required'
  }
  
  if (!form.type_id) {
    newErrors.type_id = 'Procurement type is required'
  }
  
  if (!form.active_yn) {
    newErrors.active_yn = 'Active status is required'
  }
  
  // Validate amount range
  if (form.amount_from && form.amount_to) {
    const from = parseFloat(form.amount_from)
    const to = parseFloat(form.amount_to)
    
    if (from >= to) {
      newErrors.amount_to = 'Amount to must be greater than amount from'
    }
  }
  
  // Copy errors to reactive object
  Object.keys(errors).forEach(key => delete errors[key])
  Object.assign(errors, newErrors)
  
  return Object.keys(newErrors).length === 0
}

// Save amount range
const saveAmountRange = async () => {
  if (!validateForm()) {
    showMessage('Please fix the errors in the form', 'error')
    return
  }
  
  loading.value = true
  
  try {
    const url = editMode.value ? `/api/setup/amount-range/${form.id}` : '/api/setup/amount-range'
    const method = editMode.value ? 'PUT' : 'POST'
    
    const response = await fetch(url, {
      method: method,
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(form)
    })
    
    const data = await response.json()
    
    if (data.success) {
      showMessage(`Amount range ${editMode.value ? 'updated' : 'created'} successfully!`)
      if (!editMode.value) {
        cancelForm() // Clear form after successful creation
      }
      loadAmountRanges() // Reload the list
    } else {
      showMessage(data.message || 'Failed to save amount range', 'error')
    }
  } catch (error) {
    console.error('Save error:', error)
    showMessage('Failed to save amount range', 'error')
  } finally {
    loading.value = false
  }
}

// Edit amount range
const editAmountRange = (item) => {
  editMode.value = true
  readonly.value = true
  
  // Populate form with item data
  Object.keys(form).forEach(key => {
    if (item.hasOwnProperty(key)) {
      form[key] = item[key]
    }
  })
  
  // Load procurement types for the selected method
  if (form.l_procurement_method_id) {
    handleProcurementMethodChange()
  }
}

// Delete amount range
const deleteAmountRange = async (item) => {
  try {
    await ElMessageBox.confirm(
      'Are you sure you want to delete this amount range?',
      'Confirm Delete',
      {
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        type: 'warning'
      }
    )
    
    const response = await fetch(`/api/setup/amount-range/${item.id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })
    
    const data = await response.json()
    
    if (data.success) {
      showMessage('Amount range deleted successfully!')
      loadAmountRanges() // Reload the list
    } else {
      showMessage(data.message || 'Failed to delete amount range', 'error')
    }
  } catch (error) {
    if (error !== 'cancel') {
      console.error('Delete error:', error)
      showMessage('Failed to delete amount range', 'error')
    }
  }
}

// Load procurement methods
const loadProcurementMethods = async () => {
  try {
    const response = await fetch('/api/setup/procurement-methods')
    const data = await response.json()
    procurementMethods.value = data.methods || []
    console.log('Procurement methods loaded:', procurementMethods.value)
  } catch (error) {
    console.error('Failed to load procurement methods:', error)
    // Add fallback data for testing
    procurementMethods.value = [
      { id: 1, procurement_method: 'Direct Purchase' },
      { id: 2, procurement_method: 'Tender' },
      { id: 3, procurement_method: 'Quotation' }
    ]
  }
}

// Load amount ranges
const loadAmountRanges = async () => {
  try {
    const response = await fetch('/api/setup/amount-range-data')
    const data = await response.json()
    amountRanges.value = data.data || []
    console.log('Amount ranges loaded:', amountRanges.value)
  } catch (error) {
    console.error('Failed to load amount ranges:', error)
    // Add fallback data for testing
    amountRanges.value = [
      {
        id: 1,
        procurement_method: 'Direct Purchase',
        amount_from: '0',
        amount_to: '10000',
        active_yn: 'Y'
      },
      {
        id: 2,
        procurement_method: 'Tender',
        amount_from: '10001',
        amount_to: '100000',
        active_yn: 'Y'
      }
    ]
  }
}

// Load initial data
onMounted(async () => {
  console.log('AmountRange component mounted')
  await loadProcurementMethods()
  await loadAmountRanges()
  console.log('Initial data loaded')
})
</script>

<style scoped>
.header-text {
  font-size: 24px;
  color: #17316E !important;
  font-weight: 600;
}

/* Switch Toggle Styles */
.switch-toggle {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
  display: none;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: 0.4s;
  border-radius: 24px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: 0.4s;
  border-radius: 50%;
}

.switch input:checked + .slider {
  background-color: #28a745;
}

.switch input:checked + .slider:before {
  transform: translateX(26px);
}

.switch-red input:checked + .slider {
  background-color: #dc3545;
}

.switch-red input:checked + .slider:before {
  transform: translateX(26px);
}

.switch-icon {
  position: absolute;
  left: 28px;
  top: 50%;
  transform: translateY(-50%);
  color: white;
  font-size: 10px;
  opacity: 0;
  transition: opacity 0.3s;
}

.switch input:checked ~ .switch-icon {
  opacity: 1;
}

.switch-label {
  font-weight: 500;
  color: #495057;
  user-select: none;
}

.card {
  border: none;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-title {
  font-weight: 600;
  color: #212529;
  margin-bottom: 1rem;
}

.text-bold-600 {
  font-weight: 600;
}

.form-label {
  font-weight: 500;
  color: #212529;
  margin-bottom: 0.5rem;
}

.form-control {
  color: #000000;
  background-color: #fff;
  border: 1px solid #ced4da;
}

.form-control:focus {
  color: #000000;
  background-color: #fff;
  border-color: #80bdff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

select.form-control {
  color: #000000;
  background-color: #fff;
  border: 1px solid #ced4da;
}

select.form-control:focus {
  color: #000000;
  background-color: #fff;
  border-color: #80bdff;
}

.required::after {
  content: ' *';
  color: #dc3545;
}

.text-danger {
  color: #dc3545;
  font-size: 0.875rem;
}

.badge {
  padding: 0.25em 0.4em;
  font-size: 0.75em;
  font-weight: 700;
  line-height: 1;
  text-align: center;
  white-space: nowrap;
  vertical-align: baseline;
  border-radius: 0.25rem;
}

.badge-success {
  color: #fff;
  background-color: #28a745;
}

.badge-danger {
  color: #fff;
  background-color: #dc3545;
}

.btn {
  padding: 0.375rem 0.75rem;
  font-size: 0.875rem;
  border-radius: 0.25rem;
}

.btn-group {
  position: relative;
  display: inline-flex;
  vertical-align: middle;
}

.btn-group .btn {
  position: relative;
  flex: 1 1 auto;
}

.table-responsive {
  display: block;
  width: 100%;
  overflow-x: auto;
}

.table {
  width: 100%;
  margin-bottom: 1rem;
  color: #212529;
}

.table th,
.table td {
  padding: 0.75rem;
  vertical-align: top;
  border-top: 1px solid #dee2e6;
}

.table thead th {
  vertical-align: bottom;
  border-bottom: 2px solid #dee2e6;
  border-top: 0;
}

.alert {
  position: relative;
  padding: 0.75rem 1.25rem;
  margin-bottom: 1rem;
  border: 1px solid transparent;
  border-radius: 0.25rem;
}

.alert-success {
  color: #155724;
  background-color: #d4edda;
  border-color: #c3e6cb;
}

.alert-danger {
  color: #721c24;
  background-color: #f8d7da;
  border-color: #f5c6cb;
}

.alert-info {
  color: #0c5460;
  background-color: #d1ecf1;
  border-color: #bee5eb;
}

.close {
  float: right;
  font-size: 1.5rem;
  font-weight: 700;
  line-height: 1;
  color: #000;
  text-shadow: 0 1px 0 #fff;
  opacity: 0.5;
  cursor: pointer;
}

.close:hover {
  color: #000;
  text-decoration: none;
  opacity: 0.75;
}

@media (max-width: 768px) {
  .col-md-3 {
    width: 100%;
    margin-bottom: 1rem;
  }
  
  .col-md-12 {
    width: 100%;
  }
}

@media (max-width: 576px) {
  .container-fluid {
    padding-left: 0.5rem;
    padding-right: 0.5rem;
  }
}
</style>
