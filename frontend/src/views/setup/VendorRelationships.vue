<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <!-- Filter Section -->
        <div class="card showFilter" style="display: none">
          <div class="head_title">Purchase Requisition</div>
          <div class="card-body">
            <form @submit.prevent="searchRelationships" id="search-form">
              <div class="row">
                <div class="col-md-3">
                  <label class=""></label>
                  <select class="form-control select2" v-model="filters.department_id" id="department_id">
                    <option value="">Select One</option>
                    <option value=""></option>
                  </select>
                </div>
              </div>
              <div class="row mt-1">
                <div class="col-md-12 text-right" id="add">
                  <button type="submit" id="submit" class="btn btn-dark shadow mr-1 mb-1 btn-info">
                    <i class='bx bx-search'></i>&nbsp; Search
                  </button>
                  <button type="button" @click="resetFilters" id="reset" class="btn btn-outline shadow mb-1 btn-danger">
                    <i class='bx bx-reset'></i>&nbsp; Reset
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- List Card -->
        <div class="card shadow-lg" v-if="!showForm">
          <div class="head_title text-dark pl-4 mt-2 ml-3">
            Supplier/Vendor Relationship List
          </div>
          <div class="card-content">
            <div class="d-flex justify-content-end mt-3" style="margin-right: 1rem;">
              <a class="btn btn-info create" @click="showForm = true" href="javascript:void(0)">
                <i class="bx bx-plus-circle"></i>Relationship Entry
              </a>
            </div>

            <div class="card-body card-dashboard">
              <div class="table-responsive">
                <table id="searchResultTable" class="table table-sm datatable mdl-data-table dataTable">
                  <thead>
                    <tr>
                      <th>SL</th>
                      <th>Vendor</th>
                      <th>Organization</th>
                      <th>Review Year</th>
                      <th>Total Score</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in relationships" :key="item.id">
                      <td>{{ index + 1 }}</td>
                      <td>{{ item.vendor_name }}</td>
                      <td>{{ item.organization_name }}</td>
                      <td>{{ item.review_year }}</td>
                      <td>{{ item.total_score }}</td>
                      <td>
                        <div class="btn-group">
                          <button class="btn btn-sm btn-outline-primary" @click="editRelationship(item)">
                            <i class="fas fa-edit"></i>
                          </button>
                          <button class="btn btn-sm btn-outline-danger" @click="deleteRelationship(item)">
                            <i class="fas fa-trash"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <br><br>
            </div>
          </div>
        </div>

        <!-- Form Card -->
        <div class="card" v-if="showForm">
          <div class="head_title">
            Vendor Point Entry Form
          </div>
          <div class="card-body">
            <form @submit.prevent="saveRelationship" id="searchResultPeriodGridList">
              <div class="row">
                <div class="col-12">
              
                    <div class="col-12">
                      <div class="row">
                        <input type="hidden" name="logging_mst_id" id="logging_mst_id" v-model="form.logging_mst_id">
                        
                        <div class="col-md-4">
                          <label class="required text-dark">Date</label>
                          <div class="input-group date" id="datetimepicker2" data-target-input="nearest">
                            <input type="text"
                              v-model="form.requisition_date"
                              class="form-control datetimepicker-input"
                              id="requisition-date"
                              name="requisition-date"
                              autocomplete="off"
                              :readonly="editMode"
                              required
                            />
                          </div>
                          <span class="text-danger"></span>
                        </div>

                        <div class="col-md-4">
                          <label for="vendor_id" class="required text-dark">Select Vendor Name</label>
                          <select required
                            class="custom-select select2 form-control"
                            name="vendor_id"
                            id="vendor_id"
                            v-model="form.vendor_id"
                            @change="handleVendorChange"
                          >
                            <option value="">Select Vendor</option>
                            <option v-for="vendor in vendors" :key="vendor.id" :value="vendor.id">
                              {{ vendor.name }}
                            </option>
                          </select>
                          <span class="text-danger"></span>
                        </div>

                        <div class="col-md-4">
                          <label class="text-dark">Vendor Name</label>
                          <input type="text"
                            v-model="form.vendor_name"
                            class="form-control"
                            id="vendor_name"
                            name="vendor_name"
                            placeholder="Vendor Name" readonly/>
                          <span class="text-danger"></span>
                        </div>
                      </div>

                      <div class="row mt-1">
                        <div class="col-md-4">
                          <label class="text-dark">Vendor Short Name</label>
                          <input type="text"
                            v-model="form.vendor_short_name"
                            class="form-control"
                            id="vendor_short_name"
                            name="vendor_short_name"
                            placeholder="Vendor Short Name" readonly/>
                          <span class="text-danger"></span>
                        </div>
                        <div class="col-md-4">
                          <label class="text-dark">BIN No</label>
                          <input type="text"
                            v-model="form.bin_no"
                            class="form-control"
                            id="nid_no"
                            name="nid_no"
                            placeholder="BIN No" readonly/>
                          <span class="text-danger"></span>
                        </div>

                        <div class="col-md-4">
                          <label class="text-dark">TIN NO</label>
                          <input type="text"
                            v-model="form.tin_no"
                            class="form-control"
                            id="dl_no"
                            name="dl_no"
                            placeholder="TIN NO" readonly />
                          <span class="text-danger"></span>
                        </div>
                      </div>

                      <div class="row mt-1">
                        <div class="col-md-4">
                          <label class="text-dark">Issuing Date</label>
                          <input type="text"
                            v-model="form.dl_issue_date"
                            class="form-control"
                            id="dl_issue_date"
                            name="dl_issue_date"
                            placeholder="Issuing Date" readonly
                          />
                          <span class="text-danger"></span>
                        </div>

                        <div class="col-md-4">
                          <label class="text-dark">VAT Registration No</label>
                          <input type="text"
                            v-model="form.vat_registration_no"
                            class="form-control"
                            id="vat_registration_no"
                            name="vat_registration_no"
                            placeholder="VAT Registration No" readonly/>
                          <span class="text-danger"></span>
                        </div>

                        <div class="col-md-4">
                          <label class="text-dark">Contact Person Name</label>
                          <input type="text"
                            v-model="form.contact_person_name"
                            class="form-control"
                            id="contact_person_name"
                            name="contact_person_name"
                            placeholder="Contact Person Name" readonly/>
                          <span class="text-danger"></span>
                        </div>
                      </div>

                      <div class="row mt-1">
                        <div class="col-md-4">
                          <label class="text-dark">Contact Person Phone</label>
                          <input type="text"
                            v-model="form.contact_person_phone"
                            class="form-control"
                            id="contact_person_phone"
                            name="contact_person_phone"
                            placeholder="Contact Person Phone" readonly/>
                          <span class="text-danger"></span>
                        </div>
                        <div class="col-md-4">
                          <label class="text-dark">Organization</label>
                          <select name="organisation_name" id="organisation_name" class="form-control select2" required v-model="form.organisation_name">
                            <option value="">Select Organization</option>
                            <option v-for="org in organizations" :key="org" :value="org">
                              {{ org }}
                            </option>
                          </select>
                          <span class="text-danger"></span>
                        </div>

                        <div class="col-md-4">
                          <label class="required text-dark">Review Period</label>
                          <select name="financial_year" id="financial_year" class="form-control select2" required v-model="form.financial_year">
                            <option value="">Select Review Period</option>
                            <option v-for="year in financialYears" :key="year" :value="year">
                              {{ year }}
                            </option>
                          </select>
                          <span class="text-danger"></span>
                        </div>
                      </div>
                    </div>
                    <br>
                </div>

                <div class="col-12">
                  <fieldset class="border col-sm-12">
                    <legend class="w-auto required">Point</legend>
                    <div class="row">
                      <div class="table-responsive" id="table_supp_stuff">
                        <table class="table table-sm table-striped table-bordered" id="pointTable">
                          <thead class="table-primary">
                            <tr>
                              <th>Point Area</th>
                              <th class="text-center required">Individual Score (1-9)</th>
                              <th>Remarks</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="(point, index) in pointList" :key="point.point_id">
                              <td>{{ point.point_name }} (1-{{ point.point_score }})</td>
                              <td>
                                <input type="number" class="form-control text-center score tRow" 
                                  v-model="form.individual_scores[point.point_name]"
                                  @input="calculateTotal"
                                  required placeholder="0" pattern="[0-9]" maxlength="9"
                                  :data-index="index"
                                />
                                <input type="hidden" class="form-control text-center" 
                                  :value="point.point_id"
                                />
                              </td>
                              <td>
                                <input type="text" class="form-control" 
                                  v-model="form.individual_remarks[point.point_name]"
                                  placeholder="Enter Remarks"
                                />
                              </td>
                            </tr>
                            <tr class="table-primary">
                              <td>Total</td>
                              <td><input type="number" class="form-control text-center" name="individual_total" id="individual_total" readonly v-model="form.total_score" placeholder="0"></td>
                              <td><input type="text" class="form-control" name="individual_total_dup" id="individual_total_dup" readonly></td>
                            </tr>
                            <tr class="table-primary">
                              <td>Observation/Comments</td>
                              <td colspan="2">
                                <input type="text" class="form-control" name="observation" id="observation" 
                                  v-model="form.observation" placeholder="" required
                                />
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <br>
                  </fieldset>
                </div>

                <div class="col-md-12 d-flex justify-content-end mt-3" id="add" style="text-align: right;">
                  <br>
                  <button type="button" @click="cancelForm" class="btn btn-danger mb-1 me-2">
                    <i class="bx bx-reset"></i> Cancel
                  </button>
                  <button type="submit" id="submit" name="save" class="btn btn-success shadow mb-1 btn-primary" :disabled="loading">
                    <i class="bx bx-save"></i> {{ editMode ? 'Update' : 'Submit' }}
                  </button>
                </div>
              </div>
            </form>
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
const showForm = ref(false)
const editMode = ref(false)
const relationships = ref([])
const vendors = ref([])
const pointList = ref([])
const organizations = ref(['Organization A', 'Organization B', 'Organization C'])

