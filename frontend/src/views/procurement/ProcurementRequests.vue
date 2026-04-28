<template>
  <div class="procurement-requests">
    <div class="floating-shapes">
      <div class="shape shape-1"></div>
      <div class="shape shape-2"></div>
      <div class="shape shape-3"></div>
      <div class="shape shape-4"></div>
      <div class="shape shape-5"></div>
      <div class="shape shape-6"></div>
    </div>
    
    <div class="page-header">
      <div class="header-content">
        <div class="title-section">
          <h1 class="main-title">Procurement Requests</h1>
          <p class="subtitle">Manage and track all procurement requests</p>
        </div>
        <button class="create-button" @click="$router.push('/procurement-requests/create')">
          <el-icon><Plus /></el-icon>
          New Request
        </button>
      </div>
      
      <div class="filters-section">
        <div class="filters-form">
          <div class="filter-group">
            <label class="filter-label">Status</label>
            <select v-model="filters.status" class="filter-select">
              <option value="">All Status</option>
              <option value="draft">Draft</option>
              <option value="submitted">Submitted</option>
              <option value="under_review">Under Review</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
          
          <div class="filter-group">
            <label class="filter-label">Priority</label>
            <select v-model="filters.priority" class="filter-select">
              <option value="">All Priorities</option>
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
              <option value="urgent">Urgent</option>
            </select>
          </div>
          
          <div class="filter-group">
            <label class="filter-label">Department</label>
            <select v-model="filters.department_id" class="filter-select">
              <option value="">All Departments</option>
              <option
                v-for="dept in departments"
                :key="dept.id"
                :value="dept.id"
              >
                {{ dept.name }}
              </option>
            </select>
          </div>
          
          <div class="filter-actions">
            <button class="search-button" @click="loadRequests">Search</button>
            <button class="reset-button" @click="resetFilters">Reset</button>
          </div>
        </div>
      </div>
    </div>

    <div class="requests-card">
      <div class="table-container">
        <el-table :data="requests" v-loading="loading" class="modern-table">
          <el-table-column prop="request_number" label="Request #" width="140" />
          <el-table-column prop="title" label="Title" min-width="200" />
          <el-table-column prop="department.name" label="Department" width="150" />
          <el-table-column prop="category.name" label="Category" width="120" />
          <el-table-column prop="status" label="Status" width="120">
            <template #default="scope">
              <span class="status-tag" :class="`status-${scope.row.status}`">
                {{ formatStatus(scope.row.status) }}
              </span>
            </template>
          </el-table-column>
          <el-table-column prop="priority" label="Priority" width="100">
            <template #default="scope">
              <span class="priority-tag" :class="`priority-${scope.row.priority}`">
                {{ scope.row.priority }}
              </span>
            </template>
          </el-table-column>
          <el-table-column prop="estimated_cost" label="Est. Cost" width="120">
            <template #default="scope">
              <span class="cost-value">${{ formatCurrency(scope.row.estimated_cost) }}</span>
            </template>
          </el-table-column>
          <el-table-column prop="required_date" label="Required Date" width="120">
            <template #default="scope">
              <span class="date-value">{{ formatDate(scope.row.required_date) }}</span>
            </template>
          </el-table-column>
          <el-table-column prop="created_at" label="Created" width="120">
            <template #default="scope">
              <span class="date-value">{{ formatDate(scope.row.created_at) }}</span>
            </template>
          </el-table-column>
          <el-table-column label="Actions" width="180" fixed="right">
            <template #default="scope">
              <div class="action-buttons">
                <button class="action-button view" @click="viewRequest(scope.row)">
                  <el-icon><View /></el-icon>
                </button>
                <button 
                  v-if="canEdit(scope.row)"
                  class="action-button edit"
                  @click="editRequest(scope.row)"
                >
                  <el-icon><Edit /></el-icon>
                </button>
                <button 
                  v-if="canSubmit(scope.row)"
                  class="action-button submit"
                  @click="submitRequest(scope.row)"
                >
                  <el-icon><Upload /></el-icon>
                </button>
                <button 
                  v-if="canApprove(scope.row)"
                  class="action-button approve"
                  @click="approveRequest(scope.row)"
                >
                  <el-icon><Check /></el-icon>
                </button>
                <button 
                  v-if="canReject(scope.row)"
                  class="action-button reject"
                  @click="rejectRequest(scope.row)"
                >
                  <el-icon><Close /></el-icon>
                </button>
              </div>
            </template>
          </el-table-column>
        </el-table>
      </div>

      <div class="pagination-wrapper">
        <el-pagination
          v-model:current-page="pagination.current_page"
          v-model:page-size="pagination.per_page"
          :page-sizes="[10, 20, 50, 100]"
          :total="pagination.total"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import procurementService from '@/services/procurement'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, View, Edit, Upload, Check, Close } from '@element-plus/icons-vue'

