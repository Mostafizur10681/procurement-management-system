<template>
  <div class="reports">
    <div class="page-header">
      <h1>Reports & Analytics</h1>
      <div class="date-range">
        <el-date-picker
          v-model="dateRange"
          type="daterange"
          range-separator="To"
          start-placeholder="Start date"
          end-placeholder="End date"
          @change="loadReports"
        />
        <el-button type="primary" @click="loadReports">
          <el-icon><Refresh /></el-icon>
          Refresh
        </el-button>
      </div>
    </div>

    <el-row :gutter="20" class="summary-cards">
      <el-col :span="6">
        <el-card class="summary-card">
          <div class="card-content">
            <div class="card-icon total">
              <el-icon><Document /></el-icon>
            </div>
            <div class="card-info">
              <h3>{{ summary.total_requests }}</h3>
              <p>Total Requests</p>
            </div>
          </div>
        </el-card>
      </el-col>
      
      <el-col :span="6">
        <el-card class="summary-card">
          <div class="card-content">
            <div class="card-icon pending">
              <el-icon><Clock /></el-icon>
            </div>
            <div class="card-info">
              <h3>{{ summary.pending_requests }}</h3>
              <p>Pending</p>
            </div>
          </div>
        </el-card>
      </el-col>
      
      <el-col :span="6">
        <el-card class="summary-card">
          <div class="card-content">
            <div class="card-icon approved">
              <el-icon><Check /></el-icon>
            </div>
            <div class="card-info">
              <h3>{{ summary.approved_requests }}</h3>
              <p>Approved</p>
            </div>
          </div>
        </el-card>
      </el-col>
      
      <el-col :span="6">
        <el-card class="summary-card">
          <div class="card-content">
            <div class="card-icon value">
              <el-icon><Money /></el-icon>
            </div>
            <div class="card-info">
              <h3>${{ formatCurrency(summary.total_value) }}</h3>
              <p>Total Value</p>
            </div>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <el-row :gutter="20" class="charts-row">
      <el-col :span="12">
        <el-card class="chart-card">
          <template #header>
            <h3>Request Status Distribution</h3>
          </template>
          <div class="chart-container">
            <canvas ref="statusChart"></canvas>
          </div>
        </el-card>
      </el-col>
      
      <el-col :span="12">
        <el-card class="chart-card">
          <template #header>
            <h3>Requests by Priority</h3>
          </template>
          <div class="chart-container">
            <canvas ref="priorityChart"></canvas>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <el-row :gutter="20" class="charts-row">
      <el-col :span="16">
        <el-card class="chart-card">
          <template #header>
            <h3>Requests by Department</h3>
          </template>
          <div class="chart-container">
            <canvas ref="departmentChart"></canvas>
          </div>
        </el-card>
      </el-col>
      
      <el-col :span="8">
        <el-card class="chart-card">
          <template #header>
            <h3>Approval Rate</h3>
          </template>
          <div class="approval-rate">
            <div class="rate-circle">
              <el-progress
                type="circle"
                :percentage="approvalRate"
                :width="120"
                :stroke-width="8"
              />
            </div>
            <div class="rate-details">
              <p>{{ summary.approved_requests }} of {{ summary.total_requests }} approved</p>
              <p>{{ summary.rejected_requests }} rejected</p>
            </div>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <el-row :gutter="20" class="tables-row">
      <el-col :span="12">
        <el-card class="table-card">
          <template #header>
            <h3>Vendor Performance</h3>
          </template>
          <el-table :data="vendorPerformance" style="width: 100%" max-height="400">
            <el-table-column prop="name" label="Vendor" min-width="150" />
            <el-table-column prop="procurement_items_count" label="Orders" width="80" align="center" />
            <el-table-column prop="total_value" label="Total Value" width="120" align="right">
              <template #default="scope">
                ${{ formatCurrency(scope.row.total_value) }}
              </template>
            </el-table-column>
            <el-table-column prop="average_order_value" label="Avg Order" width="120" align="right">
              <template #default="scope">
                ${{ formatCurrency(scope.row.average_order_value) }}
              </template>
            </el-table-column>
          </el-table>
        </el-card>
      </el-col>
      
      <el-col :span="12">
        <el-card class="table-card">
          <template #header>
            <h3>Budget Analysis by Department</h3>
          </template>
          <el-table :data="budgetAnalysis" style="width: 100%" max-height="400">
            <el-table-column prop="name" label="Department" min-width="120" />
            <el-table-column prop="budget_limit" label="Budget" width="100" align="right">
              <template #default="scope">
                ${{ formatCurrency(scope.row.budget_limit) }}
              </template>
            </el-table-column>
            <el-table-column prop="spent" label="Spent" width="100" align="right">
              <template #default="scope">
                ${{ formatCurrency(scope.row.spent) }}
              </template>
            </el-table-column>
            <el-table-column prop="percentage_used" label="Used" width="80" align="center">
              <template #default="scope">
                {{ scope.row.percentage_used.toFixed(1) }}%
              </template>
            </el-table-column>
            <el-table-column label="Status" width="100">
              <template #default="scope">
                <el-tag :type="getBudgetStatusType(scope.row.percentage_used)" size="small">
                  {{ getBudgetStatus(scope.row.percentage_used) }}
                </el-tag>
              </template>
            </el-table-column>
          </el-table>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, computed } from 'vue'
import procurementService from '@/services/procurement'
import { Chart, registerables } from 'chart.js'
import { Document, Clock, Check, Money, Refresh } from '@element-plus/icons-vue'

Chart.register(...registerables)

const loading = ref(false)
const dateRange = ref([
  new Date(new Date().getFullYear(), new Date().getMonth(), 1),
  new Date()
])

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

const statusChart = ref(null)
const priorityChart = ref(null)
const departmentChart = ref(null)

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
  if (percentage >= 50) return 'info'
  return 'success'
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

.date-range {
  display: flex;
  gap: 12px;
  align-items: center;
}

.summary-cards {
  margin-bottom: 24px;
}

.summary-card {
  height: 120px;
}

.card-content {
  display: flex;
  align-items: center;
  height: 100%;
}

.card-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 16px;
  font-size: 24px;
  color: white;
}

.card-icon.total {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card-icon.pending {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.card-icon.approved {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.card-icon.value {
  background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.card-info h3 {
  margin: 0 0 4px 0;
  font-size: 24px;
  font-weight: 600;
  color: #303133;
}

.card-info p {
  margin: 0;
  color: #909399;
  font-size: 14px;
}

.charts-row,
.tables-row {
  margin-bottom: 24px;
}

.chart-card,
.table-card {
  height: 400px;
}

.chart-card h3,
.table-card h3 {
  margin: 0;
  color: #303133;
}

.chart-container {
  height: 320px;
  position: relative;
}

.approval-rate {
  display: flex;
  align-items: center;
  justify-content: space-around;
  height: 320px;
}

.rate-circle {
  text-align: center;
}

.rate-details {
  text-align: center;
}

.rate-details p {
  margin: 8px 0;
  color: #606266;
}
</style>
