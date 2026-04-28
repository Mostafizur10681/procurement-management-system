<template>
  <div class="vendor-detail">
    <div class="page-header">
      <h1>Vendor Details</h1>
      <div class="header-actions">
        <el-button @click="$router.back()">
          <el-icon><ArrowLeft /></el-icon>
          Back
        </el-button>
        <el-button
          v-if="canEditVendor"
          type="primary"
          @click="editVendor"
        >
          <el-icon><Edit /></el-icon>
          Edit
        </el-button>
        <el-button
          v-if="canDeleteVendor"
          type="danger"
          @click="deleteVendor"
        >
          <el-icon><Delete /></el-icon>
          Delete
        </el-button>
      </div>
    </div>

    <el-row :gutter="20">
      <el-col :span="16">
        <el-card>
          <template #header>
            <div class="card-header">
              <h3>{{ vendor.name }}</h3>
              <el-tag :type="getStatusType(vendor.status)">
                {{ formatStatus(vendor.status) }}
              </el-tag>
            </div>
          </template>

          <el-descriptions :column="2" border>
            <el-descriptions-item label="Contact Person">
              {{ vendor.contact_person || '-' }}
            </el-descriptions-item>
            <el-descriptions-item label="Email">
              {{ vendor.email || '-' }}
            </el-descriptions-item>
            <el-descriptions-item label="Phone">
              {{ vendor.phone || '-' }}
            </el-descriptions-item>
            <el-descriptions-item label="Tax ID">
              {{ vendor.tax_id || '-' }}
            </el-descriptions-item>
            <el-descriptions-item label="Credit Limit" span="2">
              <strong>${{ formatCurrency(vendor.credit_limit) }}</strong>
            </el-descriptions-item>
            <el-descriptions-item label="Address" span="2">
              {{ vendor.address || '-' }}
            </el-descriptions-item>
          </el-descriptions>

          <div v-if="vendor.notes" class="notes-section">
            <h4>Notes</h4>
            <p>{{ vendor.notes }}</p>
          </div>
        </el-card>

        <el-card style="margin-top: 20px">
          <template #header>
            <h3>Procurement History</h3>
          </template>

          <el-table :data="vendor.procurement_items" style="width: 100%">
            <el-table-column label="Request #" width="140">
              <template #default="scope">
                <router-link
                  :to="`/procurement-requests/${scope.row.procurement_request_id}`"
                  class="request-link"
                >
                  {{ getRequestNumber(scope.row) }}
                </router-link>
              </template>
            </el-table-column>
            <el-table-column prop="item_name" label="Item Name" min-width="200" />
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
            <el-table-column label="Date" width="120">
              <template #default="scope">
                {{ formatDate(scope.row.created_at) }}
              </template>
            </el-table-column>
          </el-table>

          <div v-if="!vendor.procurement_items || vendor.procurement_items.length === 0" class="no-data">
            <p>No procurement history found</p>
          </div>
        </el-card>
      </el-col>

      <el-col :span="8">
        <el-card>
          <template #header>
            <h3>Vendor Statistics</h3>
          </template>

          <div class="stats-grid">
            <div class="stat-item">
              <div class="stat-value">{{ vendor.procurement_items?.length || 0 }}</div>
              <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-item">
              <div class="stat-value">${{ formatCurrency(totalValue) }}</div>
              <div class="stat-label">Total Value</div>
            </div>
            <div class="stat-item">
              <div class="stat-value">${{ formatCurrency(averageOrderValue) }}</div>
              <div class="stat-label">Avg Order Value</div>
            </div>
          </div>
        </el-card>

        <el-card style="margin-top: 20px">
          <template #header>
            <h3>Quick Actions</h3>
          </template>

          <div class="quick-actions">
            <el-button
              type="primary"
              plain
              style="width: 100%; margin-bottom: 10px"
              @click="createProcurementRequest"
            >
              <el-icon><Plus /></el-icon>
              Create Procurement Request
            </el-button>
            
            <el-button
              type="success"
              plain
              style="width: 100%; margin-bottom: 10px"
              @click="printVendor"
            >
              <el-icon><Printer /></el-icon>
              Print Vendor Info
            </el-button>

            <el-button
              v-if="canChangeStatus"
              type="warning"
              plain
              style="width: 100%"
              @click="changeStatus"
            >
              <el-icon><Setting /></el-icon>
              Change Status
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
import { ArrowLeft, Edit, Delete, Plus, Printer, Setting } from '@element-plus/icons-vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const vendor = ref({})

