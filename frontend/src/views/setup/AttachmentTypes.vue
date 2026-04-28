<template>
  <div class="attachment-types">
    <div class="page-header">
      <div class="header-content">
        <h1>Attachment Types</h1>
        <el-button type="primary" @click="showCreateDialog = true">
          <el-icon><Plus /></el-icon>
          Create Attachment Type
        </el-button>
      </div>
    </div>

    <el-card>
      <el-table :data="attachmentTypes" v-loading="loading" style="width: 100%">
        <el-table-column prop="name" label="Type Name" min-width="200" />
        <el-table-column prop="code" label="Code" width="120" />
        <el-table-column prop="allowed_extensions" label="Allowed Extensions" width="180" />
        <el-table-column prop="max_file_size" label="Max Size" width="100">
          <template #default="scope">
            {{ scope.row.max_file_size ? `${scope.row.max_file_size}KB` : 'Unlimited' }}
          </template>
        </el-table-column>
        <el-table-column prop="is_required" label="Required" width="100">
          <template #default="scope">
            <el-tag :type="scope.row.is_required ? 'warning' : 'info'">
              {{ scope.row.is_required ? 'Yes' : 'No' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="is_active" label="Status" width="100">
          <template #default="scope">
            <el-tag :type="scope.row.is_active ? 'success' : 'danger'">
              {{ scope.row.is_active ? 'Active' : 'Inactive' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="Actions" width="150" fixed="right">
          <template #default="scope">
            <el-button-group>
              <el-button size="small" @click="editAttachmentType(scope.row)">
                <el-icon><Edit /></el-icon>
              </el-button>
              <el-button
                size="small"
                type="danger"
                @click="deleteAttachmentType(scope.row)"
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
      :title="editingAttachmentType ? 'Edit Attachment Type' : 'Create Attachment Type'"
      width="600px"
    >
      <el-form
        ref="formRef"
        :model="form"
        :rules="rules"
        label-width="140px"
      >
        <el-form-item label="Type Name" prop="name">
          <el-input v-model="form.name" placeholder="Enter attachment type name" />
        </el-form-item>
        
        <el-form-item label="Code" prop="code">
          <el-input v-model="form.code" placeholder="Enter unique code" />
        </el-form-item>
        
        <el-form-item label="Description">
          <el-input
            v-model="form.description"
            type="textarea"
            :rows="3"
            placeholder="Enter description"
          />
        </el-form-item>
        
        <el-form-item label="Allowed Extensions">
          <el-input
            v-model="form.allowed_extensions"
            placeholder="e.g., pdf,doc,docx,xls,xlsx"
          />
          <div class="form-help">Comma-separated list without dots</div>
        </el-form-item>
        
        <el-form-item label="Max File Size (KB)">
          <el-input-number
            v-model="form.max_file_size"
            :min="0"
            :max="10240"
            placeholder="Maximum file size in KB"
            style="width: 100%"
          />
          <div class="form-help">Leave empty for unlimited size</div>
        </el-form-item>
        
        <el-form-item label="Required">
          <el-switch
            v-model="form.is_required"
            active-text="Required"
            inactive-text="Optional"
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
          {{ editingAttachmentType ? 'Update' : 'Create' }}
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, Edit, Delete } from '@element-plus/icons-vue'

const loading = ref(false)
const attachmentTypes = ref([])
const showCreateDialog = ref(false)
const editingAttachmentType = ref(null)
const formRef = ref()

const form = reactive({
  name: '',
  code: '',
  description: '',
  allowed_extensions: '',
  max_file_size: null,
  is_required: false,
  is_active: true
})

const rules = {
  name: [
    { required: true, message: 'Attachment type name is required', trigger: 'blur' },
    { min: 2, message: 'Name must be at least 2 characters', trigger: 'blur' }
  ],
  code: [
    { required: true, message: 'Code is required', trigger: 'blur' },
    { min: 2, message: 'Code must be at least 2 characters', trigger: 'blur' }
  ]
}

const loadAttachmentTypes = async () => {
  loading.value = true
  try {
    // Mock data for now - replace with actual API call
    attachmentTypes.value = [
      {
        id: 1,
        name: 'Quotation Document',
        code: 'QUOTATION',
        description: 'Supplier quotation documents',
        allowed_extensions: 'pdf,doc,docx',
        max_file_size: 5120,
        is_required: true,
        is_active: true
      },
      {
        id: 2,
        name: 'Technical Specification',
        code: 'TECH_SPEC',
        description: 'Technical specification documents',
        allowed_extensions: 'pdf,doc,docx',
        max_file_size: 10240,
        is_required: false,
        is_active: true
      },
      {
        id: 3,
        name: 'Invoice',
        code: 'INVOICE',
        description: 'Invoice documents',
        allowed_extensions: 'pdf',
        max_file_size: 2048,
        is_required: true,
        is_active: true
      }
    ]
  } catch (error) {
    ElMessage.error('Failed to load attachment types')
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  form.name = ''
  form.code = ''
  form.description = ''
  form.allowed_extensions = ''
  form.max_file_size = null
  form.is_required = false
  form.is_active = true
  editingAttachmentType.value = null
}

const editAttachmentType = (attachmentType) => {
  editingAttachmentType.value = attachmentType
  form.name = attachmentType.name
  form.code = attachmentType.code
  form.description = attachmentType.description || ''
  form.allowed_extensions = attachmentType.allowed_extensions || ''
  form.max_file_size = attachmentType.max_file_size
  form.is_required = attachmentType.is_required
  form.is_active = attachmentType.is_active
  showCreateDialog.value = true
}

const handleSubmit = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()
    loading.value = true

    // Mock API call - replace with actual implementation
    ElMessage.success('Attachment type saved successfully')
    showCreateDialog.value = false
    resetForm()
    loadAttachmentTypes()
  } catch (error) {
    ElMessage.error('Failed to save attachment type')
  } finally {
    loading.value = false
  }
}

const deleteAttachmentType = async (attachmentType) => {
  try {
    await ElMessageBox.confirm(
      `Are you sure you want to delete attachment type "${attachmentType.name}"?`,
      'Confirm Deletion',
      {
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        type: 'warning'
      }
    )
    
    // Mock API call - replace with actual implementation
    ElMessage.success('Attachment type deleted successfully')
    loadAttachmentTypes()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('Failed to delete attachment type')
    }
  }
}

onMounted(() => {
  loadAttachmentTypes()
})
</script>

<style scoped>
.attachment-types {
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

.form-help {
  font-size: 12px;
  color: #909399;
  margin-top: 4px;
}
</style>
