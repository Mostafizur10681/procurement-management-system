<template>
  <div class="vendors">
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
          <h1 class="main-title">Vendors</h1>
          <p class="subtitle">Manage supplier relationships and vendor information</p>
        </div>
        <button 
          v-if="canCreateVendor"
          class="create-button"
          @click="$router.push('/vendors/create')"
        >
          <el-icon><Plus /></el-icon>
          Add Vendor
        </button>
      </div>
      
      <div class="filters-section">
        <div class="filters-form">
          <div class="filter-group">
            <label class="filter-label">Status</label>
            <select v-model="filters.status" class="filter-select">
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="blacklisted">Blacklisted</option>
            </select>
          </div>
          
          <div class="filter-group">
            <label class="filter-label">Search</label>
            <div class="search-input-wrapper">
              <el-icon class="search-icon"><Search /></el-icon>
              <input
                v-model="filters.search"
                type="text"
                class="search-input"
                placeholder="Search vendors..."
              />
            </div>
          </div>
          
          <div class="filter-actions">
            <button class="search-button" @click="loadVendors">Search</button>
            <button class="reset-button" @click="resetFilters">Reset</button>
          </div>
        </div>
      </div>
    </div>

    <div class="vendors-card">
      <div class="table-container">
        <el-table :data="vendors" v-loading="loading" class="modern-table">
          <el-table-column prop="name" label="Vendor Name" min-width="200" />
          <el-table-column prop="contact_person" label="Contact Person" width="150" />
          <el-table-column prop="email" label="Email" width="200" />
          <el-table-column prop="phone" label="Phone" width="130" />
          <el-table-column prop="status" label="Status" width="120">
            <template #default="scope">
              <span class="status-tag" :class="`status-${scope.row.status}`">
                {{ formatStatus(scope.row.status) }}
              </span>
            </template>
          </el-table-column>
          <el-table-column prop="credit_limit" label="Credit Limit" width="120" align="right">
            <template #default="scope">
              <span class="credit-limit">${{ formatCurrency(scope.row.credit_limit) }}</span>
            </template>
          </el-table-column>
          <el-table-column label="Actions" width="200" fixed="right">
            <template #default="scope">
              <div class="action-buttons">
                <button class="action-button view" @click="viewVendor(scope.row)">
                  <el-icon><View /></el-icon>
                </button>
                <button 
                  v-if="canEditVendor"
                  class="action-button edit"
                  @click="editVendor(scope.row)"
                >
                  <el-icon><Edit /></el-icon>
                </button>
                <button 
                  v-if="canDeleteVendor(scope.row)"
                  class="action-button delete"
                  @click="deleteVendor(scope.row)"
                >
                  <el-icon><Delete /></el-icon>
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
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import procurementService from '@/services/procurement'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, Search, View, Edit, Delete } from '@element-plus/icons-vue'

const router = useRouter()
const authStore = useAuthStore()

const loading = ref(false)
const vendors = ref([])

const filters = reactive({
  status: '',
  search: ''
})

const pagination = reactive({
  current_page: 1,
  per_page: 10,
  total: 0
})

const canCreateVendor = computed(() => {
  return ['admin', 'manager'].includes(authStore.user?.role)
})

const canEditVendor = computed(() => {
  return ['admin', 'manager'].includes(authStore.user?.role)
})

const canDeleteVendor = (vendor) => {
  return ['admin', 'manager'].includes(authStore.user?.role) && 
         (!vendor.procurement_items || vendor.procurement_items.length === 0)
}

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

const loadVendors = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      ...filters
    }
    
    const response = await procurementService.getVendors(params)
    vendors.value = response.data || response
    pagination.total = response.total || vendors.value.length
    pagination.current_page = response.current_page || 1
    pagination.per_page = response.per_page || 10
  } catch (error) {
    ElMessage.error('Failed to load vendors')
  } finally {
    loading.value = false
  }
}

const resetFilters = () => {
  filters.status = ''
  filters.search = ''
  pagination.current_page = 1
  loadVendors()
}

const handleSizeChange = (size) => {
  pagination.per_page = size
  pagination.current_page = 1
  loadVendors()
}

const handleCurrentChange = (page) => {
  pagination.current_page = page
  loadVendors()
}

const viewVendor = (vendor) => {
  router.push(`/vendors/${vendor.id}`)
}

const editVendor = (vendor) => {
  router.push(`/vendors/${vendor.id}/edit`)
}

const deleteVendor = async (vendor) => {
  try {
    await ElMessageBox.confirm(
      `Are you sure you want to delete vendor "${vendor.name}"?`,
      'Confirm Deletion',
      {
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        type: 'warning'
      }
    )
    
    await procurementService.deleteVendor(vendor.id)
    ElMessage.success('Vendor deleted successfully')
    loadVendors()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('Failed to delete vendor')
    }
  }
}

onMounted(() => {
  loadVendors()
})
</script>

<style scoped>
.vendors {
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
  width: 120px;
  height: 120px;
  top: 8%;
  left: 3%;
  animation-delay: 0s;
}

.shape-2 {
  width: 160px;
  height: 160px;
  top: 65%;
  right: 3%;
  animation-delay: 3s;
}

.shape-3 {
  width: 90px;
  height: 90px;
  bottom: 8%;
  left: 25%;
  animation-delay: 6s;
}

.shape-4 {
  width: 140px;
  height: 140px;
  top: 12%;
  right: 30%;
  animation-delay: 9s;
}

.shape-5 {
  width: 70px;
  height: 70px;
  bottom: 15%;
  right: 20%;
  animation-delay: 12s;
}

.shape-6 {
  width: 100px;
  height: 100px;
  top: 45%;
  left: 45%;
  animation-delay: 15s;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0) rotate(0deg);
    opacity: 0.6;
  }
  25% {
    transform: translateY(-40px) rotate(90deg);
    opacity: 0.8;
  }
  50% {
    transform: translateY(0) rotate(180deg);
    opacity: 0.6;
  }
  75% {
    transform: translateY(40px) rotate(270deg);
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

.search-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 12px;
  color: #64748b;
  font-size: 16px;
}

.search-input {
  padding: 8px 12px 8px 40px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.9);
  color: #1e293b;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.3s ease;
  width: 250px;
}

.search-input:hover {
  border-color: rgba(255, 255, 255, 0.5);
  background: white;
}

.search-input:focus {
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

.vendors-card {
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

.status-active {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
}

.status-inactive {
  background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
  color: white;
}

.status-blacklisted {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
}

.credit-limit {
  font-weight: 600;
  color: #10b981;
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

.action-button.delete {
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
