<template>
  <div class="dashboard">
    <div class="floating-shapes">
      <div class="shape shape-1"></div>
      <div class="shape shape-2"></div>
      <div class="shape shape-3"></div>
      <div class="shape shape-4"></div>
      <div class="shape shape-5"></div>
      <div class="shape shape-6"></div>
    </div>
    
    <div class="dashboard-header">
      <div class="header-content">
        <h1 class="main-title">Dashboard</h1>
        <p class="welcome-text">Welcome back, <span class="user-name">{{ authStore.user?.name }}</span>! 👋</p>
      </div>
    </div>

    <el-row :gutter="24" class="stats-row">
      <el-col :span="6">
        <div class="stat-card">
          <div class="stat-content">
            <div class="stat-icon requests">
              <el-icon><Document /></el-icon>
            </div>
            <div class="stat-info">
              <h3 class="stat-card-value">{{ stats.totalRequests }}</h3>
              <p class="stat-card-title">Total Requests</p>
            </div>
          </div>
        </div>
      </el-col>
      
      <el-col :span="6">
        <div class="stat-card">
          <div class="stat-content">
            <div class="stat-icon pending">
              <el-icon><Clock /></el-icon>
            </div>
            <div class="stat-info">
              <h3 class="stat-card-value">{{ stats.pendingRequests }}</h3>
              <p class="stat-card-title">Pending</p>
            </div>
          </div>
        </div>
      </el-col>
      
      <el-col :span="6">
        <div class="stat-card">
          <div class="stat-content">
            <div class="stat-icon approved">
              <el-icon><Check /></el-icon>
            </div>
            <div class="stat-info">
              <h3 class="stat-card-value">{{ stats.approvedRequests }}</h3>
              <p class="stat-card-title">Approved</p>
            </div>
          </div>
        </div>
      </el-col>
      
      <el-col :span="6">
        <div class="stat-card">
          <div class="stat-content">
            <div class="stat-icon value">
              <el-icon><Money /></el-icon>
            </div>
            <div class="stat-info">
              <h3 class="stat-card-value">${{ formatCurrency(stats.totalValue) }}</h3>
              <p class="stat-card-title">Total Value</p>
            </div>
          </div>
        </div>
      </el-col>
    </el-row>

    <el-row :gutter="24" class="content-row">
      <el-col :span="16">
        <div class="chart-card">
          <div class="card-header">
            <h3 class="card-title">Recent Procurement Requests</h3>
          </div>
          
          <div class="table-container">
            <el-table :data="recentRequests" class="modern-table">
              <el-table-column prop="request_number" label="Request #" width="120" />
              <el-table-column prop="title" label="Title" min-width="200" />
              <el-table-column prop="department.name" label="Department" width="150" />
              <el-table-column prop="status" label="Status" width="120">
                <template #default="scope">
                  <span class="status-tag" :class="`status-${scope.row.status.toLowerCase()}`">
                    {{ scope.row.status }}
                  </span>
                </template>
              </el-table-column>
              <el-table-column prop="estimated_cost" label="Cost" width="120">
                <template #default="scope">
                  <span class="cost-value">${{ formatCurrency(scope.row.estimated_cost) }}</span>
                </template>
              </el-table-column>
              <el-table-column label="Actions" width="120">
                <template #default="scope">
                  <button
                    class="view-button"
                    @click="$router.push(`/procurement-requests/${scope.row.id}`)"
                  >
                    View
                  </button>
                </template>
              </el-table-column>
            </el-table>
          </div>
        </div>
      </el-col>
      
      <el-col :span="8">
        <div class="chart-card">
          <div class="card-header">
            <h3 class="card-title">Request Status Distribution</h3>
          </div>
          
          <div class="chart-container">
            <canvas ref="statusChart"></canvas>
          </div>
        </div>
      </el-col>
    </el-row>

    <el-row :gutter="24" class="content-row">
      <el-col :span="12">
        <div class="chart-card">
          <div class="card-header">
            <h3 class="card-title">Requests by Priority</h3>
          </div>
          
          <div class="chart-container">
            <canvas ref="priorityChart"></canvas>
          </div>
        </div>
      </el-col>
      
      <el-col :span="12">
        <div class="chart-card">
          <div class="card-header">
            <h3 class="card-title">Department Requests</h3>
          </div>
          
          <div class="chart-container">
            <canvas ref="departmentChart"></canvas>
          </div>
        </div>
      </el-col>
    </el-row>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { useAuthStore } from '@/stores/auth'
import procurementService from '@/services/procurement'
import { Chart, registerables } from 'chart.js'
import { Document, Clock, Check, Money } from '@element-plus/icons-vue'

Chart.register(...registerables)

const authStore = useAuthStore()

const stats = ref({
  totalRequests: 0,
  pendingRequests: 0,
  approvedRequests: 0,
  totalValue: 0
})

const recentRequests = ref([])

const statusChart = ref(null)
const priorityChart = ref(null)
const departmentChart = ref(null)

