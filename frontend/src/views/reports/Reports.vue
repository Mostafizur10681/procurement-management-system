<template>
  <div class="reports">
    <div class="floating-shapes">
      <div class="shape shape-1"></div>
      <div class="shape shape-2"></div>
      <div class="shape shape-3"></div>
      <div class="shape shape-4"></div>
      <div class="shape shape-5"></div>
      <div class="shape shape-6"></div>
    </div>
    
    <!-- Header Section -->
    <div class="dashboard-header">
      <div class="header-content">
        <h1 class="main-title">Reports & Analytics</h1>
        <p class="welcome-text">Comprehensive procurement insights and performance metrics</p>
      </div>
      <div class="header-controls">
        <div class="date-range">
          <el-date-picker
            v-model="dateRange"
            type="daterange"
            range-separator="To"
            start-placeholder="Start date"
            end-placeholder="End date"
            @change="loadReports"
          />
        </div>
        <div class="control-buttons">
          <button class="view-button" @click="loadReports">
            <el-icon><Refresh /></el-icon>
            Refresh
          </button>
          <button class="view-button" @click="exportData">
            <el-icon><Download /></el-icon>
            Export
          </button>
        </div>
      </div>
    </div>

    <!-- Key Metrics Overview -->
    <div class="metrics-overview">
      <div class="section-title">
        <h2>Key Performance Metrics</h2>
        <div class="period-selector">
          <el-radio-group v-model="period" size="small" @change="loadReports">
            <el-radio-button label="today">Today</el-radio-button>
            <el-radio-button label="week">This Week</el-radio-button>
            <el-radio-button label="month">This Month</el-radio-button>
            <el-radio-button label="year">This Year</el-radio-button>
          </el-radio-group>
        </div>
      </div>
      
      <el-row :gutter="24" class="stats-row">
        <el-col :span="6">
          <div class="stat-card primary">
            <div class="stat-content">
              <div class="stat-icon requests">
                <el-icon><Document /></el-icon>
              </div>
              <div class="stat-info">
                <h3 class="stat-card-value">{{ summary.total_requests }}</h3>
                <p class="stat-card-title">Total Requests</p>
                <div class="stat-change positive">
                  <el-icon><TrendCharts /></el-icon>
                  <span>+12.5%</span>
                </div>
              </div>
            </div>
          </div>
      </el-col>
      
      <el-col :span="6">
        <div class="stat-card warning">
          <div class="stat-content">
            <div class="stat-icon pending">
              <el-icon><Clock /></el-icon>
            </div>
            <div class="stat-info">
              <h3 class="stat-card-value">{{ summary.pending_requests }}</h3>
              <p class="stat-card-title">Pending</p>
              <div class="stat-change negative">
                <el-icon><TrendCharts /></el-icon>
                <span>-3.2%</span>
              </div>
            </div>
          </div>
        </div>
      </el-col>
      
      <el-col :span="6">
        <div class="stat-card success">
          <div class="stat-content">
            <div class="stat-icon approved">
              <el-icon><Check /></el-icon>
            </div>
            <div class="stat-info">
              <h3 class="stat-card-value">{{ summary.approved_requests }}</h3>
              <p class="stat-card-title">Approved</p>
              <div class="stat-change positive">
                <el-icon><TrendCharts /></el-icon>
                <span>+8.7%</span>
              </div>
            </div>
          </div>
        </div>
      </el-col>
      
      <el-col :span="6">
        <div class="stat-card info">
          <div class="stat-content">
            <div class="stat-icon value">
              <el-icon><Money /></el-icon>
            </div>
            <div class="stat-info">
              <h3 class="stat-card-value">${{ formatCurrency(summary.total_value) }}</h3>
              <p class="stat-card-title">Total Value</p>
              <div class="stat-change positive">
                <el-icon><TrendCharts /></el-icon>
                <span>+15.3%</span>
              </div>
            </div>
          </div>
        </div>
      </el-col>
    </el-row>
    </div>

    <!-- Analytics Dashboard -->
    <div class="analytics-dashboard">
      <div class="section-title">
        <h2>Analytics Dashboard</h2>
        <div class="view-options">
          <el-select v-model="chartView" placeholder="Select view" size="small">
            <el-option label="Overview" value="overview" />
            <el-option label="Detailed" value="detailed" />
            <el-option label="Comparative" value="comparative" />
          </el-select>
        </div>
      </div>

      <el-row :gutter="24" class="charts-row">
        <el-col :span="16">
          <div class="chart-card large">
            <div class="chart-header">
              <h3>Request Trends Over Time</h3>
              <div class="chart-controls">
                <el-button-group size="small">
                  <el-button @click="changeChartType('line')">Line</el-button>
                  <el-button @click="changeChartType('bar')">Bar</el-button>
                  <el-button @click="changeChartType('area')">Area</el-button>
                </el-button-group>
              </div>
            </div>
            <div class="chart-container large">
              <canvas ref="trendsChart"></canvas>
            </div>
          </div>
        </el-col>
        
        <el-col :span="8">
          <div class="chart-card">
            <div class="chart-header">
              <h3>Request Status</h3>
            </div>
            <div class="chart-container">
              <canvas ref="statusChart"></canvas>
            </div>
          </div>
        </el-col>
      </el-row>

      <el-row :gutter="24" class="charts-row">
        <el-col :span="8">
          <div class="chart-card">
            <div class="chart-header">
              <h3>Priority Distribution</h3>
            </div>
            <div class="chart-container">
              <canvas ref="priorityChart"></canvas>
            </div>
          </div>
        </el-col>
        
        <el-col :span="8">
          <div class="chart-card">
            <div class="chart-header">
              <h3>Department Performance</h3>
            </div>
            <div class="chart-container">
              <canvas ref="departmentChart"></canvas>
            </div>
          </div>
        </el-col>
        
        <el-col :span="8">
          <div class="chart-card">
            <div class="chart-header">
              <h3>Vendor Analysis</h3>
            </div>
            <div class="chart-container">
              <canvas ref="vendorChart"></canvas>
            </div>
          </div>
        </el-col>
      </el-row>
    </div>

    <!-- Data Tables Section -->
    <div class="data-section">
      <div class="section-title">
        <h2>Detailed Reports</h2>
        <div class="table-controls">
          <el-input
            v-model="searchQuery"
            placeholder="Search requests..."
            prefix-icon="Search"
            size="small"
            style="width: 200px"
          />
          <el-select v-model="statusFilter" placeholder="Filter by status" size="small" style="width: 150px">
            <el-option label="All Status" value="" />
            <el-option label="Pending" value="pending" />
            <el-option label="Approved" value="approved" />
            <el-option label="Rejected" value="rejected" />
          </el-select>
        </div>
      </div>

      <div class="table-card">
        <el-table :data="filteredRequests" v-loading="loading" stripe>
          <el-table-column prop="request_number" label="Request #" width="120" />
          <el-table-column prop="title" label="Title" min-width="200" />
          <el-table-column prop="department.name" label="Department" width="150" />
          <el-table-column prop="status" label="Status" width="100">
            <template #default="scope">
              <el-tag :type="getStatusType(scope.row.status)">
                {{ scope.row.status }}
              </el-tag>
            </template>
          </el-table-column>
          <el-table-column prop="priority" label="Priority" width="100">
            <template #default="scope">
              <el-tag :type="getPriorityType(scope.row.priority)">
                {{ scope.row.priority }}
              </el-tag>
            </template>
          </el-table-column>
          <el-table-column prop="estimated_cost" label="Cost" width="120">
            <template #default="scope">
              <span class="cost-value">${{ formatCurrency(scope.row.estimated_cost) }}</span>
            </template>
          </el-table-column>
          <el-table-column prop="created_at" label="Created" width="120">
            <template #default="scope">
              {{ formatDate(scope.row.created_at) }}
            </template>
          </el-table-column>
          <el-table-column label="Actions" width="100">
            <template #default="scope">
              <button class="view-button" @click="viewDetails(scope.row.id)">
                View
              </button>
            </template>
          </el-table-column>
        </el-table>
        
        <div class="table-pagination">
          <el-pagination
            v-model:current-page="currentPage"
            v-model:page-size="pageSize"
            :page-sizes="[10, 20, 50, 100]"
            :total="totalRequests"
            layout="total, sizes, prev, pager, next, jumper"
            @size-change="handleSizeChange"
            @current-change="handleCurrentChange"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, computed } from 'vue'
