<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <h2 class="mb-4 header-text">Vendor Point Area</h2>
        
        <!-- Form Card -->
        <div class="card shadow-lg mb-4">
          <div class="card-body">
            <h4 class="card-title text-bold-600 text-dark">Add Vendor Point Area</h4>
            <hr>
            
            <form @submit.prevent="saveVendorPoint" ref="vendorPointForm">
              <!-- Alert Messages -->
              <div v-if="message" class="alert" :class="messageClass" role="alert">
                {{ message }}
                <button type="button" class="close" @click="clearMessage" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="required text-dark">Vendor</label>
                    <select 
                      v-model="form.vendor_id" 
                      class="form-control" 
                      :disabled="readonly"
                      @change="handleVendorChange"
                      required
                    >
                      <option value="">Select vendor</option>
                      <option 
                        v-for="vendor in vendors" 
                        :key="vendor.id" 
                        :value="vendor.id"
                      >
                        {{ vendor.name }}
                      </option>
                    </select>
                  </div>
                </div>
                
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="required text-dark">Point Area Name</label>
                      <input 
                        type="text" 
                        v-model="form.area_name" 
                        class="form-control"
                        placeholder="Enter point area name"
                        :readonly="readonly"
                        required
                      />
                    </div>
                  </div>
                </div>
                
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="text-dark">Description</label>
                      <textarea 
                        v-model="form.description" 
                        class="form-control"
                        rows="3"
                        placeholder="Enter description"
                        :readonly="readonly"
                      ></textarea>
                    </div>
                  </div>
                </div>
                
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="text-dark">Active Status</label>
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
                </div>
              </form>
            </div>
        </div>
        
        <!-- List Card -->
        <div class="card shadow-lg mb-4">
          <div class="card-body">
            <h4 class="card-title text-bold-600 text-dark">Vendor Point Area List</h4>
            <div class="table-responsive">
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>SL</th>
                    <th>Vendor</th>
                    <th>Point Area Name</th>
                    <th>Description</th>
                    <th>Active Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in vendorPointAreas" :key="item.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ item.vendor_name }}</td>
                    <td>{{ item.area_name }}</td>
                    <td>{{ item.description || 'N/A' }}</td>
                    <td>
                      <span class="badge" :class="item.active_yn === 'Y' ? 'badge-success' : 'badge-danger'">
                        {{ item.active_yn === 'Y' ? 'Active' : 'Inactive' }}
                      </span>
                    </td>
                    <td>
                      <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" @click="editVendorPoint(item)">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" @click="deleteVendorPoint(item)">
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
import { ref, reactive, computed, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'

const loading = ref(false)
const editMode = ref(false)
const readonly = ref(false)
const message = ref('')
const messageClass = ref('alert-danger')

// Form data
const form = reactive({
  id: '',
  vendor_id: '',
  area_name: '',
  description: '',
  active_yn: 'Y'
})

// Switch toggle state
const isActive = computed({
  get: () => form.active_yn === 'Y',
  set: (value) => {
    form.active_yn = value ? 'Y' : 'N'
  }
})

// Switch state for styling (red when clicked)
const isSwitchRed = ref(false)

// Data arrays
const vendors = ref([])
const vendorPointAreas = ref([])

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

// Handle vendor change
const handleVendorChange = () => {
  // Load vendor point areas for selected vendor
  console.log('Vendor changed:', form.vendor_id)
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
  
  // Reset mode
  editMode.value = false
  readonly.value = false
  
  showMessage('Form has been cleared', 'info')
}

// Save vendor point area
const saveVendorPoint = async () => {
  if (!form.vendor_id || !form.area_name) {
    showMessage('Vendor and Point Area Name are required', 'error')
    return
  }
  
  loading.value = true
  
  try {
    const url = editMode.value ? `/api/setup/vendor-point-area/${form.id}` : '/api/setup/vendor-point-area'
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
      showMessage(`Vendor Point Area ${editMode.value ? 'updated' : 'created'} successfully!`)
      if (!editMode.value) {
        cancelForm() // Clear form after successful creation
      }
      loadVendorPointAreas() // Reload the list
    } else {
      showMessage(data.message || 'Failed to save vendor point area', 'error')
    }
  } catch (error) {
    console.error('Save error:', error)
    showMessage('Failed to save vendor point area', 'error')
  } finally {
    loading.value = false
  }
}

// Edit vendor point area
const editVendorPoint = (item) => {
  editMode.value = true
  readonly.value = true
  
  // Populate form with item data
  Object.keys(form).forEach(key => {
    if (item.hasOwnProperty(key)) {
      form[key] = item[key]
    }
  })
}

// Delete vendor point area
const deleteVendorPoint = async (item) => {
  try {
    await ElMessageBox.confirm(
      'Are you sure you want to delete this vendor point area?',
      'Confirm Delete',
      {
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        type: 'warning'
      }
    )
    
    const response = await fetch(`/api/setup/vendor-point-area/${item.id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })
    
    const data = await response.json()
    
    if (data.success) {
      showMessage('Vendor point area deleted successfully!')
      loadVendorPointAreas() // Reload the list
    } else {
      showMessage(data.message || 'Failed to delete vendor point area', 'error')
    }
  } catch (error) {
    if (error !== 'cancel') {
      console.error('Delete error:', error)
      showMessage('Failed to delete vendor point area', 'error')
    }
  }
}

// Load vendors
const loadVendors = async () => {
  try {
    const response = await fetch('/api/setup/vendors')
    const data = await response.json()
    vendors.value = data.vendors || []
  } catch (error) {
    console.error('Failed to load vendors:', error)
    // Add fallback data for testing
    vendors.value = [
      { id: 1, name: 'Vendor A' },
      { id: 2, name: 'Vendor B' },
      { id: 3, name: 'Vendor C' }
    ]
  }
}

// Load vendor point areas
const loadVendorPointAreas = async () => {
  try {
    const response = await fetch('/api/setup/vendor-point-areas')
    const data = await response.json()
    vendorPointAreas.value = data.data || []
  } catch (error) {
    console.error('Failed to load vendor point areas:', error)
    // Add fallback data for testing
    vendorPointAreas.value = [
      {
        id: 1,
        vendor_name: 'Vendor A',
        area_name: 'North Zone',
        description: 'Main service area for northern region',
        active_yn: 'Y'
      },
      {
        id: 2,
        vendor_name: 'Vendor B',
        area_name: 'South Zone',
        description: 'Service coverage for southern region',
        active_yn: 'Y'
      }
    ]
  }
}

// Load initial data
onMounted(async () => {
  console.log('VendorPointArea component mounted')
  await loadVendors()
  await loadVendorPointAreas()
  console.log('Initial data loaded')
})
</script>

<style scoped>
.header-text {
  font-size: 24px;
  color: #17316E !important;
  font-weight: 600;
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

.text-bold-400 {
  font-weight: 400;
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
  .col-md-6 {
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