const formatCurrency = (value) => {
  return value ? value.toLocaleString() : '0'
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

const loadDashboardData = async () => {
  try {
    const [summaryData, requestsData] = await Promise.all([
      procurementService.getProcurementSummary(),
      procurementService.getProcurementRequests({ limit: 5 })
    ])

    stats.value = {
      totalRequests: summaryData.total_requests,
      pendingRequests: summaryData.pending_requests,
      approvedRequests: summaryData.approved_requests,
      totalValue: summaryData.total_value
    }

    recentRequests.value = requestsData.data

    await nextTick()
    createCharts(summaryData)
  } catch (error) {
    console.error('Error loading dashboard data:', error)
  }
}

const createCharts = (data) => {
  // Status Chart
  if (statusChart.value) {
    new Chart(statusChart.value, {
      type: 'doughnut',
      data: {
        labels: ['Pending', 'Approved', 'Rejected'],
        datasets: [{
          data: [
            data.pending_requests,
            data.approved_requests,
            data.rejected_requests
          ],
          backgroundColor: ['#E6A23C', '#67C23A', '#F56C6C']
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    })
  }

  // Priority Chart
  if (priorityChart.value && data.requests_by_priority) {
    new Chart(priorityChart.value, {
      type: 'bar',
      data: {
        labels: data.requests_by_priority.map(item => item.priority),
        datasets: [{
          label: 'Requests',
          data: data.requests_by_priority.map(item => item.count),
          backgroundColor: '#409EFF'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    })
  }

  // Department Chart
  if (departmentChart.value && data.requests_by_department) {
    new Chart(departmentChart.value, {
      type: 'pie',
      data: {
        labels: data.requests_by_department.map(item => item.name),
        datasets: [{
          data: data.requests_by_department.map(item => item.count),
          backgroundColor: [
            '#409EFF', '#67C23A', '#E6A23C', '#F56C6C', '#909399'
          ]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    })
  }
}

onMounted(() => {
  loadDashboardData()
})
</script>

<style scoped>
.dashboard {
  padding: 32px;
  background: #193478;
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

.dashboard-header {
  margin-bottom: 40px;
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
  text-align: center;
}

.main-title {
  font-size: 48px;
  font-weight: 700;
  color: #193478 !important;
  margin: 0 0 16px 0;
  letter-spacing: -1px;
  animation: titlePulse 3s ease-in-out infinite;
}

@keyframes titlePulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.02); }
}

.welcome-text {
  font-size: 20px;
  color: #333 !important;
  margin: 0;
  font-weight: 500;
}

.user-name {
  color: black !important;
  font-weight: 600;
}

.stats-row {
  margin-bottom: 32px;
}

.stat-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  padding: 24px;
  box-shadow: 
    0 20px 40px rgba(0, 0, 0, 0.1),
    0 10px 20px rgba(0, 0, 0, 0.05),
    inset 0 1px 0 rgba(255, 255, 255, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.2);
  position: relative;
  z-index: 10;
  transition: all 0.3s ease;
  animation: fadeInUp 0.6s ease-out backwards;
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }

.stat-card:hover {
  transform: translateY(-8px);
  box-shadow: 
    0 32px 64px rgba(0, 0, 0, 0.15),
    0 16px 32px rgba(0, 0, 0, 0.08),
    inset 0 1px 0 rgba(255, 255, 255, 0.6);
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

.stat-content {
  display: flex;
  align-items: center;
  gap: 16px;
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  color: white;
  position: relative;
  overflow: hidden;
}

.stat-icon::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  transition: left 0.8s ease;
}

.stat-card:hover .stat-icon::before {
  left: 100%;
}

.stat-icon.requests {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-icon.pending {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.stat-icon.approved {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stat-icon.value {
  background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.stat-info {
  flex: 1;
}

.stat-card-value {
  font-size: 32px;
  font-weight: 800;
  color: #1e293b;
  margin: 0 0 8px 0;
  line-height: 1;
}

.stat-card-title {
  font-size: 14px;
  color: #64748b;
  margin: 0;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.content-row {
  margin-bottom: 32px;
}

.chart-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  padding: 32px;
  box-shadow: 
    0 20px 40px rgba(0, 0, 0, 0.1),
    0 10px 20px rgba(0, 0, 0, 0.05),
    inset 0 1px 0 rgba(255, 255, 255, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.2);
  position: relative;
  z-index: 10;
  animation: fadeInUp 0.8s ease-out backwards;
}

.chart-card:nth-child(1) { animation-delay: 0.5s; }
.chart-card:nth-child(2) { animation-delay: 0.6s; }

.card-header {
  margin-bottom: 24px;
}

.card-title {
  font-size: 20px;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.table-container {
  border-radius: 12px;
  overflow: hidden;
}

.modern-table {
  background: white;
  border-radius: 12px;
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

.status-pending {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  color: white;
}

.status-approved {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  color: white;
}

.status-rejected {
  background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
  color: white;
}

.cost-value {
  font-weight: 600;
  color: #10b981;
}

.view-button {
  padding: 6px 16px;
  background: #163069 !important;
  color: white !important;
  border: none;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.view-button:hover {
  background: #193375 !important;
}

.chart-container {
  height: 300px;
  position: relative;
}
</style>
