<template>
  <div class="vendor-relationships">
    <div class="page-header">
      <div class="header-content">
        <h1>Vendor Relationships</h1>
        <el-button type="primary" @click="showCreateDialog = true">
          <el-icon><Plus /></el-icon>
          Create Relationship
        </el-button>
      </div>
    </div>

    <el-card>
      <div class="filters">
        <el-form :model="filters" inline>
          <el-form-item label="Vendor">
            <el-select v-model="filters.vendor_id" placeholder="Select Vendor" clearable filterable>
              <el-option
                v-for="vendor in vendors"
                :key="vendor.id"
                :label="vendor.name"
                :value="vendor.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item label="Relationship Type">
            <el-select v-model="filters.type" placeholder="All Types" clearable>
              <el-option label="Partner" value="partner" />
              <el-option label="Subsidiary" value="subsidiary" />
              <el-option label="Supplier" value="supplier" />
              <el-option label="Competitor" value="competitor" />
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click="loadRelationships">
              <el-icon><Search /></el-icon>
              Search
            </el-button>
          </el-form-item>
        </el-form>
      </div>

      <el-table :data="relationships" v-loading="loading" style="width: 100%">
        <el-table-column prop="vendor.name" label="Vendor" min-width="200" />
        <el-table-column prop="related_vendor.name" label="Related Vendor" min-width="200" />
        <el-table-column prop="relationship_type" label="Type" width="120">
          <template #default="scope">
            <el-tag :type="getTypeColor(scope.row.relationship_type)">
              {{ scope.row.relationship_type }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="notes" label="Notes" min-width="250" />
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
              <el-button size="small" @click="editRelationship(scope.row)">
                <el-icon><Edit /></el-icon>
              </el-button>
              <el-button
                size="small"
                type="danger"
                @click="deleteRelationship(scope.row)"
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
      :title="editingRelationship ? 'Edit Relationship' : 'Create Relationship'"
      width="600px"
    >
      <el-form
        ref="formRef"
        :model="form"
        :rules="rules"
        label-width="140px"
      >
        <el-form-item label="Vendor" prop="vendor_id">
          <el-select v-model="form.vendor_id" placeholder="Select vendor" filterable>
            <el-option
              v-for="vendor in vendors"
              :key="vendor.id"
              :label="vendor.name"
              :value="vendor.id"
            />
          </el-select>
        </el-form-item>
        
        <el-form-item label="Related Vendor" prop="related_vendor_id">
          <el-select v-model="form.related_vendor_id" placeholder="Select related vendor" filterable>
            <el-option
              v-for="vendor in vendors"
              :key="vendor.id"
              :label="vendor.name"
              :value="vendor.id"
              :disabled="vendor.id === form.vendor_id"
            />
          </el-select>
        </el-form-item>
        
        <el-form-item label="Relationship Type" prop="relationship_type">
          <el-select v-model="form.relationship_type" placeholder="Select type">
            <el-option label="Partner" value="partner" />
            <el-option label="Subsidiary" value="subsidiary" />
            <el-option label="Supplier" value="supplier" />
            <el-option label="Competitor" value="competitor" />
          </el-select>
        </el-form-item>
        
        <el-form-item label="Notes">
          <el-input
            v-model="form.notes"
            type="textarea"
            :rows="4"
            placeholder="Enter relationship notes"
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
          {{ editingRelationship ? 'Update' : 'Create' }}
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, Edit, Delete, Search } from '@element-plus/icons-vue'

const loading = ref(false)
const relationships = ref([])
const vendors = ref([])
const showCreateDialog = ref(false)
const editingRelationship = ref(null)
const formRef = ref()

const filters = reactive({
  vendor_id: null,
  type: ''
})

const form = reactive({
  vendor_id: null,
  related_vendor_id: null,
  relationship_type: '',
  notes: '',
  is_active: true
})

const rules = {
  vendor_id: [
    { required: true, message: 'Vendor is required', trigger: 'change' }
  ],
  related_vendor_id: [
    { required: true, message: 'Related vendor is required', trigger: 'change' }
  ],
  relationship_type: [
    { required: true, message: 'Relationship type is required', trigger: 'change' }
  ]
}

const loadRelationships = async () => {
  loading.value = true
  try {
    // Mock data for now - replace with actual API call
    relationships.value = [
      {
        id: 1,
        vendor: { id: 1, name: 'Tech Solutions Inc' },
        related_vendor: { id: 2, name: 'Hardware Co' },
        relationship_type: 'partner',
        notes: 'Strategic partnership for IT equipment supply',
        is_active: true
      },
      {
        id: 2,
        vendor: { id: 3, name: 'Office Depot' },
        related_vendor: { id: 4, name: 'Stationery Plus' },
        relationship_type: 'competitor',
        notes: 'Competing office supplies vendor',
        is_active: true
      }
    ]
  } catch (error) {
    ElMessage.error('Failed to load vendor relationships')
  } finally {
    loading.value = false
  }
}

const loadVendors = async () => {
  try {
    // Mock data for now - replace with actual API call
    vendors.value = [
      { id: 1, name: 'Tech Solutions Inc' },
      { id: 2, name: 'Hardware Co' },
      { id: 3, name: 'Office Depot' },
      { id: 4, name: 'Stationery Plus' },
      { id: 5, name: 'Software Co' }
    ]
  } catch (error) {
    ElMessage.error('Failed to load vendors')
  }
}

const resetForm = () => {
  form.vendor_id = null
  form.related_vendor_id = null
  form.relationship_type = ''
  form.notes = ''
  form.is_active = true
  editingRelationship.value = null
}

const editRelationship = (relationship) => {
  editingRelationship.value = relationship
  form.vendor_id = relationship.vendor.id
  form.related_vendor_id = relationship.related_vendor.id
  form.relationship_type = relationship.relationship_type
  form.notes = relationship.notes || ''
  form.is_active = relationship.is_active
  showCreateDialog.value = true
}

const handleSubmit = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()
    loading.value = true

    // Mock API call - replace with actual implementation
    ElMessage.success('Vendor relationship saved successfully')
    showCreateDialog.value = false
    resetForm()
    loadRelationships()
  } catch (error) {
    ElMessage.error('Failed to save vendor relationship')
  } finally {
    loading.value = false
  }
}

const deleteRelationship = async (relationship) => {
  try {
    await ElMessageBox.confirm(
      `Are you sure you want to delete the relationship between "${relationship.vendor.name}" and "${relationship.related_vendor.name}"?`,
      'Confirm Deletion',
      {
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        type: 'warning'
      }
    )
    
    // Mock API call - replace with actual implementation
    ElMessage.success('Vendor relationship deleted successfully')
    loadRelationships()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('Failed to delete vendor relationship')
    }
  }
}

const getTypeColor = (type) => {
  const colors = {
    partner: 'success',
    subsidiary: 'primary',
    supplier: 'warning',
    competitor: 'danger'
  }
  return colors[type] || 'info'
}

onMounted(() => {
  loadRelationships()
  loadVendors()
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