const filters = reactive({
  department_id: ''
})

const form = reactive({
  logging_mst_id: '',
  requisition_date: '',
  vendor_id: '',
  vendor_name: '',
  vendor_short_name: '',
  bin_no: '',
  tin_no: '',
  dl_issue_date: '',
  vat_registration_no: '',
  contact_person_name: '',
  contact_person_phone: '',
  organisation_name: '',
  financial_year: '',
  individual_scores: {},
  individual_remarks: {},
  total_score: '',
  observation: ''
})

// Generate financial years from 2022 to current year
const financialYears = computed(() => {
  const years = []
  const currentYear = new Date().getFullYear()
  for (let year = 2022; year <= currentYear; year++) {
    years.push(`${year}-${year + 1}`)
  }
  return years
})

// Load relationships
const loadRelationships = async () => {
  try {
    const response = await fetch('/api/setup/datatable-points')
    const data = await response.json()
    relationships.value = data.data || []
  } catch (error) {
    console.error('Failed to load relationships:', error)
    // Fallback data
    relationships.value = [
      {
        id: 1,
        vendor_name: 'Vendor A',
        organization_name: 'Organization A',
        review_year: '2024-2025',
        total_score: 8.5
      },
      {
        id: 2,
        vendor_name: 'Vendor B',
        organization_name: 'Organization B',
        review_year: '2024-2025',
        total_score: 7.2
      }
    ]
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
    // Fallback data
    vendors.value = [
      { id: 1, name: 'Vendor A' },
      { id: 2, name: 'Vendor B' },
      { id: 3, name: 'Vendor C' }
    ]
  }
}

