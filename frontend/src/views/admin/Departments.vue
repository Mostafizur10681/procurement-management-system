<template>
  <div class="departments">
    <div class="page-header">
      <div class="header-content">
        <h1>Departments</h1>
        <el-button type="primary" @click="showCreateDialog = true">
          <el-icon><Plus /></el-icon>
          Add Department
        </el-button>
      </div>
    </div>

    <el-card>
      <el-table :data="departments" v-loading="loading" style="width: 100%">
        <el-table-column prop="name" label="Department Name" min-width="200" />
        <el-table-column prop="code" label="Code" width="120" />
        <el-table-column prop="head_of_department" label="Head of Department" width="180" />
        <el-table-column prop="budget_limit" label="Budget Limit" width="120" align="right">
          <template #default="scope">
            ${{ formatCurrency(scope.row.budget_limit) }}
          </template>
        </el-table-column>
        <el-table-column prop="is_active" label="Status" width="100">
          <template #default="scope">
            <el-tag :type="scope.row.is_active ? 'success' : 'danger'">
              {{ scope.row.is_active ? 'Active' : 'Inactive' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="created_at" label="Created" width="120">
          <template #default="scope">
            {{ formatDate(scope.row.created_at) }}
          </template>
        </el-table-column>
        <el-table-column label="Actions" width="150" fixed="right">
          <template #default="scope">
            <el-button-group>
              <el-button size="small" @click="editDepartment(scope.row)">
                <el-icon><Edit /></el-icon>
              </el-button>
              <el-button
                size="small"
                type="danger"
                @click="deleteDepartment(scope.row)"
              >
                <el-icon><Delete /></el-icon>
              </el-button>
            </el-button-group>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- Create/Edit Dialog -->
    <el-dialog
      v-model="showCreateDialog"
      :title="editingDepartment ? 'Edit Department' : 'Create Department'"
      width="600px"
    >
      <el-form
        ref="formRef"
        :model="form"
        :rules="rules"
        label-width="140px"
      >
        <el-form-item label="Department Name" prop="name">
          <el-input v-model="form.name" placeholder="Enter department name" />
        </el-form-item>
        
        <el-form-item label="Code" prop="code">
          <el-input v-model="form.code" placeholder="Enter department code" />
        </el-form-item>
        
        <el-form-item label="Head of Department">
          <el-input v-model="form.head_of_department" placeholder="Enter head of department name" />
        </el-form-item>
        
        <el-form-item label="Budget Limit" prop="budget_limit">
          <el-input-number
            v-model="form.budget_limit"
            :min="0"
            :precision="2"
            style="width: 100%"
            placeholder="Enter budget limit"
          />
        </el-form-item>
        
        <el-form-item label="Description">
          <el-input
            v-model="form.description"
            type="textarea"
            :rows="3"
            placeholder="Enter department description"
          />
        </el-form-item>
        
        <el-form-item label="Status">
          <el-switch
            v-model="form.is_active"
            active-text="Active"
            inactive-text="Inactive"
          />
        </el-form-item>
      </el-form>
      
      <template #footer>
        <el-button @click="showCreateDialog = false">Cancel</el-button>
        <el-button type="primary" @click="handleSubmit" :loading="loading">
          {{ editingDepartment ? 'Update' : 'Create' }}
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import procurementService from '@/services/procurement'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, Edit, Delete } from '@element-plus/icons-vue'

const loading = ref(false)
const departments = ref([])
const showCreateDialog = ref(false)
const editingDepartment = ref(null)
const formRef = ref()

const form = reactive({
  name: '',
  code: '',
  head_of_department: '',
  budget_limit: 0,
  description: '',
  is_active: true
})

const rules = {
  name: [
    { required: true, message: 'Department name is required', trigger: 'blur' },
    { min: 2, message: 'Name must be at least 2 characters', trigger: 'blur' }
  ],
  code: [
    { required: true, message: 'Department code is required', trigger: 'blur' },
    { min: 2, message: 'Code must be at least 2 characters', trigger: 'blur' }
  ],
  budget_limit: [
    { type: 'number', min: 0, message: 'Budget limit must be positive', trigger: 'blur' }
  ]
}

const formatCurrency = (value) => {
  return value ? value.toLocaleString() : '0'
}

const formatDate = (date) => {
  return date ? new Date(date).toLocaleDateString() : ''
}

const loadDepartments = async () => {
  loading.value = true
  try {
    const response = await procurementService.getDepartments()
    departments.value = response
  } catch (error) {
    ElMessage.error('Failed to load departments')
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  form.name = ''
  form.code = ''
  form.head_of_department = ''
  form.budget_limit = 0
  form.description = ''
  form.is_active = true
  editingDepartment.value = null
}

const editDepartment = (department) => {
  editingDepartment.value = department
  form.name = department.name
  form.code = department.code
  form.head_of_department = department.head_of_department || ''
  form.budget_limit = department.budget_limit
  form.description = department.description || ''
  form.is_active = department.is_active
  showCreateDialog.value = true
}

const handleSubmit = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()
    loading.value = true

    if (editingDepartment.value) {
      await procurementService.updateDepartment(editingDepartment.value.id, form)
      ElMessage.success('Department updated successfully')
    } else {
      await procurementService.createDepartment(form)
      ElMessage.success('Department created successfully')
    }

    showCreateDialog.value = false
    resetForm()
    loadDepartments()
  } catch (error) {
    if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      ElMessage.error(errors[0])
    } else {
      ElMessage.error('Failed to save department')
    }
  } finally {
    loading.value = false
  }
}

const deleteDepartment = async (department) => {
  try {
    await ElMessageBox.confirm(
      `Are you sure you want to delete department "${department.name}"?`,
      'Confirm Deletion',
      {
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        type: 'warning'
      }
    )
    
    await procurementService.deleteDepartment(department.id)
    ElMessage.success('Department deleted successfully')
    loadDepartments()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('Failed to delete department')
    }
  }
}

onMounted(() => {
  loadDepartments()
})
</script>

<style scoped>
.departments {
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
</style>
