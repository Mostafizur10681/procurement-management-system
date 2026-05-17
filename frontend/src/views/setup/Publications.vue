<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <!-- Alert Message -->
        <div v-if="message" class="alert" :class="messageClass" role="alert">
          {{ message }}
          <button type="button" class="close" @click="clearMessage" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <!-- Form Card -->
        <div class="card shadow-lg mb-4">
          <div class="head_title font-size-large">Media Entry</div>
          <div class="card-body">
            <form @submit.prevent="savePublication" id="advertisement_form">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="required text-dark">Media Name</label>
                    <input type="text"
                      name="advertisement_name"
                      id="advertisement_name"
                      v-model="form.advertisement_name"
                      autocomplete="off"
                      class="form-control"
                      required
                    />
                  </div>
                  <span class="text-danger"></span>
                </div>
                <div class="col-md-4">
                  <label class="required text-dark">Media Name Bangla</label>
                  <div class="input-group">
                    <input type="text"
                      class="form-control"
                      id="advertisement_name_bn"
                      v-model="form.advertisement_name_bn"
                      name="advertisement_name_bn"
                      autocomplete="off"
                      @input="validateBanglaInput"
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

              <div class="modal-footer justify-content-end">
                <button class="btn btn-primary" type="submit" :disabled="loading">
                  <i class="bx bx-save"></i> {{ editMode ? 'Update' : 'Save' }}
                </button>
                <button class="btn btn-danger" type="button" @click="resetForm">
                  <i class="bx bx-reset"></i> Reset
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- List Card -->
        <div class="col-12">
          <div class="card shadow-lg">
            <div class="card-body">
              <div class="table-responsive">
                <table id="advertisement_list" class="table table-sm datatable mdl-data-table dataTable">
                  <thead>
                    <tr>
                      <th>SL</th>
                      <th>Media Name</th>
                      <th>Media Name Bangla</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in publications" :key="item.id">
                      <td>{{ index + 1 }}</td>
                      <td>{{ item.advertise_name }}</td>
                      <td>{{ item.advertise_bn }}</td>
                      <td>
                        <span class="badge" :class="item.status === 'Y' ? 'badge-success' : 'badge-danger'">
                          {{ item.status === 'Y' ? 'Active' : 'In-Active' }}
                        </span>
                      </td>
                      <td>
                        <div class="btn-group">
                          <button class="btn btn-sm btn-outline-primary" @click="editPublication(item)">
                            <i class="fas fa-edit"></i>
                          </button>
                          <button class="btn btn-sm btn-outline-danger" @click="deletePublication(item)">
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
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'

const loading = ref(false)
const editMode = ref(false)
const message = ref('')
const messageClass = ref('alert-danger')
const publications = ref([])

const form = reactive({
  advertisement_name: '',
  advertisement_name_bn: '',
  active_status: 'Y'
})

// Bangla input validation
const validateBanglaInput = (event) => {
  const banglaRegex = /^[\u0980-\u09FF\s]+$/
  const inputValue = event.target.value
  if (!banglaRegex.test(inputValue)) {
    event.target.value = inputValue.replace(/[^\u0980-\u09FF\s]/g, '')
    form.advertisement_name_bn = event.target.value
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

// Load publications
const loadPublications = async () => {
  try {
    const response = await fetch('/api/setup/advertisement-datatable-list')
    const data = await response.json()
    publications.value = data.data || []
  } catch (error) {
    console.error('Failed to load publications:', error)
    // Fallback data
    publications.value = [
      {
        id: 1,
        advertise_name: 'TV Channel',
        advertise_bn: 'টিভি চ্যানেল',
        status: 'Y'
      },
      {
        id: 2,
        advertise_name: 'Radio',
        advertise_bn: 'রেডিও',
        status: 'N'
      }
    ]
  }
}

// Save publication
const savePublication = async () => {
  if (!form.advertisement_name || !form.advertisement_name_bn) {
    showMessage('Media Name and Media Name Bangla are required', 'error')
    return
  }

  loading.value = true

  try {
    const url = editMode.value ? '/api/setup/advertisement-update' : '/api/setup/advertisement-post'
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
      showMessage(`Publication ${editMode.value ? 'updated' : 'created'} successfully!`)
      resetForm()
      loadPublications()
    } else {
      showMessage(data.message || 'Failed to save publication', 'error')
    }
  } catch (error) {
    console.error('Save error:', error)
    showMessage('Failed to save publication', 'error')
  } finally {
    loading.value = false
  }
}

// Edit publication
const editPublication = (item) => {
  editMode.value = true
  form.advertisement_name = item.advertise_name || ''
  form.advertisement_name_bn = item.advertise_bn || ''
  form.active_status = item.status || 'Y'
}

// Delete publication
const deletePublication = async (item) => {
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

    const response = await fetch('/api/setup/advertisement-delete', {
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
      showMessage('Deleted!')
      loadPublications()
    } else {
      showMessage(data.message || 'Failed to delete publication', 'error')
    }
  } catch (error) {
    if (error !== 'cancel') {
      console.error('Delete error:', error)
      showMessage('Failed to delete publication', 'error')
    }
  }
}

// Reset form
const resetForm = () => {
  form.advertisement_name = ''
  form.advertisement_name_bn = ''
  form.active_status = 'Y'
  editMode.value = false
}

onMounted(() => {
  loadPublications()
})
</script>

<style scoped>
.badge-success {
  background-color: #28a745;
  color: white;
}

.badge-danger {
  background-color: #dc3545;
  color: white;
}
</style>