// Load point list
const loadPointList = async () => {
  try {
    const response = await fetch('/api/setup/point-areas')
    const data = await response.json()
    pointList.value = data.data || []
  } catch (error) {
    console.error('Failed to load point list:', error)
    // Fallback data
    pointList.value = [
      { point_id: 1, point_name: 'Quality', point_score: 9 },
      { point_id: 2, point_name: 'Price', point_score: 9 },
      { point_id: 3, point_name: 'Delivery', point_score: 9 }
    ]
  }
}

// Handle vendor change - auto-populate vendor details
const handleVendorChange = async () => {
  if (!form.vendor_id) {
    // Clear vendor details
    form.vendor_name = ''
    form.vendor_short_name = ''
    form.bin_no = ''
    form.tin_no = ''
    form.dl_issue_date = ''
    form.vat_registration_no = ''
    form.contact_person_name = ''
    form.contact_person_phone = ''
    return
  }

  try {
    const response = await fetch(`/api/setup/vendor-info/${form.vendor_id}`)
    const data = await response.json()
    
    if (data) {
      form.vendor_name = data.vendor_name || ''
      form.vendor_short_name = data.vendor_short_name || ''
      form.bin_no = data.bin_no || ''
      form.tin_no = data.tin_no || ''
      form.dl_issue_date = data.insert_date || ''
      form.vat_registration_no = data.vat_registration_no || ''
      form.contact_person_name = data.contact_person_name || ''
      form.contact_person_phone = data.contact_person_mobile || ''
    }
  } catch (error) {
    console.error('Failed to load vendor info:', error)
  }
}

