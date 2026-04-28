<template>
  <div class="publications">
    <div class="page-header">
      <div class="header-content">
        <h1>Publications</h1>
        <el-button type="primary" @click="showCreateDialog = true">
          <el-icon><Plus /></el-icon>
          Create Publication
        </el-button>
      </div>
    </div>

    <el-card>
      <div class="filters">
        <el-form :model="filters" inline>
          <el-form-item label="Type">
            <el-select v-model="filters.type" placeholder="All Types" clearable>
              <el-option label="Tender" value="tender" />
              <el-option label="Notice" value="notice" />
              <el-option label="Announcement" value="announcement" />
            </el-select>
          </el-form-item>
          <el-form-item label="Status">
            <el-select v-model="filters.status" placeholder="All Status" clearable>
              <el-option label="Draft" value="draft" />
              <el-option label="Published" value="published" />
              <el-option label="Closed" value="closed" />
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click="loadPublications">
              <el-icon><Search /></el-icon>
              Search
            </el-button>
          </el-form-item>
        </el-form>
      </div>

      <el-table :data="publications" v-loading="loading" style="width: 100%">
        <el-table-column prop="title" label="Title" min-width="250" />
        <el-table-column prop="publication_type" label="Type" width="120">
          <template #default="scope">
            <el-tag :type="getTypeColor(scope.row.publication_type)">
              {{ scope.row.publication_type }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="publication_date" label="Publication Date" width="140" />
        <el-table-column prop="closing_date" label="Closing Date" width="140" />
        <el-table-column prop="estimated_value" label="Est. Value" width="120">
          <template #default="scope">
            ${{ formatCurrency(scope.row.estimated_value) }}
          </template>
        </el-table-column>
        <el-table-column prop="status" label="Status" width="100">
          <template #default="scope">
            <el-tag :type="getStatusColor(scope.row.status)">
              {{ scope.row.status }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="Actions" width="200" fixed="right">
          <template #default="scope">
            <el-button-group>
              <el-button size="small" @click="editPublication(scope.row)">
                <el-icon><Edit /></el-icon>
              </el-button>
              <el-button
                size="small"
                type="success"
                @click="publishPublication(scope.row)"
                v-if="scope.row.status === 'draft'"
              >
                <el-icon><Promotion /></el-icon>
              </el-button>
              <el-button
                size="small"
                type="warning"
                @click="closePublication(scope.row)"
                v-if="scope.row.status === 'published'"
              >
                <el-icon><Close /></el-icon>
              </el-button>
              <el-button
                size="small"
                type="danger"
                @click="deletePublication(scope.row)"
              >
                <el-icon><Delete /></el-icon>
              </el-button>
            </el-button-group>
          </template>
        </el-table-column>
      </el-table>

      <div class="pagination">
        <el-pagination
          v-model:current-page="pagination.page"
          v-model:page-size="pagination.size"
          :total="pagination.total"
          :page-sizes="[10, 20, 50, 100]"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="loadPublications"
          @current-change="loadPublications"
        />
      </div>
    </el-card>

    <!-- Create/Edit Dialog -->
    <el-dialog
      v-model="showCreateDialog"
      :title="editingPublication ? 'Edit Publication' : 'Create Publication'"
      width="700px"
    >
      <el-form
        ref="formRef"
        :model="form"
        :rules="rules"
        label-width="140px"
      >
        <el-form-item label="Title" prop="title">
          <el-input v-model="form.title" placeholder="Enter publication title" />
        </el-form-item>
        
        <el-form-item label="Type" prop="publication_type">
          <el-select v-model="form.publication_type" placeholder="Select type">
            <el-option label="Tender" value="tender" />
            <el-option label="Notice" value="notice" />
            <el-option label="Announcement" value="announcement" />
          </el-select>
        </el-form-item>
        
        <el-form-item label="Description">
          <el-input
            v-model="form.description"
            type="textarea"
            :rows="4"
            placeholder="Enter publication description"
          />
        </el-form-item>
        
        <el-form-item label="Publication Date" prop="publication_date">
          <el-date-picker
            v-model="form.publication_date"
            type="date"
            placeholder="Select publication date"
            style="width: 100%"
          />
        </el-form-item>
        
        <el-form-item label="Closing Date">
          <el-date-picker
            v-model="form.closing_date"
            type="date"
            placeholder="Select closing date"
            style="width: 100%"
          />
        </el-form-item>
        
        <el-form-item label="Estimated Value">
          <el-input-number
            v-model="form.estimated_value"
            :min="0"
            :precision="2"
            placeholder="Enter estimated value"
            style="width: 100%"
          />
        </el-form-item>
        
        <el-form-item label="Committee">
          <el-select v-model="form.committee_id" placeholder="Select committee" clearable>
            <el-option
              v-for="committee in committees"
              :key="committee.id"
              :label="committee.name"
              :value="committee.id"
            />
          </el-select>
        </el-form-item>
        
        <el-form-item label="Status">
          <el-select v-model="form.status" placeholder="Select status">
            <el-option label="Draft" value="draft" />
            <el-option label="Published" value="published" />
            <el-option label="Closed" value="closed" />
          </el-select>
        </el-form-item>
      </el-form>
      
      <template #footer>
        <el-button @click="showCreateDialog = false">Cancel</el-button>
        <el-button type="primary" @click="handleSubmit" :loading="loading">
          {{ editingPublication ? 'Update' : 'Create' }}
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, Edit, Delete, Search, Promotion, Close } from '@element-plus/icons-vue'

const loading = ref(false)
const publications = ref([])
const committees = ref([])
const showCreateDialog = ref(false)
const editingPublication = ref(null)
const formRef = ref()

const filters = reactive({
  type: '',
  status: ''
})

const pagination = reactive({
  page: 1,
  size: 20,
  total: 0
})

const form = reactive({
  title: '',
  description: '',
  publication_type: '',
  publication_date: '',
  closing_date: '',
  estimated_value: null,
  committee_id: null,
  status: 'draft'
})

const rules = {
  title: [
    { required: true, message: 'Publication title is required', trigger: 'blur' },
    { min: 2, message: 'Title must be at least 2 characters', trigger: 'blur' }
  ],
  publication_type: [
    { required: true, message: 'Publication type is required', trigger: 'change' }
  ],
  publication_date: [
    { required: true, message: 'Publication date is required', trigger: 'change' }
  ]
}

const loadPublications = async () => {
  loading.value = true
  try {
    // Mock data for now - replace with actual API call
    publications.value = [
      {
        id: 1,
        title: 'IT Equipment Procurement',
        publication_type: 'tender',
        description: 'Procurement of IT equipment for various departments',
        publication_date: '2024-01-15',
        closing_date: '2024-02-15',
        estimated_value: 50000,
        status: 'published'
      },
      {
        id: 2,
        title: 'Office Supplies Notice',
        publication_type: 'notice',
        description: 'Annual office supplies procurement notice',
        publication_date: '2024-01-20',
        closing_date: '2024-01-30',
        estimated_value: 15000,
        status: 'draft'
      }
    ]
    pagination.total = publications.value.length
  } catch (error) {
    ElMessage.error('Failed to load publications')
  } finally {
    loading.value = false
  }
}

const loadCommittees = async () => {
  try {
    // Mock data for now - replace with actual API call
    committees.value = [
      { id: 1, name: 'Procurement Committee' },
      { id: 2, name: 'Technical Committee' }
    ]
  } catch (error) {
    ElMessage.error('Failed to load committees')
  }
}

const resetForm = () => {
  form.title = ''
  form.description = ''
  form.publication_type = ''
  form.publication_date = ''
  form.closing_date = ''
  form.estimated_value = null
  form.committee_id = null
  form.status = 'draft'
  editingPublication.value = null
}

const editPublication = (publication) => {
  editingPublication.value = publication
  form.title = publication.title
  form.description = publication.description || ''
  form.publication_type = publication.publication_type
  form.publication_date = publication.publication_date
  form.closing_date = publication.closing_date
  form.estimated_value = publication.estimated_value
  form.committee_id = publication.committee_id
  form.status = publication.status
  showCreateDialog.value = true
}

const handleSubmit = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()
    loading.value = true

    // Mock API call - replace with actual implementation
    ElMessage.success('Publication saved successfully')
    showCreateDialog.value = false
    resetForm()
    loadPublications()
  } catch (error) {
    ElMessage.error('Failed to save publication')
  } finally {
    loading.value = false
  }
}