const formatStatus = (status) => {
  return status ? status.charAt(0).toUpperCase() + status.slice(1) : ''
}

const getStatusType = (status) => {
  const types = {
    active: 'success',
    inactive: 'warning',
    blacklisted: 'danger'
  }
  return types[status] || 'info'
}

const formatCurrency = (value) => {
  return value ? value.toLocaleString() : '0'
}

const formatDate = (date) => {
  return date ? new Date(date).toLocaleDateString() : ''
}

const totalValue = computed(() => {
  if (!vendor.value.procurement_items) return 0
  return vendor.value.procurement_items.reduce((total, item) => total + (item.total_price || 0), 0)
})

const averageOrderValue = computed(() => {
  if (!vendor.value.procurement_items || vendor.value.procurement_items.length === 0) return 0
  return totalValue.value / vendor.value.procurement_items.length
})

const canEditVendor = computed(() => {
  return ['admin', 'manager'].includes(authStore.user?.role)
})

const canDeleteVendor = computed(() => {
  return ['admin', 'manager'].includes(authStore.user?.role) && 
         (!vendor.value.procurement_items || vendor.value.procurement_items.length === 0)
})

const canChangeStatus = computed(() => {
  return ['admin', 'manager'].includes(authStore.user?.role)
})

const getRequestNumber = (item) => {
  // This would need to be populated from the backend
  return `PR-${item.procurement_request_id}`
}

const loadVendor = async () => {
  try {
    const response = await procurementService.getVendor(route.params.id)
    vendor.value = response
  } catch (error) {
    ElMessage.error('Failed to load vendor details')
    router.push('/vendors')
  }
}

const editVendor = () => {
  router.push(`/vendors/${vendor.value.id}/edit`)
}

const deleteVendor = async () => {
  try {
    await ElMessageBox.confirm(
      `Are you sure you want to delete vendor "${vendor.value.name}"?`,
      'Confirm Deletion',
      {
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        type: 'warning'
      }
    )
    
    await procurementService.deleteVendor(vendor.value.id)
    ElMessage.success('Vendor deleted successfully')
    router.push('/vendors')
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('Failed to delete vendor')
    }
  }
}

const createProcurementRequest = () => {
  router.push('/procurement-requests/create')
}

const printVendor = () => {
  window.print()
}

const changeStatus = async () => {
  const options = ['active', 'inactive', 'blacklisted']
  const currentIndex = options.indexOf(vendor.value.status)
  
  try {
    const { value: newStatus } = await ElMessageBox.prompt(
      'Select new status',
      'Change Vendor Status',
      {
        confirmButtonText: 'Update',
        cancelButtonText: 'Cancel',
        inputType: 'select',
        inputOptions: options.map(status => ({
          label: formatStatus(status),
          value: status
        })),
        inputValue: vendor.value.status
      }
    )
    
    await procurementService.updateVendor(vendor.value.id, { status: newStatus })
    ElMessage.success('Vendor status updated successfully')
    loadVendor()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('Failed to update vendor status')
    }
  }
}

onMounted(() => {
  loadVendor()
})
</script>

<style scoped>
.vendor-detail {
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

.notes-section {
  margin-top: 20px;
}

.notes-section h4 {
  margin: 0 0 8px 0;
  color: #303133;
  font-size: 16px;
}

.notes-section p {
  margin: 0;
  color: #606266;
  line-height: 1.6;
}

.stats-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 16px;
}

.stat-item {
  text-align: center;
  padding: 16px;
  background: #f8f9fa;
  border-radius: 8px;
}

.stat-value {
  font-size: 24px;
  font-weight: 600;
  color: #409EFF;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 14px;
  color: #606266;
}

.quick-actions {
  display: flex;
  flex-direction: column;
}

.request-link {
  color: #409EFF;
  text-decoration: none;
}

.request-link:hover {
  text-decoration: underline;
}

.no-data {
  text-align: center;
  padding: 40px;
  color: #909399;
}

@media print {
  .header-actions,
  .quick-actions {
    display: none;
  }
}
</style>
