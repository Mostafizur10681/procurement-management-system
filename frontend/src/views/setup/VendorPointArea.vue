<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <h2 class="mb-4 header-text">Vendor Point Area</h2>
        
        <!-- Form Card -->
        <div class="card shadow-lg mb-4">
          <div class="card-body">
           
            
              <form @submit.prevent="saveVendorPoint" ref="vendorPointForm">
                <!-- Alert Messages -->
                <div v-if="message" class="alert" :class="messageClass" role="alert" style="margin-bottom:0.67rem !important;">
                  {{ message }}
                  <button type="button" class="close" @click="clearMessage" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                
                <div class="col-12">
                  <div class="row">
                    <input type="hidden" name="point_id" id="point_id" value="">
                    
                    <div class="col-md-4">
                      <label class="required text-dark">Point Name</label>
                      <div class="input-group date" id="datetimepicker2" data-target-input="nearest">
                        <input 
                          type="text"
                          v-model="form.point_name"
                          class="form-control"
                          id="point_name"
                          name="point_name"
                          autocomplete="off"
                          required
                        />
                      </div>
                      <span class="text-danger"></span>
                    </div>
                    
                    <div class="col-md-4">
                      <label class="required text-dark">Point Name Bangla</label>
                      <div class="input-group date" id="datetimepicker2" data-target-input="nearest">
                        <input 
                          type="text"
                          v-model="form.point_name_bn"
                          class="form-control datetimepicker-input"
                          id="point_name_bn"
                          name="point_name_bn"
                          autocomplete="off"
                          @input="validateBanglaInput"
                          required
                        />
                      </div>
                      <span class="text-danger"></span>
                    </div>
                    
                    <div class="col-md-4">
                      <label class="required text-dark">Total Score</label>
                      <div class="input-group" id="datetimepicker2" data-target-input="nearest">
                        <input 
                          type="number" 
                          maxlength="9"
                          v-model="form.point_score"
                          class="form-control"
                          id="point_score"
                          name="point_score"
                          autocomplete="off"
                          required
                        />
                      </div>
                      <span class="text-danger"></span>
                    </div>
                    
                    <div class="col-md-4">
                      <label class="required text-dark">Year</label>
                      <div class="input-group date" id="datetimepicker2" data-target-input="nearest">
                        <input 
                          type="number"
                          v-model="form.year"
                          class="form-control"
                          id="year"
                          name="year"
                          autocomplete="off"
                          required
                        />
                      </div>
                      <span class="text-danger"></span>
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
                </div>
                
                <div class="col-md-12 text-right mt-3" id="add" style="text-align: right;">
                  <button 
                    type="button" 
                    @click="cancelForm" 
                    class="btn btn-danger shadow mb-1 me-2"
                  >
                    <i class="fas fa-times"></i> Cancel
                  </button>
                  <button 
                    type="submit" 
                    id="submit"
                    name="save" 
                    class="btn btn-success shadow mb-1"
                    :disabled="loading"
                  >
                    <i class="bx bx-save"></i> {{ editMode ? 'Update' : 'Submit' }}
                  </button>
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
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'

const loading = ref(false)
const editMode = ref(false)
const message = ref('')
const messageClass = ref('alert-danger')

// Form data
const form = reactive({
  point_id: '',
  point_name: '',
  point_name_bn: '',
  point_score: '',
  year: '',
  active_yn: 'Y'
})

// Data arrays
const vendorPointAreas = ref([])

// Validate Bangla input
const validateBanglaInput = (event) => {
  const input = event.target
  const banglaRegex = /[\u0980-\u09FF\s]/
  if (!banglaRegex.test(input.value)) {
    ElMessage.warning('Please enter Bangla text only')
  }
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
  
  showMessage('Form has been cleared', 'info')
}

// Save vendor point
const saveVendorPoint = async () => {
  if (!form.point_name || !form.point_name_bn || !form.point_score || !form.year) {
    showMessage('All fields are required', 'error')
    return
  }
  
  loading.value = true
  
  try {
    const url = editMode.value ? `/api/setup/points-area/${form.point_id}` : '/api/setup/points-area'
    const method = editMode.value ? 'PUT' : 'POST'
    
    const response = await fetch(url, {
      method: method,
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(form)
    })
    
    const data = await response.json()
    
    if (data.success) {
      showMessage(`Vendor Point ${editMode.value ? 'updated' : 'created'} successfully!`)
      if (!editMode.value) {
        cancelForm() // Clear form after successful creation
      }
      loadVendorPointAreas() // Reload the list
    } else {
      showMessage(data.message || 'Failed to save vendor point', 'error')
    }
  } catch (error) {
    console.error('Save error:', error)
    showMessage('Failed to save vendor point', 'error')
  } finally {
    loading.value = false
  }
}

// Edit vendor point
const editVendorPoint = (item) => {
  editMode.value = true
  
  // Populate form with item data
  form.point_id = item.point_id || item.id
  form.point_name = item.point_name
  form.point_name_bn = item.point_name_bn
  form.point_score = item.point_score
  form.year = item.year
  form.active_yn = item.active_yn
}

// Delete vendor point
const deleteVendorPoint = async (item) => {
  try {
    await ElMessageBox.confirm(
      'Do you want to Delete?',
      'Confirm Delete',
      {
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        type: 'warning'
      }
    )
    
    const response = await fetch('/api/setup/points-area-delete', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        id: item.point_id || item.id
      })
    })
    
    const data = await response.json()
    
    if (data.success) {
      ElMessage.success('Deleted!')
      loadVendorPointAreas() // Reload the list
    } else {
      showMessage(data.message || 'Failed to delete vendor point', 'error')
    }
  } catch (error) {
    if (error !== 'cancel') {
      console.error('Delete error:', error)
      showMessage('Failed to delete vendor point', 'error')
    }
  }
}

// Load vendor point areas (datatable simulation)
const loadVendorPointAreas = async () => {
  try {
    const response = await fetch('/api/setup/datatable-points-area')
    const data = await response.json()
    vendorPointAreas.value = data.data || []
  } catch (error) {
    console.error('Failed to load vendor point areas:', error)
    // Add fallback data for testing
    vendorPointAreas.value = [
      {
        id: 1,
        point_id: 1,
        point_name: 'Quality',
        point_name_bn: 'গুণমান',
        point_score: 30,
        year: 2024,
        active_yn: 'Y'
      },
      {
        id: 2,
        point_id: 2,
        point_name: 'Price',
        point_name_bn: 'দাম',
        point_score: 25,
        year: 2024,
        active_yn: 'Y'
      },
      {
        id: 3,
        point_id: 3,
        point_name: 'Delivery',
        point_name_bn: 'ডেলিভারি',
        point_score: 20,
        year: 2024,
        active_yn: 'Y'
      }
    ]
  }
}

// Load initial data
onMounted(async () => {
  console.log('VendorPointArea component mounted')
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
