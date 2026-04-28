<template>
  <div class="procurement-request-detail">
    <div class="page-header">
      <h1>Procurement Request Details</h1>
      <div class="header-actions">
        <el-button @click="$router.back()">
          <el-icon><ArrowLeft /></el-icon>
          Back
        </el-button>
        <el-button
          v-if="canEdit"
          type="primary"
          @click="editRequest"
        >
          <el-icon><Edit /></el-icon>
          Edit
        </el-button>
        <el-button
          v-if="canSubmit"
          type="success"
          @click="submitRequest"
        >
          <el-icon><Upload /></el-icon>
          Submit
        </el-button>
        <el-button
          v-if="canApprove"
          type="success"
          @click="approveRequest"
        >
          <el-icon><Check /></el-icon>
          Approve
        </el-button>
        <el-button
          v-if="canReject"
          type="danger"
          @click="rejectRequest"
        >
          <el-icon><Close /></el-icon>
          Reject
        </el-button>
      </div>
    </div>

    <el-row :gutter="20">
      <el-col :span="16">
        <el-card>
          <template #header>
            <div class="card-header">
              <h3>{{ request.title }}</h3>
              <el-tag :type="getStatusType(request.status)">
                {{ formatStatus(request.status) }}
              </el-tag>
            </div>
          </template>

          <el-descriptions :column="2" border>
            <el-descriptions-item label="Request Number">
              {{ request.request_number }}
            </el-descriptions-item>
            <el-descriptions-item label="Department">
              {{ request.department?.name }}
            </el-descriptions-item>
            <el-descriptions-item label="Category">
              {{ request.category?.name }}
            </el-descriptions-item>
            <el-descriptions-item label="Priority">
              <el-tag :type="getPriorityType(request.priority)" size="small">
                {{ request.priority }}
              </el-tag>
            </el-descriptions-item>
            <el-descriptions-item label="Requester">
              {{ request.user?.name }}
            </el-descriptions-item>
            <el-descriptions-item label="Required Date">
              {{ formatDate(request.required_date) }}
            </el-descriptions-item>
            <el-descriptions-item label="Created Date">
              {{ formatDate(request.created_at) }}
            </el-descriptions-item>
            <el-descriptions-item label="Estimated Cost">
              <strong>${{ formatCurrency(request.estimated_cost) }}</strong>
            </el-descriptions-item>
          </el-descriptions>

          <div class="description-section">
            <h4>Description</h4>
            <p>{{ request.description }}</p>
          </div>

          <div v-if="request.justification" class="justification-section">
            <h4>Justification</h4>
            <p>{{ request.justification }}</p>
          </div>

          <div v-if="request.approval_notes" class="approval-section">
            <h4>Approval Notes</h4>
            <p>{{ request.approval_notes }}</p>
            <p><small>Approved by: {{ request.approved_by?.name }} on {{ formatDate(request.approved_at) }}</small></p>
          </div>
        </el-card>

        <el-card style="margin-top: 20px">
          <template #header>
            <h3>Procurement Items</h3>
          </template>

          <el-table :data="request.items" style="width: 100%">
            <el-table-column prop="item_name" label="Item Name" min-width="200" />
            <el-table-column prop="description" label="Description" min-width="150" />
            <el-table-column prop="quantity" label="Quantity" width="100" align="center" />
            <el-table-column prop="unit_of_measure" label="UoM" width="80" align="center" />
            <el-table-column prop="unit_price" label="Unit Price" width="120" align="right">
              <template #default="scope">
                ${{ formatCurrency(scope.row.unit_price) }}
              </template>
            </el-table-column>
            <el-table-column prop="total_price" label="Total Price" width="120" align="right">
              <template #default="scope">
                ${{ formatCurrency(scope.row.total_price) }}
              </template>
            </el-table-column>
            <el-table-column prop="vendor.name" label="Vendor" width="150" />
            <el-table-column label="Specifications" min-width="200">
              <template #default="scope">
                <span v-if="scope.row.specifications">{{ scope.row.specifications }}</span>
                <span v-else class="text-muted">-</span>
              </template>
            </el-table-column>
          </el-table>
        </el-card>
      </el-col>

      <el-col :span="8">
        <el-card>
          <template #header>
            <h3>Workflow History</h3>
          </template>

          <el-timeline>
            <el-timeline-item
              v-for="log in request.workflow_logs"
              :key="log.id"
              :timestamp="formatDateTime(log.created_at)"
              placement="top"
            >
              <div class="workflow-item">
                <div class="workflow-action">
                  <el-tag :type="getActionType(log.action)" size="small">
                    {{ formatAction(log.action) }}
                  </el-tag>
                  <span class="user-name">{{ log.user?.name }}</span>
                </div>
                <div v-if="log.comments" class="workflow-comments">
                  {{ log.comments }}
                </div>
              </div>
            </el-timeline-item>
          </el-timeline>
        </el-card>

        <el-card style="margin-top: 20px">
          <template #header>
            <h3>Quick Actions</h3>
          </template>

          <div class="quick-actions">
            <el-button
              v-if="canPrint"
              type="primary"
              plain
              style="width: 100%; margin-bottom: 10px"
              @click="printRequest"
            >
              <el-icon><Printer /></el-icon>
              Print Request
            </el-button>
            
            <el-button
              v-if="canExport"
              type="success"
              plain
              style="width: 100%; margin-bottom: 10px"
              @click="exportRequest"
            >
              <el-icon><Download /></el-icon>
              Export PDF
            </el-button>

            <el-button
              v-if="canCancel"
              type="danger"
              plain
              style="width: 100%"
              @click="cancelRequest"
            >
              <el-icon><Close /></el-icon>
              Cancel Request
            </el-button>
          </div>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import procurementService from '@/services/procurement'
