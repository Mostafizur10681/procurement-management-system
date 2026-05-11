<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <h2 class="mb-4 header-text">Vendor Profile</h2>
        
        <!-- Basic Information Card -->
        <div class="card shadow-lg mb-4">
          <div class="card-body">
            <h4 class="card-title text-bold-600 text-dark">Basic Information</h4>
            <hr>
            
            <form @submit.prevent="saveVendor" ref="vendorForm">
              <!-- Hidden Fields -->
              <input type="hidden" v-model="form.vendor_id" />
              
              <div class="row mb-3">
                <div class="col-md-4">
                  <label class="form-label required text-dark">Name</label>
                  <input 
                    type="text" 
                    placeholder="Enter Name"
                    v-model="form.name" 
                    class="form-control form-control-sm" 
                    :readonly="readonly"
                    required
                  />
                </div>
                <div class="col-md-4">
                  <label class="form-label required text-dark">Short Name</label>
                  <input 
                    type="text" 
                    placeholder="Enter Short Name"
                    v-model="form.short_name" 
                    class="form-control form-control-sm" 
                    :readonly="readonly"
                    required
                  />
                </div>
                <div class="col-md-4">
                  <label class="form-label text-dark">Vendor Type</label>
                  <select 
                    v-model="form.vendor_type" 
                    class="form-control form-control-sm" 
                    :disabled="readonly"
                    @change="handleVendorTypeChange"
                  >
                    <option value="1">External Vendors</option>
                    <option value="2">Internal Vendors</option>
                  </select>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Unique Identification Card -->
        <div class="card shadow-lg mb-4">
          <div class="card-body">
            <h4 class="card-title text-bold-600 text-dark">Unique Identification</h4>
            <hr>
            
            <form>
              <div class="row mb-3">
                <div class="col-md-4">
                  <label class="form-label required text-dark">BIN</label>
                  <input 
                    type="text" 
                    v-model="form.bin" 
                    class="form-control form-control-sm" 
                    maxlength="13"
                    :readonly="readonly"
                    placeholder="Please enter 13 digit BIN number"
                    required
                  />
                </div>
                <div class="col-md-4">
                  <label class="form-label required text-dark">VAT Registration No</label>
                  <input 
                    type="text" 
                    placeholder="Enter VAT Registration No."
                    v-model="form.vat" 
                    class="form-control form-control-sm" 
                    :readonly="readonly"
                    required
                  />
                </div>
                <div class="col-md-4">
                  <label class="form-label required text-dark">TIN</label>
                  <input 
                    type="text" 
                    v-model="form.tin" 
                    class="form-control form-control-sm" 
                    maxlength="10"
                    :readonly="readonly"
                    placeholder="Please enter 10 digit TIN number"
                    required
                  />
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Vendor Address Card -->
        <div class="card shadow-lg mb-4">
          <div class="card-body">
            <h4 class="card-title text-bold-600 text-dark">Vendor Address</h4>
            <hr>
            
            <form>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label required text-dark">Present Address</label>
                  <input 
                    type="text" 
                    placeholder="Enter Present Address"
                    v-model="form.address_1" 
                    class="form-control form-control-sm" 
                    :readonly="readonly"
                    required
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label text-dark">Permanent Address</label>
                  <input 
                    type="text" 
                    placeholder="Enter Premanent Address"
                    v-model="form.address_2" 
                    class="form-control form-control-sm" 
                    :readonly="readonly"
                  />
                </div>
              </div>
              
              <div class="row mb-3">
                <div class="col-md-4">
                  <label class="form-label required text-dark">City</label>
                  <input 
                    type="text" 
                    placeholder="Enter City"
                    v-model="form.city" 
                    class="form-control form-control-sm" 
                    :readonly="readonly"
                    required
                  />
                </div>
                <div class="col-md-4">
                  <label class="form-label required text-dark">State</label>
                  <input 
                    type="text" 
                    placeholder="Enter State"
                    v-model="form.state" 
                    class="form-control form-control-sm" 
                    :readonly="readonly"
                    required
                  />
                </div>
                <div class="col-md-4">
                  <label class="form-label required text-dark">Postal Code</label>
                  <input 
                    type="text" 
                    placeholder="Enter Postal Code"
                    v-model="form.postal_code" 
                    class="form-control form-control-sm" 
                    maxlength="6"
                    :readonly="readonly"
                  />
                </div>
                <div class="col-md-4 mt-3">
                  <label class="form-label required text-dark">Country</label>
                  <select 
                    v-model="form.country" 
                    class="form-control form-control-sm select2" 
                    :disabled="readonly"
                  >
                    <option value="">Select country</option>
                    <option 
                      v-for="country in countries" 
                      :key="country.country_id" 
                      :value="country.country_id"
                      :selected="form.country === country.country_id"
                    >
                      {{ country.country }}
                    </option>
                  </select>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Contact Person Card -->
        <div class="card shadow-lg mb-4">
          <div class="card-body">
            <h4 class="card-title text-bold-600 text-dark">Contact Person</h4>
            <hr>
            
            <form>
              <div class="row mb-3">
                <div class="col-md-4">
                  <label class="form-label required text-dark">Contact Name</label>
                  <input 
                    type="text" 
                    placeholder="Enter Contact Name"
                    v-model="form.contact_name" 
                    class="form-control form-control-sm" 
                    :readonly="readonly"
                    required
                  />
                </div>
                <div class="col-md-4">
                  <label class="form-label required text-dark">Mobile No</label>
                  <input 
                    type="text" 
                    placeholder="Enter Mobile No."
                    v-model="form.mobile" 
                    class="form-control form-control-sm mobilecheck" 
                    :readonly="readonly"
                    required
                  />
                </div>
                <div class="col-md-4">
                  <label class="form-label required text-dark">Email Address</label>
                  <input 
                    type="email" 
                    placeholder="Enter Email Address"
                    v-model="form.email" 
                    class="form-control form-control-sm" 
                    :readonly="readonly"
                    required
                  />
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Bank Information Card -->
        <div class="card shadow-lg mb-4" :class="{ 'd-none': !showBankInfo }">
          <div class="card-body">
            <h4 class="card-title text-bold-600 text-dark">Vendor Bank Information</h4>
            <hr>
            
            <form>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label required text-dark">Bank</label>
                  <select 
                    v-model="form.bank_id" 
                    class="form-control form-control-sm select2" 
                    :disabled="readonly"
                    @change="handleBankChange"
                  >
                    <option value="">Select Bank</option>
                    <option 
                      v-for="bank in banks" 
                      :key="bank.bank_id" 
                      :value="bank.bank_id"
                      :selected="form.bank_id === bank.bank_id"
                    >
                      {{ bank.bank_name }}
                    </option>
                  </select>
                </div>
              </div>
              
              <div class="row mb-3" :class="{ 'd-none': !showBankInfo }">
                <div class="col-md-6">
                  <label class="form-label required text-dark">Bank Branch</label>
                  <select 
                    v-model="form.branch_id" 
                    class="form-control form-control-sm select2" 
                    :disabled="readonly"
                    @change="handleBranchChange"
                  >
                    <option value="">Select Branch</option>
                    <option 
                      v-for="branch in branches" 
                      :key="branch.branch_id" 
                      :value="branch.branch_id"
                      :selected="form.branch_id === branch.branch_id"
                    >
                      {{ branch.branch_name }}
                    </option>
                  </select>
                </div>
              </div>
              
              <div class="row mb-3" :class="{ 'd-none': !showBankInfo }">
                <div class="col-md-4">
                  <label class="form-label required text-dark">Routing Number</label>
                  <input 
                    type="text" 
                    v-model="form.routing_number" 
                    class="form-control form-control-sm" 
                    maxlength="8"
                    :readonly="readonly"
                  />
                </div>
                <div class="col-md-4">
                  <label class="form-label required text-dark">Account No</label>
                  <input 
                    type="text" 
                    v-model="form.account_no" 
                    class="form-control form-control-sm" 
                    maxlength="13"
                    :readonly="readonly"
                  />
                </div>
                <div class="col-md-4">
                  <label class="form-label text-dark">Account Title</label>
                  <input 
                    type="text" 
                    v-model="form.account_title" 
                    class="form-control form-control-sm" 
                    :readonly="readonly"
                  />
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Status Card -->
        <div class="card shadow-lg mb-4">
          <div class="card-body">
            <h4 class="card-title text-bold-600 text-dark">Vendor Active Status</h4>
            <hr>
            
            <form>
              <div class="col-md-3 mt-3">
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
            </form>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-4">
          <div class="col-md-12 d-flex justify-content-end">
            <button 
              type="button" 
              @click="cancelForm" 
              class="btn btn-danger me-2"
            >
              <i class="fas fa-times"></i> Cancel
            </button>
            <button 
              type="button" 
              @click="saveVendor" 
              class="btn btn-success" 
              :disabled="loading"
            >
              <i class="fas fa-save"></i> {{ editMode ? 'Update' : 'Save' }}
            </button>
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
const showBankInfo = ref(false)

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
const rules = {
  name: [
    { required: true, message: 'Name is required', trigger: 'blur' },
    { min: 2, message: 'Name must be at least 2 characters', trigger: 'blur' }
  ],
  short_name: [
    { required: true, message: 'Short name is required', trigger: 'blur' }
  ],
  vendor_type: [
    { required: true, message: 'Vendor type is required', trigger: 'change' }
  ],
  bin: [
    { required: true, message: 'BIN is required', trigger: 'blur' },
    { min: 13, message: 'BIN must be exactly 13 digits', trigger: 'blur' }
  ],
  vat: [
    { required: true, message: 'VAT registration number is required', trigger: 'blur' }
  ],
  tin: [
    { required: true, message: 'TIN is required', trigger: 'blur' },
    { min: 10, message: 'TIN must be exactly 10 digits', trigger: 'blur' }
  ],
  address_1: [
    { required: true, message: 'Address line 1 is required', trigger: 'blur' }
  ],
  city: [
    { required: true, message: 'City is required', trigger: 'blur' }
  ],
  state: [
    { required: true, message: 'State is required', trigger: 'blur' }
  ],
  postal_code: [
    { required: true, message: 'Postal code is required', trigger: 'blur' },
    { max: 6, message: 'Postal code must be maximum 6 digits', trigger: 'blur' }
  ],
  country: [
    { required: true, message: 'Country is required', trigger: 'change' }
  ],
  contact_name: [
    { required: true, message: 'Contact name is required', trigger: 'blur' }
  ],
  mobile: [
    { required: true, message: 'Mobile number is required', trigger: 'blur' },
    { pattern: /^(\+88|88)?(01[3-9]\d{8})$/, message: 'Invalid mobile number format', trigger: 'blur' }
  ],
  email: [
    { required: true, message: 'Email address is required', trigger: 'blur' },
    { type: 'email', message: 'Invalid email format', trigger: 'blur' }
  ],
  bank_id: [
    { required: true, message: 'Bank is required', trigger: 'change' }
  ],
  branch_id: [
    { required: true, message: 'Bank branch is required', trigger: 'change' }
  ],
  routing_number: [
    { required: true, message: 'Routing number is required', trigger: 'blur' },
    { max: 8, message: 'Routing number must be maximum 8 digits', trigger: 'blur' }
  ],
  account_no: [
    { required: true, message: 'Account number is required', trigger: 'blur' },
    { max: 13, message: 'Account number must be maximum 13 digits', trigger: 'blur' }
  ],
  account_title: [
    { required: true, message: 'Account title is required', trigger: 'blur' }
  ]
}

