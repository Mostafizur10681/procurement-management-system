<template>
  <div class="workflows">
    <div class="floating-shapes">
      <div class="shape shape-1"></div>
      <div class="shape shape-2"></div>
      <div class="shape shape-3"></div>
      <div class="shape shape-4"></div>
      <div class="shape shape-5"></div>
    </div>
    
    <div class="page-header">
      <div class="header-content">
        <div class="title-section">
          <h1 class="main-title">Workflow Setup</h1>
          <p class="subtitle">Configure and manage procurement workflows</p>
        </div>
        <button class="create-button" @click="showCreateDialog = true">
          <el-icon><Plus /></el-icon>
          Create Workflow
        </button>
      </div>
    </div>

    <div class="workflows-card">
      <div class="table-container">
        <el-table :data="workflows" v-loading="loading" class="modern-table">
          <el-table-column prop="name" label="Workflow Name" min-width="200" />
          <el-table-column prop="rule_name" label="Rule Type" min-width="180" />
          <el-table-column prop="description" label="Description" min-width="200" />
          <el-table-column label="Steps" width="120">
            <template #default="scope">
              <span class="steps-tag">{{ scope.row.steps?.length || 0 }} steps</span>
            </template>
          </el-table-column>
          <el-table-column prop="is_active" label="Status" width="100">
            <template #default="scope">
              <span class="status-tag" :class="scope.row.is_active ? 'active' : 'inactive'">
                {{ scope.row.is_active ? 'Active' : 'Inactive' }}
              </span>
            </template>
          </el-table-column>
          <el-table-column label="Actions" width="150" fixed="right">
            <template #default="scope">
              <div class="action-buttons">
                <button class="action-button edit" @click="editWorkflow(scope.row)">
                  <el-icon><Edit /></el-icon>
                </button>
                <button class="action-button delete" @click="deleteWorkflow(scope.row)">
                  <el-icon><Delete /></el-icon>
                </button>
              </div>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </div>

    <!-- Create/Edit Dialog -->
    <el-dialog
      v-model="showCreateDialog"
      :title="editingWorkflow ? 'Edit Workflow' : 'Create Workflow'"
      width="900px"
      :show-close="false"
      :close-on-click-modal="false"
    >
      <div class="workflow-form">
        <!-- Blue Header -->
        <div class="form-header">
          <h3>Workflow Setup form</h3>
        </div>

        <!-- Top Input Fields -->
        <div class="form-fields">
          <el-row :gutter="20">
            <el-col :span="8">
              <el-form-item label="Approval Type*">
                <el-select
                  v-model="form.approval_type"
                  placeholder="Select One"
                  style="width: 100%"
                >
                  <el-option
                    v-for="(name, id) in workflowRules"
                    :key="id"
                    :label="name"
                    :value="id"
                  />
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8">
              <el-form-item label="Work Flow Name*">
                <el-input
                  v-model="form.name"
                  placeholder="Enter workflow name"
                />
              </el-form-item>
            </el-col>
            <el-col :span="8">
              <el-form-item label="Active">
                <el-radio-group v-model="form.is_active">
                  <el-radio :label="true">Yes</el-radio>
                  <el-radio :label="false">No</el-radio>
                </el-radio-group>
              </el-form-item>
            </el-col>
          </el-row>
        </div>

        <!-- Workflow Step Section -->
        <div class="workflow-step-section">
          <div class="section-header">
            <h4>Workflow Step</h4>
          </div>
          
          <div class="dual-list-container">
            <!-- Left Panel -->
            <div class="list-panel left-panel">
              <div class="panel-header">
                <el-input
                  v-model="leftSearch"
                  placeholder="Search..."
                  size="small"
                  prefix-icon="Search"
                />
              </div>
              <div class="panel-title">Team Member/Designation</div>
              <div class="panel-content">
                <div class="checkbox-list">
                  <el-checkbox
                    v-for="member in availableMembers"
                    :key="member.id"
                    v-model="member.selected"
                    class="member-checkbox"
                  >
                    {{ member.name }}
                    <span class="member-department">({{ member.department }})</span>
                  </el-checkbox>
                </div>
              </div>
              <div class="panel-footer">
                <el-button size="small" @click="selectAllLeft">Select All</el-button>
              </div>
            </div>

            <!-- Transfer Buttons -->
            <div class="transfer-buttons">
              <el-button
                type="primary"
                size="small"
                @click="transferRight"
                :disabled="selectedLeftCount === 0"
              >
                <el-icon><ArrowRight /></el-icon>
              </el-button>
              <el-button
                type="primary"
                size="small"
                @click="transferLeft"
                :disabled="selectedRightCount === 0"
              >
                <el-icon><ArrowLeft /></el-icon>
              </el-button>
            </div>

            <!-- Right Panel -->
            <div class="list-panel right-panel">
              <div class="panel-header">
                <el-input
                  v-model="rightSearch"
                  placeholder="Search..."
                  size="small"
                  prefix-icon="Search"
                />
              </div>
              <div class="panel-title">Assign to Review</div>
              <div class="panel-content">
                <div class="checkbox-list">
                  <el-checkbox
                    v-for="member in assignedMembers"
                    :key="member.id"
                    v-model="member.selected"
                    class="member-checkbox"
                  >
                    {{ member.name }}
                    <span class="member-department">({{ member.department }})</span>
                  </el-checkbox>
                </div>
              </div>
              <div class="panel-footer">
                <el-button size="small" @click="selectAllRight">Deselect All</el-button>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <template #footer>
        <div class="dialog-footer">
          <el-button type="success" @click="handleSubmit" :loading="loading">
            Save
          </el-button>
        </div>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, Edit, Delete, ArrowRight, ArrowLeft, Search } from '@element-plus/icons-vue'