const router = useRouter()
const authStore = useAuthStore()

const loading = ref(false)
const requests = ref([])
const departments = ref([])

const filters = reactive({
  status: '',
  priority: '',
  department_id: ''
})

const pagination = reactive({
  current_page: 1,
  per_page: 10,
  total: 0
})

const formatStatus = (status) => {
  return status ? status.replace('_', ' ').toUpperCase() : ''
}

const getStatusType = (status) => {
  const types = {
    draft: 'info',
    submitted: 'warning',
    under_review: 'warning',
    approved: 'success',
    rejected: 'danger',
    cancelled: 'info'
  }
  return types[status] || 'info'
}

const getPriorityType = (priority) => {
  const types = {
    low: 'info',
    medium: 'warning',
    high: 'danger',
    urgent: 'danger'
  }
  return types[priority] || 'info'
}

const formatCurrency = (value) => {
  return value ? value.toLocaleString() : '0'
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const canEdit = (request) => {
  return request.status === 'draft' && request.user_id === authStore.user?.id
}

const canSubmit = (request) => {
  return request.status === 'draft' && request.user_id === authStore.user?.id
}

const canApprove = (request) => {
  return ['admin', 'manager'].includes(authStore.user?.role) && 
         ['submitted', 'under_review'].includes(request.status)
}

const canReject = (request) => {
  return ['admin', 'manager'].includes(authStore.user?.role) && 
         ['submitted', 'under_review'].includes(request.status)
}

const loadRequests = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      ...filters
    }
    
    const response = await procurementService.getProcurementRequests(params)
    requests.value = response.data
    pagination.total = response.total
    pagination.current_page = response.current_page
    pagination.per_page = response.per_page
  } catch (error) {
    ElMessage.error('Failed to load procurement requests')
  } finally {
    loading.value = false
  }
}

const loadDepartments = async () => {
  try {
    const response = await procurementService.getDepartments()
    departments.value = response
  } catch (error) {
    console.error('Failed to load departments:', error)
  }
}

const resetFilters = () => {
  filters.status = ''
  filters.priority = ''
  filters.department_id = ''
  pagination.current_page = 1
  loadRequests()
}

const handleSizeChange = (size) => {
  pagination.per_page = size
  pagination.current_page = 1
  loadRequests()
}

const handleCurrentChange = (page) => {
  pagination.current_page = page
  loadRequests()
}

const viewRequest = (request) => {
  router.push(`/procurement-requests/${request.id}`)
}

const editRequest = (request) => {
  router.push(`/procurement-requests/${request.id}/edit`)
}

const submitRequest = async (request) => {
  try {
    await ElMessageBox.confirm(
      'Are you sure you want to submit this procurement request?',
      'Confirm Submission',
      {
        confirmButtonText: 'Submit',
        cancelButtonText: 'Cancel',
        type: 'warning'
      }
    )
    
    await procurementService.submitRequest(request.id, {})
    ElMessage.success('Request submitted successfully')
    loadRequests()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('Failed to submit request')
    }
  }
}