const form = reactive({
  vendor_id: '',
  name: '',
  short_name: '',
  vendor_type: '1',
  enlisted_vendor: false,
  bin: '',
  vat: '',
  tin: '',
  address_1: '',
  address_2: '',
  city: '',
  state: '',
  postal_code: '',
  country: '',
  contact_name: '',
  mobile: '',
  email: '',
  bank_id: '',
  branch_id: '',
  routing_number: '',
  account_no: '',
  account_title: '',
  active_yn: 'Y'
})

// Dropdown data
const banks = ref([])
const branches = ref([])
const countries = ref([])

// Handle vendor type change
const handleVendorTypeChange = () => {
  if (form.vendor_type == 1) {
    // External Vendor
    showBankInfo.value = true
  } else if (form.vendor_type == 2) {
    // Internal Vendor
    showBankInfo.value = false
  } else {
    showBankInfo.value = false
  }
}

// Handle bank change
const handleBankChange = () => {
  loadBranches(form.bank_id)
}

// Handle branch change
const handleBranchChange = () => {
  const selectedBranch = branches.value.find(b => b.branch_id === form.branch_id)
  if (selectedBranch) {
    form.routing_number = selectedBranch.routing || ''
  }
}

// Load branches for selected bank
const loadBranches = async (bankId) => {
  try {
    const response = await fetch(`/api/branches/${bankId}`)
    const data = await response.json()
    branches.value = data.branches || []
  } catch (error) {
    console.error('Failed to load branches:', error)
    branches.value = []
  }
}