const loading = ref(false)
const workflows = ref([])
const workflowRules = ref([])
const showCreateDialog = ref(false)
const editingWorkflow = ref(null)
const formRef = ref()

// Search terms
const leftSearch = ref('')
const rightSearch = ref('')

// Team members data
const allMembers = ref([
  { id: 1, name: 'Managing Director', department: 'Board Department', selected: false },
  { id: 2, name: 'General Manager', department: 'HR, Finance, Accounts', selected: false },
  { id: 3, name: 'Assistant General Manager', department: 'Operations', selected: false },
  { id: 4, name: 'Senior Manager', department: 'Finance', selected: false },
  { id: 5, name: 'Manager', department: 'IT', selected: false },
  { id: 6, name: 'Assistant Manager', department: 'Procurement', selected: false },
  { id: 7, name: 'Senior Executive', department: 'Admin', selected: false },
  { id: 8, name: 'Executive', department: 'Marketing', selected: false }
])

const form = reactive({
  name: '',
  approval_type: null,
  is_active: true,
  assigned_members: []
})

// Computed properties for filtered lists
const availableMembers = computed(() => {
  const assignedIds = form.assigned_members.map(m => m.id)
  return allMembers.value
    .filter(member => !assignedIds.includes(member.id))
    .filter(member => 
      member.name.toLowerCase().includes(leftSearch.value.toLowerCase()) ||
      member.department.toLowerCase().includes(leftSearch.value.toLowerCase())
    )
})

const assignedMembers = computed(() => {
  return allMembers.value
    .filter(member => form.assigned_members.some(m => m.id === member.id))
    .filter(member => 
      member.name.toLowerCase().includes(rightSearch.value.toLowerCase()) ||
      member.department.toLowerCase().includes(rightSearch.value.toLowerCase())
    )
})

const selectedLeftCount = computed(() => {
  return availableMembers.value.filter(m => m.selected).length
})

const selectedRightCount = computed(() => {
  return assignedMembers.value.filter(m => m.selected).length
})