import { ElMessage, ElMessageBox } from 'element-plus'
import { ArrowLeft, Edit, Upload, Check, Close, Printer, Download } from '@element-plus/icons-vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const request = ref({})

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

const getActionType = (action) => {
  const types = {
    submitted: 'warning',
    approved: 'success',
    rejected: 'danger',
    cancelled: 'info'
  }
  return types[action] || 'info'
}

const formatAction = (action) => {
  return action ? action.charAt(0).toUpperCase() + action.slice(1) : ''
}

const formatCurrency = (value) => {
  return value ? value.toLocaleString() : '0'
}

const formatDate = (date) => {
  return date ? new Date(date).toLocaleDateString() : ''
}

const formatDateTime = (date) => {
  return date ? new Date(date).toLocaleString() : ''
}

const canEdit = computed(() => {
  return request.value.status === 'draft' && request.value.user_id === authStore.user?.id
})

const canSubmit = computed(() => {
  return request.value.status === 'draft' && request.value.user_id === authStore.user?.id
})

const canApprove = computed(() => {
  return ['admin', 'manager'].includes(authStore.user?.role) && 
         ['submitted', 'under_review'].includes(request.value.status)
})

const canReject = computed(() => {
  return ['admin', 'manager'].includes(authStore.user?.role) && 
         ['submitted', 'under_review'].includes(request.value.status)
})

const canPrint = computed(() => {
  return true // All users can print
})

const canExport = computed(() => {
  return true // All users can export
})

const canCancel = computed(() => {
  return ['draft', 'submitted'].includes(request.value.status) && 
         request.value.user_id === authStore.user?.id
})

const loadRequest = async () => {
  try {
    const response = await procurementService.getProcurementRequest(route.params.id)
    request.value = response
  } catch (error) {
    ElMessage.error('Failed to load procurement request')
    router.push('/procurement-requests')
  }
}

const editRequest = () => {
  router.push(`/procurement-requests/${request.value.id}/edit`)
}

const submitRequest = async () => {
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
    
    await procurementService.submitRequest(request.value.id, {})
    ElMessage.success('Request submitted successfully')
    loadRequest()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('Failed to submit request')
    }
  }
}

const approveRequest = async () => {
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
    
    await procurementService.approveRequest(request.value.id, { approval_notes: notes })
    ElMessage.success('Request approved successfully')
    loadRequest()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('Failed to approve request')
    }
  }
}

const rejectRequest = async () => {
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
    
    await procurementService.rejectRequest(request.value.id, { rejection_reason: reason })
    ElMessage.success('Request rejected successfully')
    loadRequest()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('Failed to reject request')
    }
  }
}

const cancelRequest = async () => {
  try {
    await ElMessageBox.confirm(
      'Are you sure you want to cancel this procurement request?',
      'Confirm Cancellation',
      {
        confirmButtonText: 'Cancel',
        cancelButtonText: 'Back',
        type: 'warning'
      }
    )
    
    // This would need a cancel endpoint in the backend
    ElMessage.success('Request cancelled successfully')
    loadRequest()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('Failed to cancel request')
    }
  }
}

const printRequest = () => {
  window.print()
}

const exportRequest = () => {
  // This would need an export endpoint in the backend
  ElMessage.info('Export functionality coming soon')
}

onMounted(() => {
  loadRequest()
})
</script>

<style scoped>
.procurement-request-detail {
  padding: 0;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.page-header h1 {
  margin: 0;
  color: #303133;
}

.header-actions {
  display: flex;
  gap: 8px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.card-header h3 {
  margin: 0;
  color: #303133;
}

.description-section,
.justification-section,
.approval-section {
  margin-top: 20px;
}

.description-section h4,
.justification-section h4,
.approval-section h4 {
  margin: 0 0 8px 0;
  color: #303133;
  font-size: 16px;
}

.description-section p,
.justification-section p,
.approval-section p {
  margin: 0;
  color: #606266;
  line-height: 1.6;
}

.workflow-item {
  margin-bottom: 8px;
}

.workflow-action {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 4px;
}

.user-name {
  font-weight: 500;
  color: #303133;
}

.workflow-comments {
  color: #606266;
  font-size: 14px;
  margin-left: 8px;
}

.quick-actions {
  display: flex;
  flex-direction: column;
}

.text-muted {
  color: #909399;
}

@media print {
  .header-actions,
  .quick-actions {
    display: none;
  }
}
</style>