import procurementService from '@/services/procurement'
import { Chart, registerables } from 'chart.js'
import { Document, Clock, Check, Money, Refresh, TrendCharts, Download } from '@element-plus/icons-vue'

Chart.register(...registerables)

const loading = ref(false)
const dateRange = ref([
  new Date(new Date().getFullYear(), new Date().getMonth(), 1),
  new Date()
])

// New reactive variables for enhanced layout
const period = ref('month')
const chartView = ref('overview')
const searchQuery = ref('')
const statusFilter = ref('')
const currentPage = ref(1)
const pageSize = ref(20)
const totalRequests = ref(0)

// Chart references
const trendsChart = ref(null)
const statusChart = ref(null)
const priorityChart = ref(null)
const departmentChart = ref(null)
const vendorChart = ref(null)

const summary = ref({
  total_requests: 0,
  pending_requests: 0,
  approved_requests: 0,
  rejected_requests: 0,
  total_value: 0,
  requests_by_priority: [],
  requests_by_department: []
})

const vendorPerformance = ref([])
const budgetAnalysis = ref([])

const approvalRate = computed(() => {
  if (summary.value.total_requests === 0) return 0
  return Math.round((summary.value.approved_requests / summary.value.total_requests) * 100)
})

const formatCurrency = (value) => {
  return value ? value.toLocaleString() : '0'
}

