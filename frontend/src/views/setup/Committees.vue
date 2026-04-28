<template>
  <div class="committees">
    <div class="page-header">
      <div class="header-content">
        <h1>Committee Setup</h1>
        <el-button type="primary" @click="showCreateDialog = true">
          <el-icon><Plus /></el-icon>
          Create Committee
        </el-button>
      </div>
    </div>

    <el-card>
      <el-table :data="committees" v-loading="loading" style="width: 100%">
        <el-table-column prop="name" label="Committee Name" min-width="200" />
        <el-table-column prop="chairperson" label="Chairperson" width="180" />
        <el-table-column label="Members" width="120">
          <template #default="scope">
            <el-tag>{{ scope.row.members?.length || 0 }} members</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="description" label="Description" min-width="250" />
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
              <el-button size="small" @click="editCommittee(scope.row)">
                <el-icon><Edit /></el-icon>
              </el-button>
              <el-button
                size="small"
                type="danger"
                @click="deleteCommittee(scope.row)"
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
      :title="editingCommittee ? 'Edit Committee' : 'Create Committee'"
      width="700px"
    >
      <el-form
        ref="formRef"
        :model="form"
        :rules="rules"
        label-width="120px"
      >
        <el-form-item label="Name" prop="name">
          <el-input v-model="form.name" placeholder="Enter committee name" />
        </el-form-item>
        
        <el-form-item label="Chairperson">
          <el-input v-model="form.chairperson" placeholder="Enter chairperson name" />
        </el-form-item>
        
        <el-form-item label="Description">
          <el-input
            v-model="form.description"
            type="textarea"
            :rows="3"
            placeholder="Enter committee description"
          />
        </el-form-item>
        
        <el-form-item label="Members">
          <div class="committee-members">
            <div
              v-for="(member, index) in form.members"
              :key="index"
              class="member-item"
            >
              <el-card class="member-card">
                <template #header>
                  <div class="member-header">
                    <span>Member {{ index + 1 }}</span>
                    <el-button
                      size="small"
                      type="danger"
                      @click="removeMember(index)"
                      :disabled="form.members.length <= 1"
                    >
                      <el-icon><Delete /></el-icon>
                    </el-button>
                  </div>
                </template>
                
                <el-form :model="member" label-width="80px">
                  <el-form-item label="Name">
                    <el-input v-model="member.name" placeholder="Member name" />
                  </el-form-item>
                  
                  <el-form-item label="Email">
                    <el-input v-model="member.email" placeholder="Email address" />
                  </el-form-item>
                  
                  <el-form-item label="Role">
                    <el-select v-model="member.role" placeholder="Select role">
                      <el-option label="Member" value="member" />
                      <el-option label="Chair" value="chair" />
                      <el-option label="Secretary" value="secretary" />
                    </el-select>
                  </el-form-item>
                </el-form>
              </el-card>
            </div>
            
            <el-button
              type="primary"
              plain
              @click="addMember"
              class="add-member-btn"
            >
              <el-icon><Plus /></el-icon>
              Add Member
            </el-button>
          </div>
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
          {{ editingCommittee ? 'Update' : 'Create' }}
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
const committees = ref([])
const showCreateDialog = ref(false)
const editingCommittee = ref(null)
const formRef = ref()

const form = reactive({
  name: '',
  chairperson: '',
  description: '',
  members: [
    {
      name: '',
      email: '',
      role: 'member'
    }
  ],
  is_active: true
})

const rules = {
  name: [
    { required: true, message: 'Committee name is required', trigger: 'blur' },
    { min: 2, message: 'Name must be at least 2 characters', trigger: 'blur' }
  ]
}

const loadCommittees = async () => {
  loading.value = true
  try {
    // Mock data for now - replace with actual API call
    committees.value = [
      {
        id: 1,
        name: 'Procurement Committee',
        chairperson: 'John Smith',
        description: 'Main procurement decision-making committee',
        members: [
          { name: 'John Smith', email: 'john@example.com', role: 'chair' },
          { name: 'Jane Doe', email: 'jane@example.com', role: 'member' }
        ],
        is_active: true
      }
    ]
  } catch (error) {
    ElMessage.error('Failed to load committees')
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  form.name = ''
  form.chairperson = ''
  form.description = ''
  form.members = [
    {
      name: '',
      email: '',
      role: 'member'
    }
  ]
  form.is_active = true
  editingCommittee.value = null
}

const editCommittee = (committee) => {
  editingCommittee.value = committee
  form.name = committee.name
  form.chairperson = committee.chairperson || ''
  form.description = committee.description || ''
  form.members = [...committee.members]
  form.is_active = committee.is_active
  showCreateDialog.value = true
}

const addMember = () => {
  form.members.push({
    name: '',
    email: '',
    role: 'member'
  })
}

const removeMember = (index) => {
  form.members.splice(index, 1)
}

const handleSubmit = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()
    loading.value = true

    // Mock API call - replace with actual implementation
    ElMessage.success('Committee saved successfully')
    showCreateDialog.value = false
    resetForm()
    loadCommittees()
  } catch (error) {
    ElMessage.error('Failed to save committee')
  } finally {
    loading.value = false
  }
}

const deleteCommittee = async (committee) => {
  try {
    await ElMessageBox.confirm(
      `Are you sure you want to delete committee "${committee.name}"?`,
      'Confirm Deletion',
      {
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        type: 'warning'
      }
    )
    
    // Mock API call - replace with actual implementation
    ElMessage.success('Committee deleted successfully')
    loadCommittees()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('Failed to delete committee')
    }
  }
}

onMounted(() => {
  loadCommittees()
})
</script>

<style scoped>
.committees {
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

.committee-members {
  width: 100%;
}

.member-item {
  margin-bottom: 16px;
}

.member-card {
  border: 1px solid #e4e7ed;
}

.member-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-weight: 600;
}

.add-member-btn {
  width: 100%;
  margin-top: 16px;
}
</style>
