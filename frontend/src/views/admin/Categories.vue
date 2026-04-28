<template>
  <div class="categories">
    <div class="page-header">
      <div class="header-content">
        <h1>Categories</h1>
        <el-button type="primary" @click="showCreateDialog = true">
          <el-icon><Plus /></el-icon>
          Add Category
        </el-button>
      </div>
    </div>

    <el-card>
      <el-table :data="categories" v-loading="loading" style="width: 100%">
        <el-table-column prop="name" label="Category Name" min-width="200" />
        <el-table-column prop="code" label="Code" width="120" />
        <el-table-column prop="description" label="Description" min-width="250" />
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
              <el-button size="small" @click="editCategory(scope.row)">
                <el-icon><Edit /></el-icon>
              </el-button>
              <el-button
                size="small"
                type="danger"
                @click="deleteCategory(scope.row)"
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
      :title="editingCategory ? 'Edit Category' : 'Create Category'"
      width="500px"
    >
      <el-form
        ref="formRef"
        :model="form"
        :rules="rules"
        label-width="100px"
      >
        <el-form-item label="Name" prop="name">
          <el-input v-model="form.name" placeholder="Enter category name" />
        </el-form-item>
        
        <el-form-item label="Code" prop="code">
          <el-input v-model="form.code" placeholder="Enter category code" />
        </el-form-item>
        
        <el-form-item label="Description">
          <el-input
            v-model="form.description"
            type="textarea"
            :rows="3"
            placeholder="Enter category description"
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
          {{ editingCategory ? 'Update' : 'Create' }}
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
const categories = ref([])
const showCreateDialog = ref(false)
const editingCategory = ref(null)
const formRef = ref()

const form = reactive({
  name: '',
  code: '',
  description: '',
  is_active: true
})

const rules = {
  name: [
    { required: true, message: 'Category name is required', trigger: 'blur' },
    { min: 2, message: 'Name must be at least 2 characters', trigger: 'blur' }
  ],
  code: [
    { required: true, message: 'Category code is required', trigger: 'blur' },
    { min: 2, message: 'Code must be at least 2 characters', trigger: 'blur' }
  ]
}

const formatDate = (date) => {
  return date ? new Date(date).toLocaleDateString() : ''
}

const loadCategories = async () => {
  loading.value = true
  try {
    const response = await procurementService.getCategories()
    categories.value = response
  } catch (error) {
    ElMessage.error('Failed to load categories')
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  form.name = ''
  form.code = ''
  form.description = ''
  form.is_active = true
  editingCategory.value = null
}

const editCategory = (category) => {
  editingCategory.value = category
  form.name = category.name
  form.code = category.code
  form.description = category.description || ''
  form.is_active = category.is_active
  showCreateDialog.value = true
}

const handleSubmit = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()
    loading.value = true

    if (editingCategory.value) {
      await procurementService.updateCategory(editingCategory.value.id, form)
      ElMessage.success('Category updated successfully')
    } else {
      await procurementService.createCategory(form)
      ElMessage.success('Category created successfully')
    }

    showCreateDialog.value = false
    resetForm()
    loadCategories()
  } catch (error) {
    if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      ElMessage.error(errors[0])
    } else {
      ElMessage.error('Failed to save category')
    }
  } finally {
    loading.value = false
  }
}

const deleteCategory = async (category) => {
  try {
    await ElMessageBox.confirm(
      `Are you sure you want to delete category "${category.name}"?`,
      'Confirm Deletion',
      {
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        type: 'warning'
      }
    )
    
    await procurementService.deleteCategory(category.id)
    ElMessage.success('Category deleted successfully')
    loadCategories()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('Failed to delete category')
    }
  }
}

onMounted(() => {
  loadCategories()
})
</script>

<style scoped>
.categories {
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