const publishPublication = async (publication) => {
  try {
    await ElMessageBox.confirm(
      `Are you sure you want to publish "${publication.title}"?`,
      'Confirm Publication',
      {
        confirmButtonText: 'Publish',
        cancelButtonText: 'Cancel',
        type: 'info'
      }
    )
    
    // Mock API call - replace with actual implementation
    ElMessage.success('Publication published successfully')
    loadPublications()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('Failed to publish publication')
    }
  }
}

const closePublication = async (publication) => {
  try {
    await ElMessageBox.confirm(
      `Are you sure you want to close "${publication.title}"?`,
      'Confirm Closure',
      {
        confirmButtonText: 'Close',
        cancelButtonText: 'Cancel',
        type: 'warning'
      }
    )
    
    // Mock API call - replace with actual implementation
    ElMessage.success('Publication closed successfully')
    loadPublications()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('Failed to close publication')
    }
  }
}

const deletePublication = async (publication) => {
  try {
    await ElMessageBox.confirm(
      `Are you sure you want to delete publication "${publication.title}"?`,
      'Confirm Deletion',
      {
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        type: 'warning'
      }
    )
    
    // Mock API call - replace with actual implementation
    ElMessage.success('Publication deleted successfully')
    loadPublications()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('Failed to delete publication')
    }
  }
}

const formatCurrency = (value) => {
  return value ? value.toLocaleString() : '0'
}

const getTypeColor = (type) => {
  const colors = {
    tender: 'primary',
    notice: 'warning',
    announcement: 'success'
  }
  return colors[type] || 'info'
}

const getStatusColor = (status) => {
  const colors = {
    draft: 'info',
    published: 'success',
    closed: 'danger'
  }
  return colors[status] || 'info'
}

onMounted(() => {
  loadPublications()
  loadCommittees()
})
</script>

<style scoped>
.publications {
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

.pagination {
  margin-top: 20px;
  text-align: right;
}
</style>