// Calculate total score
const calculateTotal = () => {
  const scores = Object.values(form.individual_scores).filter(val => val !== '' && val !== null && val !== undefined)
  const total = scores.reduce((sum, val) => sum + parseFloat(val || 0), 0)
  const avg = scores.length > 0 ? (total / scores.length).toFixed(2) : 0
  form.total_score = avg
}

// Search relationships
const searchRelationships = () => {
  loadRelationships()
}

// Reset filters
const resetFilters = () => {
  filters.department_id = ''
  loadRelationships()
}

// Cancel form
const cancelForm = () => {
  showForm.value = false
  editMode.value = false
  resetFormData()
}

// Reset form data
const resetFormData = () => {
  form.logging_mst_id = ''
  form.requisition_date = ''
  form.vendor_id = ''
  form.vendor_name = ''
  form.vendor_short_name = ''
  form.bin_no = ''
  form.tin_no = ''
  form.dl_issue_date = ''
  form.vat_registration_no = ''
  form.contact_person_name = ''
  form.contact_person_phone = ''
  form.organisation_name = ''
  form.financial_year = ''
  form.individual_scores = {}
  form.individual_remarks = {}
  form.total_score = ''
  form.observation = ''
}

// Save relationship
const saveRelationship = async () => {
  if (!form.requisition_date || !form.vendor_id || !form.organisation_name || !form.financial_year) {
    ElMessage.error('Please fill in all required fields')
    return
  }

  loading.value = true

  try {
    const url = editMode.value ? '/api/setup/edit-vendor-points' : '/api/setup/create-points'
    const method = 'POST'

    const response = await fetch(url, {
      method: method,
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(form)
    })

    const data = await response.json()

    if (data.success) {
      ElMessage.success(`Vendor relationship ${editMode.value ? 'updated' : 'created'} successfully!`)
      showForm.value = false
      resetFormData()
      loadRelationships()
    } else {
      ElMessage.error(data.message || 'Failed to save vendor relationship')
    }
  } catch (error) {
    console.error('Save error:', error)
    ElMessage.error('Failed to save vendor relationship')
  } finally {
    loading.value = false
  }
}

// Edit relationship
const editRelationship = (item) => {
  editMode.value = true
  showForm.value = true
  
  // Populate form with item data
  form.requisition_date = item.requisition_date || ''
  form.vendor_id = item.vendor_id || ''
  form.vendor_name = item.vendor_name || ''
  form.organisation_name = item.organization_name || ''
  form.financial_year = item.review_year || ''
  form.total_score = item.total_score || ''
  form.observation = item.observation || ''
  
  // Load vendor details if vendor_id is present
  if (form.vendor_id) {
    handleVendorChange()
  }
}

// Delete relationship
const deleteRelationship = async (item) => {
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

    const response = await fetch('/api/setup/points-delete', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        id: item.id
      })
    })

    const data = await response.json()

    if (data.success) {
      ElMessage.success('Deleted!')
      loadRelationships()
    } else {
      ElMessage.error(data.message || 'Failed to delete vendor relationship')
    }
  } catch (error) {
    if (error !== 'cancel') {
      console.error('Delete error:', error)
      ElMessage.error('Failed to delete vendor relationship')
    }
  }
}

// Set default date
const setDefaultDate = () => {
  const today = new Date()
  const day = String(today.getDate()).padStart(2, '0')
  const month = String(today.getMonth() + 1).padStart(2, '0')
  const year = today.getFullYear()
  form.requisition_date = `${day}-${month}-${year}`
}

onMounted(async () => {
  setDefaultDate()
  await loadRelationships()
  await loadVendors()
  await loadPointList()
})
</script>

<style scoped>
.vendor-relationships {
  padding: 0;
}

.page-header {
  margin-bottom: 24px;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-content h1 {
  margin: 0;
  color: #303133;
}

.filters {
  margin-bottom: 20px;
  padding: 16px;
  background: #f5f7fa;
  border-radius: 4px;
}
</style>