const rules = {
  name: [
    { required: true, message: 'Workflow name is required', trigger: 'blur' },
    { min: 2, message: 'Name must be at least 2 characters', trigger: 'blur' }
  ],
  approval_type: [
    { required: true, message: 'Approval type is required', trigger: 'change' }
  ]
}

// Transfer functions
const transferRight = () => {
  const selectedMembers = availableMembers.value.filter(m => m.selected)
  selectedMembers.forEach(member => {
    member.selected = false
    if (!form.assigned_members.some(m => m.id === member.id)) {
      form.assigned_members.push({
        id: member.id,
        name: member.name,
        department: member.department
      })
    }
  })
}

const transferLeft = () => {
  const selectedMembers = assignedMembers.value.filter(m => m.selected)
  selectedMembers.forEach(member => {
    member.selected = false
    const index = form.assigned_members.findIndex(m => m.id === member.id)
    if (index > -1) {
      form.assigned_members.splice(index, 1)
    }
  })
}

const selectAllLeft = () => {
  availableMembers.value.forEach(member => {
    member.selected = true
  })
}

const selectAllRight = () => {
  assignedMembers.value.forEach(member => {
    member.selected = true
  })
}

const loadWorkflowRules = async () => {
  try {
    const response = await fetch('/api/workflow-rules')
    const data = await response.json()
    workflowRules.value = data.workflowconfigs || {}
  } catch (error) {
    console.error('Failed to load workflow rules:', error)
    // Fallback rules
    workflowRules.value = {
      '5': 'Purchase Requisition',
      '20': 'Price Estimate',
      '21': 'RFQ',
      '23': 'Tender Approval',
      '22': 'Vendor Approve',
      '11': 'PO/WO Approval',
      '25': 'Pre Bill',
      '26': 'Post Bill',
      '28': 'Committee Approve',
      '40': 'Audit'
    }
  }
}

const loadWorkflows = async () => {
  loading.value = true
  try {
    // Mock data for now - replace with actual API call
    workflows.value = [
      {
        id: 1,
        name: 'Standard Procurement',
        rule_id: '5',
        rule_name: 'Purchase Requisition',
        description: 'Standard procurement workflow',
        steps: [
          { name: 'Draft', assigned_role: 'user' },
          { name: 'Review', assigned_role: 'manager' },
          { name: 'Approval', assigned_role: 'admin' }
        ],
        is_active: true
      },
      {
        id: 2,
        name: 'Tender Workflow',
        rule_id: '23',
        rule_name: 'Tender Approval',
        description: 'Tender approval workflow',
        steps: [
          { name: 'Submit', assigned_role: 'user' },
          { name: 'Technical Review', assigned_role: 'manager' },
          { name: 'Financial Review', assigned_role: 'manager' },
          { name: 'Final Approval', assigned_role: 'admin' }
        ],
        is_active: true
      }
    ]
  } catch (error) {
    ElMessage.error('Failed to load workflows')
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  form.name = ''
  form.approval_type = null
  form.is_active = true
  form.assigned_members = []
  
  // Reset all member selections
  allMembers.value.forEach(member => {
    member.selected = false
  })
  
  // Reset search terms
  leftSearch.value = ''
  rightSearch.value = ''
  
  editingWorkflow.value = null
}

const editWorkflow = (workflow) => {
  editingWorkflow.value = workflow
  form.name = workflow.name
  form.approval_type = workflow.approval_type
  form.is_active = workflow.is_active
  form.assigned_members = workflow.assigned_members || []
  showCreateDialog.value = true
}

const handleSubmit = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()
    
    if (form.assigned_members.length === 0) {
      ElMessage.warning('Please assign at least one team member to review')
      return
    }
    
    loading.value = true

    // Create workflow data
    const workflowData = {
      name: form.name,
      approval_type: form.approval_type,
      is_active: form.is_active,
      assigned_members: form.assigned_members
    }

    if (editingWorkflow.value) {
      // Update workflow
      const index = workflows.value.findIndex(w => w.id === editingWorkflow.value.id)
      if (index > -1) {
        workflows.value[index] = { ...workflowData, id: editingWorkflow.value.id }
      }
      ElMessage.success('Workflow updated successfully')
    } else {
      // Create new workflow
      workflows.value.push({
        ...workflowData,
        id: Date.now(),
        rule_name: workflowRules.value[form.approval_type] || 'Unknown'
      })
      ElMessage.success('Workflow created successfully')
    }

    showCreateDialog.value = false
    resetForm()
  } catch (error) {
    ElMessage.error('Failed to save workflow')
  } finally {
    loading.value = false
  }
}