// Cancel form
const cancelForm = () => {
  // Clear all form data
  Object.keys(form).forEach(key => {
    if (typeof form[key] === 'boolean') {
      form[key] = key === 'active_yn' ? 'Y' : false
    } else {
      form[key] = ''
    }
  })
  
  // Reset vendor type to default
  form.vendor_type = '1'
  showBankInfo.value = false
  
  ElMessage.info('Form has been cleared')
}

// Save vendor
const saveVendor = async () => {
  // Validate mobile number
  const mobileRegex = /^(\+88|88)?(01[3-9]\d{8})$/
  if (!mobileRegex.test(form.mobile)) {
    ElMessage.error('Invalid mobile number')
    return
  }
  
  // Validate email
  const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/
  if (!emailRegex.test(form.email)) {
    ElMessage.error('Invalid email address')
    return
  }
  
  loading.value = true
  
  try {
    const url = editMode.value ? `/api/vendors/${form.vendor_id}` : '/api/vendors'
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
      ElMessage.success(`Vendor ${editMode.value ? 'updated' : 'created'} successfully!`)
      if (!editMode.value) {
        cancelForm() // Clear form after successful creation
      }
    } else {
      ElMessage.error(data.message || 'Failed to save vendor')
    }
  } catch (error) {
    console.error('Save error:', error)
    ElMessage.error('Failed to save vendor')
  } finally {
    loading.value = false
  }
}