const approveRequest = async (request) => {
  try {
    const { value: notes } = await ElMessageBox.prompt(
      'Please enter approval notes (optional)',
      'Approve Request',
      {
        confirmButtonText: 'Approve',
        cancelButtonText: 'Cancel',
        inputType: 'textarea',
        inputPlaceholder: 'Enter approval notes...'
      }
    )
    
    await procurementService.approveRequest(request.id, { approval_notes: notes })
    ElMessage.success('Request approved successfully')
    loadRequests()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('Failed to approve request')
    }
  }
}

const rejectRequest = async (request) => {
  try {
    const { value: reason } = await ElMessageBox.prompt(
      'Please enter rejection reason',
      'Reject Request',
      {
        confirmButtonText: 'Reject',
        cancelButtonText: 'Cancel',
        inputType: 'textarea',
        inputPlaceholder: 'Enter rejection reason...',
        inputValidator: (value) => {
          if (!value) {
            return 'Rejection reason is required'
          }
          return true
        }
      }
    )
    
    await procurementService.rejectRequest(request.id, { rejection_reason: reason })
    ElMessage.success('Request rejected successfully')
    loadRequests()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('Failed to reject request')
    }
  }
}

onMounted(() => {
  loadRequests()
  loadDepartments()
})
</script>

<style scoped>
.procurement-requests {
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
  width: 100px;
  height: 100px;
  top: 5%;
  left: 5%;
  animation-delay: 0s;
}

.shape-2 {
  width: 150px;
  height: 150px;
  top: 60%;
  right: 5%;
  animation-delay: 3s;
}

.shape-3 {
  width: 80px;
  height: 80px;
  bottom: 10%;
  left: 20%;
  animation-delay: 6s;
}

.shape-4 {
  width: 120px;
  height: 120px;
  top: 15%;
  right: 25%;
  animation-delay: 9s;
}

.shape-5 {
  width: 60px;
  height: 60px;
  bottom: 20%;
  right: 15%;
  animation-delay: 12s;
}

.shape-6 {
  width: 90px;
  height: 90px;
  top: 40%;
  left: 40%;
  animation-delay: 15s;
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
  margin-bottom: 24px;
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

.filters-section {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  padding: 24px;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.filters-form {
  display: flex;
  gap: 20px;
  align-items: flex-end;
  flex-wrap: wrap;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.filter-label {
  font-size: 14px;
  font-weight: 600;
  color: white;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.filter-select {
  padding: 8px 12px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.9);
  color: #1e293b;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.3s ease;
  min-width: 150px;
}

.filter-select:hover {
  border-color: rgba(255, 255, 255, 0.5);
  background: white;
}

.filter-select:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.filter-actions {
  display: flex;
  gap: 12px;
}

.search-button, .reset-button {
  padding: 8px 16px;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.search-button {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  color: white;
  box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);
}

.search-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(79, 172, 254, 0.4);
}

.reset-button {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.reset-button:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: translateY(-1px);
}

.requests-card {
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

.status-tag {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-draft {
  background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
  color: white;
}

.status-submitted {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
}

.status-under_review {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  color: white;
}

.status-approved {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
}

.status-rejected {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
}

.status-cancelled {
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  color: white;
}

.priority-tag {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.priority-low {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
}

.priority-medium {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
}

.priority-high {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  color: white;
}

.priority-urgent {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
}

.cost-value {
  font-weight: 600;
  color: #10b981;
  font-size: 14px;
}

.date-value {
  font-weight: 500;
  color: #64748b;
  font-size: 14px;
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
  color: white;
}

.action-button.view {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.action-button.edit {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.action-button.submit {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.action-button.approve {
  background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.action-button.reject {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.action-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.pagination-wrapper {
  display: flex;
  justify-content: center;
  margin-top: 24px;
}

.pagination-wrapper :deep(.el-pagination) {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  border-radius: 12px;
  padding: 8px;
  border: 1px solid rgba(255, 255, 255, 0.2);
}
</style>