const getBudgetStatus = (percentage) => {
  if (percentage >= 90) return 'Critical'
  if (percentage >= 75) return 'Warning'
  if (percentage >= 50) return 'Normal'
  return 'Good'
}

const getBudgetStatusType = (percentage) => {
  if (percentage >= 90) return 'danger'
  if (percentage >= 75) return 'warning'
  return 'success'
}

const getStatusType = (status) => {
  const types = {
    pending: 'warning',
    approved: 'success',
    rejected: 'danger'
  }
  return types[status] || 'info'
}

const getPriorityType = (priority) => {
  const types = {
    high: 'danger',
    medium: 'warning',
    low: 'success'
  }
  return types[priority] || 'info'
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const filteredRequests = computed(() => {
  let filtered = vendorPerformance.value
  if (searchQuery.value) {
    filtered = filtered.filter(req => 
      req.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      req.request_number.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }
  if (statusFilter.value) {
    filtered = filtered.filter(req => req.status === statusFilter.value)
  }
  return filtered
})

const handleSizeChange = (size) => {
  pageSize.value = size
  loadReports()
}

const handleCurrentChange = (page) => {
  currentPage.value = page
  loadReports()
}

const viewDetails = (id) => {
  // Navigate to request details
  console.log('View details for:', id)
}

const exportData = () => {
  // Export functionality
  console.log('Export data')
}

const changeChartType = (type) => {
  // Change chart type functionality
  console.log('Change chart type to:', type)
}

const loadReports = async () => {
  loading.value = true
  try {
    const [summaryData, vendorData, budgetData] = await Promise.all([
      procurementService.getProcurementSummary({
        start_date: dateRange.value[0]?.toISOString().split('T')[0],
        end_date: dateRange.value[1]?.toISOString().split('T')[0]
      }),
      procurementService.getVendorPerformance({
        start_date: dateRange.value[0]?.toISOString().split('T')[0],
        end_date: dateRange.value[1]?.toISOString().split('T')[0]
      }),
      procurementService.getBudgetAnalysis({
        start_date: dateRange.value[0]?.toISOString().split('T')[0],
        end_date: dateRange.value[1]?.toISOString().split('T')[0]
      })
    ])

    summary.value = summaryData
    vendorPerformance.value = vendorData
    budgetAnalysis.value = budgetData

    await nextTick()
    createCharts()
  } catch (error) {
    console.error('Error loading reports:', error)
  } finally {
    loading.value = false
  }
}

const createCharts = () => {
  // Status Chart
  if (statusChart.value) {
    new Chart(statusChart.value, {
      type: 'doughnut',
      data: {
        labels: ['Pending', 'Approved', 'Rejected'],
        datasets: [{
          data: [
            summary.value.pending_requests,
            summary.value.approved_requests,
            summary.value.rejected_requests
          ],
          backgroundColor: ['#E6A23C', '#67C23A', '#F56C6C']
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    })
  }

  // Priority Chart
  if (priorityChart.value && summary.value.requests_by_priority) {
    new Chart(priorityChart.value, {
      type: 'bar',
      data: {
        labels: summary.value.requests_by_priority.map(item => item.priority),
        datasets: [{
          label: 'Requests',
          data: summary.value.requests_by_priority.map(item => item.count),
          backgroundColor: ['#409EFF', '#E6A23C', '#F56C6C', '#909399']
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    })
  }

  // Department Chart
  if (departmentChart.value && summary.value.requests_by_department) {
    new Chart(departmentChart.value, {
      type: 'pie',
      data: {
        labels: summary.value.requests_by_department.map(item => item.name),
        datasets: [{
          data: summary.value.requests_by_department.map(item => item.count),
          backgroundColor: [
            '#409EFF', '#67C23A', '#E6A23C', '#F56C6C', '#909399',
            '#B37FEB', '#FFB800', '#FF6B6B', '#4ECDC4', '#45B7D1'
          ]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'right'
          }
        }
      }
    })
  }
}

onMounted(() => {
  loadReports()
})
</script>

<style scoped>
.reports {
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
  overflow: hidden;
  z-index: 0;
}

.shape {
  position: absolute;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
  animation: float 6s ease-in-out infinite;
}

.shape-1 {
  width: 80px;
  height: 80px;
  top: 10%;
  left: 10%;
  animation-delay: 0s;
}

.shape-2 {
  width: 120px;
  height: 120px;
  top: 70%;
  right: 10%;
  animation-delay: 2s;
}

.shape-3 {
  width: 60px;
  height: 60px;
  bottom: 10%;
  left: 30%;
  animation-delay: 4s;
}

.shape-4 {
  width: 100px;
  height: 100px;
  top: 50%;
  right: 30%;
  animation-delay: 1s;
}

.shape-5 {
  width: 40px;
  height: 40px;
  top: 20%;
  right: 20%;
  animation-delay: 3s;
}

.shape-6 {
  width: 70px;
  height: 70px;
  bottom: 30%;
  right: 15%;
  animation-delay: 5s;
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
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 32px;
  position: relative;
  z-index: 10;
}

.header-content {
  flex: 1;
}

.header-controls {
  display: flex;
  align-items: center;
  gap: 16px;
}

.control-buttons {
  display: flex;
  gap: 8px;
}

.main-title {
  font-size: 24px;
  font-weight: 600;
  color: #fff !important;
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
  color: #fff !important;
  margin: 0;
  font-weight: 500;
}

.date-range {
  display: flex;
  gap: 12px;
  align-items: center;
  position: relative;
  z-index: 10;
}

/* Section Styles */
.metrics-overview,
.analytics-dashboard,
.data-section {
  margin-bottom: 40px;
  position: relative;
  z-index: 10;
}

.section-title {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.section-title h2 {
  font-size: 24px;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
}

.period-selector,
.view-options,
.table-controls {
  display: flex;
  gap: 12px;
  align-items: center;
}

/* Enhanced Stat Cards */
.stat-card.primary {
  border-left: 4px solid #667eea;
}

.stat-card.warning {
  border-left: 4px solid #f093fb;
}

.stat-card.success {
  border-left: 4px solid #4facfe;
}

.stat-card.info {
  border-left: 4px solid #43e97b;
}

.stat-change {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  font-weight: 600;
  margin-top: 8px;
}

.stat-change.positive {
  color: #10b981;
}

.stat-change.negative {
  color: #ef4444;
}

/* Chart Cards */
.chart-card.large {
  height: 400px;
}

.chart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.chart-header h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
}

.chart-controls {
  display: flex;
  gap: 8px;
}

.chart-container.large {
  height: 320px;
}

/* Data Table Styles */
.table-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 16px;
  padding: 24px;
  border: none;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  animation: slideUp 0.8s ease-out;
}

.cost-value {
  font-weight: 600;
  color: #10b981;
}

.table-pagination {
  margin-top: 20px;
  display: flex;
  justify-content: center;
}

.stats-row {
  margin-bottom: 32px;
  position: relative;
  z-index: 10;
}

.stat-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 16px;
  padding: 24px;
  height: 120px;
  display: flex;
  align-items: center;
  border: none;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  animation: slideUp 0.6s ease-out;
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }

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

.stat-content {
  display: flex;
  align-items: center;
  width: 100%;
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 20px;
  font-size: 24px;
  color: white;
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
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}

.stat-card-title {
  color: #64748b;
  margin: 0;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.charts-row {
  margin-bottom: 32px;
  position: relative;
  z-index: 10;
}

.chart-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 16px;
  padding: 24px;
  border: none;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  animation: slideUp 0.8s ease-out;
}

.chart-header {
  margin-bottom: 20px;
}

.chart-header h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
}

.chart-container {
  height: 300px;
  position: relative;
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
</style>