// Load initial data
onMounted(async () => {
  try {
    // Load banks
    const banksResponse = await fetch('/api/banks')
    const banksData = await banksResponse.json()
    banks.value = banksData.banks || []
    
    // Load countries
    const countriesResponse = await fetch('/api/countries')
    const countriesData = await countriesResponse.json()
    countries.value = countriesData.countries || []
    
    // Check if editing existing vendor
    const urlParams = new URLSearchParams(window.location.search)
    const vendorId = urlParams.get('id')
    if (vendorId) {
      editMode.value = true
      readonly.value = true
      
      // Load vendor data
      const vendorResponse = await fetch(`/api/vendors/${vendorId}`)
      const vendorData = await vendorResponse.json()
      
      if (vendorData.success) {
        Object.keys(vendorData.vendor).forEach(key => {
          if (form.hasOwnProperty(key)) {
            form[key] = vendorData.vendor[key]
          }
        })
        
        // Load branches for the vendor's bank
        if (form.bank_id) {
          await loadBranches(form.bank_id)
        }
        
        // Show bank info based on vendor type
        handleVendorTypeChange()
      }
    }
  } catch (error) {
    console.error('Failed to load initial data:', error)
  }
})
</script>

<style scoped>
.card {
  border: none;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-title {
  font-weight: 600;
  color: #333;
  margin-bottom: 1rem;
}

.header-text{
  font-size: 24px;
  color: #17316E !important;
  font-weight: 600;
}

.text-bold-600 {
  font-weight: 600;
}

.form-label {
  font-weight: 500;
  color: #495057;
  margin-bottom: 0.5rem;
}

.form-control-sm {
  padding: 0.375rem 0.75rem;
  font-size: 0.875rem;
  line-height: 1.5;
  border-radius: 0.25rem;
}

.btn {
  padding: 0.375rem 0.75rem;
  font-size: 0.875rem;
  border-radius: 0.25rem;
}

.me-2 {
  margin-right: 0.5rem;
}

@media (max-width: 768px) {
  .col-md-6 {
    width: 100%;
    margin-bottom: 1rem;
  }
  
  .col-md-4 {
    width: 100%;
    margin-bottom: 1rem;
  }
  
  .col-md-3 {
    width: 100%;
    margin-bottom: 1rem;
  }
  
  .col-md-2 {
    width: 100%;
    margin-bottom: 1rem;
  }
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

@media (max-width: 576px) {
  .container-fluid {
    padding-left: 0.5rem;
    padding-right: 0.5rem;
  }
}
</style>