const deleteWorkflow = async (workflow) => {
  try {
    await ElMessageBox.confirm(
      `Are you sure you want to delete workflow "${workflow.name}"?`,
      'Confirm Deletion',
      {
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        type: 'warning'
      }
    )
    
    // Mock API call - replace with actual implementation
    ElMessage.success('Workflow deleted successfully')
    loadWorkflows()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('Failed to delete workflow')
    }
  }
}

onMounted(() => {
  loadWorkflowRules()
  loadWorkflows()
})
</script>

<style scoped>
.workflows {
  padding: 32px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #f5576c 75%, #4facfe 100%);
  background-size: 400% 400%;
  animation: gradientShift 20s ease infinite;
  min-height: 100vh;
  position: relative;
  overflow: hidden;
}

@keyframes gradientShift {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

.floating-shapes {
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  pointer-events: none;
}

.shape {
  position: absolute;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(5px);
  animation: float 25s infinite ease-in-out;
}

.shape-1 {
  width: 80px;
  height: 80px;
  top: 10%;
  left: 5%;
  animation-delay: 0s;
}

.shape-2 {
  width: 120px;
  height: 120px;
  top: 60%;
  right: 10%;
  animation-delay: 3s;
}

.shape-3 {
  width: 60px;
  height: 60px;
  bottom: 20%;
  left: 15%;
  animation-delay: 6s;
}

.shape-4 {
  width: 100px;
  height: 100px;
  top: 20%;
  right: 20%;
  animation-delay: 9s;
}

.shape-5 {
  width: 50px;
  height: 50px;
  bottom: 30%;
  right: 15%;
  animation-delay: 12s;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0) rotate(0deg);
    opacity: 0.6;
  }
  25% {
    transform: translateY(-30px) rotate(90deg);
    opacity: 0.8;
  }
  50% {
    transform: translateY(0) rotate(180deg);
    opacity: 0.6;
  }
  75% {
    transform: translateY(30px) rotate(270deg);
    opacity: 0.8;
  }
}

.page-header {
  margin-bottom: 32px;
  padding: 32px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 24px;
  box-shadow: 
    0 32px 64px rgba(0, 0, 0, 0.1),
    0 16px 32px rgba(0, 0, 0, 0.05),
    inset 0 1px 0 rgba(255, 255, 255, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.2);
  position: relative;
  z-index: 10;
  animation: slideUp 0.8s ease-out;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 24px;
}

.title-section {
  flex: 1;
}

.main-title {
  font-size: 42px;
  font-weight: 800;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin: 0 0 8px 0;
  letter-spacing: -1px;
  animation: titlePulse 3s ease-in-out infinite;
}

@keyframes titlePulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.02); }
}

.subtitle {
  font-size: 16px;
  color: #64748b;
  margin: 0;
  font-weight: 500;
}

.create-button {
  padding: 12px 24px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
  display: flex;
  align-items: center;
  gap: 8px;
  position: relative;
  overflow: hidden;
}

.create-button::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.6s ease;
}

.create-button:hover::before {
  left: 100%;
}

.create-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 30px rgba(102, 126, 234, 0.4);
}

.workflows-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 24px;
  padding: 32px;
  box-shadow: 
    0 32px 64px rgba(0, 0, 0, 0.1),
    0 16px 32px rgba(0, 0, 0, 0.05),
    inset 0 1px 0 rgba(255, 255, 255, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.2);
  position: relative;
  z-index: 10;
  animation: fadeInUp 0.8s ease-out backwards;
  animation-delay: 0.2s;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.table-container {
  border-radius: 16px;
  overflow: hidden;
  background: white;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
}

.modern-table {
  background: white;
  border-radius: 16px;
  overflow: hidden;
}

.modern-table :deep(.el-table__header) {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.modern-table :deep(.el-table__header th) {
  background: transparent;
  color: white;
  font-weight: 600;
  border: none;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.modern-table :deep(.el-table__body tr) {
  transition: all 0.3s ease;
}

.modern-table :deep(.el-table__body tr:hover) {
  background: rgba(102, 126, 234, 0.05);
  transform: translateX(4px);
}

.steps-tag {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  color: white;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-tag {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-tag.active {
  background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
  color: white;
}

.status-tag.inactive {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  color: white;
}

.action-buttons {
  display: flex;
  gap: 8px;
}

.action-button {
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.action-button.edit {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.action-button.delete {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  color: white;
}

.action-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Workflow Form Styles */
.workflow-form {
  padding: 0;
  background: transparent;
}

.form-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 20px;
  border-radius: 16px 16px 0 0;
  margin: -20px -20px 20px -20px;
  position: relative;
  overflow: hidden;
}

.form-header::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), transparent);
  animation: shimmer 3s infinite;
}

@keyframes shimmer {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

.form-header h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  position: relative;
  z-index: 1;
}

.form-fields {
  margin-bottom: 30px;
  padding: 20px;
  background: rgba(255, 255, 255, 0.5);
  border-radius: 12px;
  backdrop-filter: blur(5px);
}

.form-fields .el-form-item {
  margin-bottom: 20px;
}

.form-fields .el-form-item__label {
  font-weight: 600;
  color: #4a5568;
  font-size: 14px;
}

.form-fields .el-input,
.form-fields .el-select {
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}

.form-fields .el-input:hover,
.form-fields .el-select:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transform: translateY(-1px);
}

.form-fields .el-input:focus-within,
.form-fields .el-select:focus-within {
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-fields .el-radio-group .el-radio {
  margin-right: 20px;
}

.form-fields .el-radio__label {
  font-weight: 500;
  color: #4a5568;
}

/* Workflow Step Section */
.workflow-step-section {
  border: 2px solid #e2e8f0;
  border-radius: 16px;
  margin-bottom: 20px;
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(5px);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}

.workflow-step-section:hover {
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
  transform: translateY(-2px);
}

.section-header {
  background: linear-gradient(135deg, #f6f8fb 0%, #e9ecef 100%);
  padding: 16px 20px;
  border-bottom: 2px solid #e2e8f0;
  border-radius: 14px 14px 0 0;
  position: relative;
}

.section-header::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(90deg, #667eea, #764ba2, #667eea);
  animation: gradient 3s ease infinite;
  background-size: 200% 100%;
}

@keyframes gradient {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

.section-header h4 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: #2d3748;
  display: flex;
  align-items: center;
}

.section-header h4::before {
  content: '⚡';
  margin-right: 8px;
  font-size: 20px;
}

/* Dual List Container */
.dual-list-container {
  display: flex;
  gap: 24px;
  padding: 24px;
  min-height: 450px;
  background: rgba(255, 255, 255, 0.3);
  border-radius: 12px;
}

/* List Panels */
.list-panel {
  flex: 1;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  background: white;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  overflow: hidden;
}

.list-panel:hover {
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
  transform: translateY(-2px);
}

.left-panel {
  border-left: 4px solid #48bb78;
}

.right-panel {
  border-left: 4px solid #4299e1;
}

.panel-header {
  padding: 16px;
  border-bottom: 2px solid #e2e8f0;
  background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
}

.panel-title {
  padding: 16px;
  font-weight: 700;
  color: #2d3748;
  border-bottom: 2px solid #e2e8f0;
  background: linear-gradient(135deg, #f6f8fb 0%, #e9ecef 100%);
  font-size: 15px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.panel-content {
  flex: 1;
  padding: 12px;
  overflow-y: auto;
  max-height: 320px;
  background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
}

.checkbox-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.member-checkbox {
  margin-bottom: 0;
  padding: 12px 16px;
  border-radius: 8px;
  transition: all 0.3s ease;
  border: 2px solid transparent;
  background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
  position: relative;
  overflow: hidden;
}

.member-checkbox::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
  transition: left 0.5s ease;
}

.member-checkbox:hover {
  background: linear-gradient(135deg, #edf2f7 0%, #e2e8f0 100%);
  border-color: #cbd5e0;
  transform: translateX(4px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.member-checkbox:hover::before {
  left: 100%;
}

.member-checkbox:has(.el-checkbox__input.is-checked) {
  background: linear-gradient(135deg, #c6f6d5 0%, #9ae6b4 100%);
  border-color: #48bb78;
}

.member-department {
  color: #718096;
  font-size: 12px;
  margin-left: 8px;
  font-weight: 500;
}

.panel-footer {
  padding: 16px;
  border-top: 2px solid #e2e8f0;
  background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
  text-align: center;
}

.panel-footer .el-button {
  background: linear-gradient(45deg, #667eea, #764ba2);
  border: none;
  color: white;
  font-weight: 500;
  border-radius: 8px;
  padding: 8px 20px;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.panel-footer .el-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

/* Transfer Buttons */
.transfer-buttons {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 16px;
  padding: 0 12px;
}

.transfer-buttons .el-button {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.transfer-buttons .el-button::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.3);
  transform: translate(-50%, -50%);
  transition: all 0.3s ease;
}

.transfer-buttons .el-button:hover::before {
  width: 100%;
  height: 100%;
}

.transfer-buttons .el-button:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

.transfer-buttons .el-button:disabled {
  opacity: 0.5;
  transform: scale(0.9);
}

/* Dialog Footer */
.dialog-footer {
  text-align: right;
  padding: 20px;
  background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
  border-radius: 0 0 16px 16px;
  margin: 0 -20px -20px -20px;
}

.dialog-footer .el-button {
  min-width: 120px;
  height: 40px;
  font-weight: 600;
  border-radius: 8px;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.dialog-footer .el-button--success {
  background: linear-gradient(45deg, #48bb78, #38a169);
  border: none;
  color: white;
  box-shadow: 0 4px 15px rgba(72, 187, 120, 0.3);
}

.dialog-footer .el-button--success:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(72, 187, 120, 0.4);
}

/* Table Styles */
.el-table {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.el-table th {
  background: linear-gradient(135deg, #f6f8fb 0%, #e9ecef 100%);
  color: #2d3748;
  font-weight: 600;
}

.el-table tr:hover > td {
  background-color: rgba(102, 126, 234, 0.05);
}

/* Animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.workflow-form,
.el-card {
  animation: fadeInUp 0.6s ease-out;
}

/* Responsive Design */
@media (max-width: 768px) {
  .dual-list-container {
    flex-direction: column;
    gap: 16px;
  }
  
  .transfer-buttons {
    flex-direction: row;
    justify-content: center;
    padding: 16px 0;
  }
  
  .transfer-buttons .el-button {
    width: 40px;
    height: 40px;
  }
  
  .form-fields .el-row {
    flex-direction: column;
  }
  
  .form-fields .el-col {
    margin-bottom: 16px;
  }
}

/* Custom scrollbar */
.panel-content::-webkit-scrollbar {
  width: 6px;
}

.panel-content::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.panel-content::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, #667eea, #764ba2);
  border-radius: 3px;
}

.panel-content::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, #5a67d8, #6b46c1);
}
</style>
