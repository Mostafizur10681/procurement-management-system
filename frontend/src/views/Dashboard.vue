<template>
  <section id="dashboard-analytics">
          <!-- Welcome Card -->
          <div class="row">
            <div class="col-md-12 my-4">
              <div class="card">
                <div class="card-content">
                  <div class="card-body">
                    <div class="text-center">
                      <h3 class="text-dark text-bold-600" style="color: #122B5A; font-size: 28px;">Welcome To Procurement Management System</h3>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Stats Cards -->
          <div class="row">
            <div class="col-md-6 col-xl-4 mb-4">
              <div class="widget-rounded-circle card">
                <div class="card-body">
                  <div class="row">
                    <div class="col-5">
                      <div class="avatar-lg rounded-circle bg-soft-primary border-primary border">
                        <i class="fas fa-shopping-cart font-22 avatar-title text-primary"></i>
                      </div>
                    </div>
                    <div class="col-7">
                      <div class="text-start">
                        <h3><span data-plugin="counterup" class="text-dark">{{ stats.totalRequisition }}</span></h3>
                        <p class="text-muted mb-1 text-truncate text-dark">Total Requisition</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6 col-xl-4">
              <div class="widget-rounded-circle card mb-4">
                <div class="card-body">
                  <div class="row">
                    <div class="col-5">
                      <div class="avatar-lg rounded-circle bg-soft-info border-info border">
                        <i class="fas fa-file-invoice font-22 avatar-title text-info"></i>
                      </div>
                    </div>
                    <div class="col-7">
                      <div class="text-start">
                        <h3 class="text-dark"><span data-plugin="counterup" class="text-dark">{{ stats.totalPO }}</span></h3>
                        <p class="text-muted mb-1 text-truncate text-dark">Total PO</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6 col-xl-4">
              <div class="widget-rounded-circle card mb-4">
                <div class="card-body">
                  <div class="row">
                    <div class="col-5">
                      <div class="avatar-lg rounded-circle bg-soft-info border-info border">
                        <i class="fas fa-box font-22 avatar-title text-info"></i>
                      </div>
                    </div>
                    <div class="col-7">
                      <div class="text-start">
                        <h3 class="text-dark"><span data-plugin="counterup" class="text-dark">{{ stats.totalReceive }}</span></h3>
                        <p class="text-muted mb-1 text-truncate text-dark">Total Receive</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Charts Section -->
          <div class="row mb-4">
            <div class="col-md-12">
              <div class="card">
                <div class="card-content">
                  <div class="card-body">
                    <div class="row p-2">
                      <div class="col-md-12 col-lg-5 d-flex pt-4 pl-2">
                        <div>
                          <div class="pieID pie" ref="pieChart"></div>
                          <div class="px-2 chart-title">
                            <p class="text-dark">Purchase Order</p>
                          </div>
                        </div>
                        <div>
                          <ul>
                            <li>
                              <em class="text-dark">Goods&nbsp;</em>
                              <span class="text-dark">{{ chartData.goods }}</span>
                            </li>
                            <li>
                              <em class="text-dark">Works &nbsp;</em>
                              <span class="text-dark">{{ chartData.works }}</span>
                            </li>
                            <li>
                              <em class="text-dark">Service &nbsp;</em>
                              <span class="text-dark">{{ chartData.service }}</span>
                            </li>
                          </ul>
                        </div>
                      </div>
                      <div class="col-md-12 col-lg-7">
                        <section class="charts_bar">
                          <article class="chart_section">
                            <div class="chart_title text-dark">Purchase Requisition</div>
                            <ul :data-baseline="getMaxValue('requisition')">
                              <li :data-value="chartData.goods" data-label="Goods">
                                <i></i>
                              </li>
                              <li :data-value="chartData.works" data-label="Works">
                                <i></i>
                              </li>
                              <li :data-value="chartData.service" data-label="Service">
                                <i></i>
                              </li>
                            </ul>
                          </article>
                          <article class="chart_section">
                            <div class="chart_title text-dark">Purchase Order</div>
                            <ul :data-baseline="getMaxValue('order')">
                              <li :data-value="chartData.goods" data-label="Goods">
                                <i></i>
                              </li>
                              <li :data-value="chartData.works" data-label="Works">
                                <i></i>
                              </li>
                              <li :data-value="chartData.service" data-label="Service">
                                <i></i>
                              </li>
                            </ul>
                          </article>
                          <article class="chart_section">
                            <div class="chart_title text-dark">Purchase Receive</div>
                            <ul :data-baseline="getMaxValue('receive')">
                              <li :data-value="chartData.goods" data-label="Goods">
                                <i></i>
                              </li>
                              <li :data-value="chartData.works" data-label="Works">
                                <i></i>
                              </li>
                              <li :data-value="chartData.service" data-label="Service">
                                <i></i>
                              </li>
                            </ul>
                          </article>

                          <div class="chart_key">
                            <ul>
                              <li class="text-dark">Goods</li>
                              <li class="text-dark">Works</li>
                              <li class="text-dark">Service</li>
                            </ul>
                          </div>
                        </section>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Procurement List Table -->
          <div class="card mb-5">
            <div class="card-body">
              <h4 class="card-title text-bold-600">Procurement List</h4>
              <div class="table-responsive">
                <table class="table table-sm datatable mdl-data-table dataTable procurement-list-dashboard table-responsive">
                  <thead>
                    <tr>
                      <th>SL</th>
                      <th class="text-left" style="width: 10%">File Number</th>
                      <th class="text-left" style="width: 15%">Item Name</th>
                      <th class="text-center" style="width: 6%">Procurement Method</th>
                      <th class="text-center" style="width: 6%">Procurement Type</th>
                      <th class="text-center" style="width: 10%">Vendor Name</th>
                      <th class="text-center" style="width: 6%">Requisition Status</th>
                      <th class="text-center" style="width: 6%">Price Estimation Status</th>
                      <th class="text-center" style="width: 6%">Vendor Selection Status</th>
                      <th class="text-center" style="width: 5%">PO Submit YN</th>
                      <th class="text-center" style="width: 5%">PO Number</th>
                      <th class="text-center" style="width: 5%">Invoice Number</th>
                      <th class="text-center" style="width: 10%">Receive Status</th>
                      <th class="text-center" style="width: 5%">Send to Account</th>
                      <th class="text-center" style="width: 5%">Bill Processing Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in procurementList" :key="index">
                      <td class="text-center">{{ index + 1 }}</td>
                      <td class="text-left" style="width: 10%">{{ item.fileNumber }}</td>
                      <td class="text-left" style="width: 15%">{{ item.itemName }}</td>
                      <td class="text-center" style="width: 6%">{{ item.procurementMethod }}</td>
                      <td class="text-center" style="width: 6%">{{ item.procurementType }}</td>
                      <td class="text-left" style="width: 10%">{{ item.vendorName }}</td>
                      <td class="text-center" style="width: 6%">
                        <span :class="getStatusBadgeClass(item.requisitionStatus)">
                          {{ item.requisitionStatus }}
                        </span>
                      </td>
                      <td class="text-center" style="width: 6%">
                        <span :class="getStatusBadgeClass(item.priceEstimationStatus)">
                          {{ item.priceEstimationStatus }}
                        </span>
                      </td>
                      <td class="text-center" style="width: 6%">
                        <span :class="getStatusBadgeClass(item.vendorSelectionStatus)">
                          {{ item.vendorSelectionStatus }}
                        </span>
                      </td>
                      <td class="text-center" style="width: 5%">
                        <span :class="getStatusBadgeClass(item.poSubmitYn)">
                          {{ item.poSubmitYn }}
                        </span>
                      </td>
                      <td class="text-left" style="width: 5%">{{ item.poNumber }}</td>
                      <td class="text-left" style="width: 5%">{{ item.invoiceNumber }}</td>
                      <td class="text-center" style="width: 10%">
                        <span :class="getStatusBadgeClass(item.receiveStatus)">
                          {{ item.receiveStatus }}
                        </span>
                      </td>
                      <td class="text-center" style="width: 5%">
                        <span :class="getStatusBadgeClass(item.sendToAccount)">
                          {{ item.sendToAccount }}
                        </span>
                      </td>
                      <td class="text-center" style="width: 5%">
                        <span :class="getStatusBadgeClass(item.billProcessingStatus)">
                          {{ item.billProcessingStatus }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </section>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

// Stats data
const stats = ref({
  totalRequisition: 156,
  totalPO: 89,
  totalReceive: 67
})

// Chart data
const chartData = ref({
  goods: 45,
  works: 32,
  service: 23
})

// Procurement list data
const procurementList = ref([
  {
    fileNumber: 'PR-2024-001',
    itemName: 'Office Equipment',
    procurementMethod: 'Direct Purchase',
    procurementType: 'Goods',
    vendorName: 'ABC Supplier Ltd',
    requisitionStatus: 'Approve',
    priceEstimationStatus: 'Approve',
    vendorSelectionStatus: 'YES',
    poSubmitYn: 'YES',
    poNumber: 'PO-2024-001',
    invoiceNumber: 'INV-2024-001',
    receiveStatus: 'Received',
    sendToAccount: 'Send',
    billProcessingStatus: 'Process Complete'
  },
  {
    fileNumber: 'PR-2024-002',
    itemName: 'Software Licenses',
    procurementMethod: 'Tender',
    procurementType: 'Service',
    vendorName: 'Tech Solutions Inc',
    requisitionStatus: 'Pending',
    priceEstimationStatus: 'Pending',
    vendorSelectionStatus: 'NO',
    poSubmitYn: 'NO',
    poNumber: '',
    invoiceNumber: '',
    receiveStatus: 'Pending',
    sendToAccount: 'Pending',
    billProcessingStatus: 'Processing'
  },
  {
    fileNumber: 'PR-2024-003',
    itemName: 'Construction Work',
    procurementMethod: 'Quotation',
    procurementType: 'Works',
    vendorName: 'BuildRight Construction',
    requisitionStatus: 'Approve',
    priceEstimationStatus: 'Approve',
    vendorSelectionStatus: 'YES',
    poSubmitYn: 'YES',
    poNumber: 'PO-2024-003',
    invoiceNumber: 'INV-2024-003',
    receiveStatus: 'Pending',
    sendToAccount: 'Pending',
    billProcessingStatus: 'Processing'
  }
])

// Functions
const getMaxValue = (type) => {
  const values = {
    requisition: Math.max(chartData.value.goods, chartData.value.works, chartData.value.service),
    order: Math.max(chartData.value.goods, chartData.value.works, chartData.value.service),
    receive: Math.max(chartData.value.goods, chartData.value.works, chartData.value.service)
  }
  return values[type] || 100
}

const getStatusBadgeClass = (status) => {
  if (status === 'Approve' || status === 'YES' || status === 'Send' || status === 'Received' || status === 'Process Complete') {
    return 'badge badge-success'
  }
  return 'badge badge-warning'
}

onMounted(() => {
  // Initialize dashboard
  initializeCharts()
})

const initializeCharts = () => {
  // Initialize pie chart and bar charts
  nextTick(() => {
    createPieChart()
    updateBarCharts()
  })
}

const createPieChart = () => {
  // Pie chart logic would go here
  console.log('Pie chart initialized')
}

const updateBarCharts = () => {
  // Update bar charts based on data
  const chartSections = document.querySelectorAll('.chart_section ul')
  chartSections.forEach(chart => {
    const baseline = parseInt(chart.dataset.baseline)
    const bars = chart.querySelectorAll('li')
    bars.forEach(bar => {
      const value = parseInt(bar.dataset.value)
      const percentage = (value / baseline) * 100
      const barElement = bar.querySelector('i')
      if (barElement) {
        barElement.style.flex = `0 0 ${percentage}%`
      }
    })
  })
}
</script>

<style scoped>
/* Laravel Dashboard Styles */
@keyframes bake-pie {
  from {
    transform: rotate(0deg) translate3d(0, 0, 0);
  }
}

body {
  font-family: "Open Sans", Arial;
  background: #EEE;
}

main {
  width: 400px;
  margin: 30px auto;
}

section {
  margin-top: 30px;
}

.pieID {
  display: inline-block;
  vertical-align: top;
}

.pie {
  height: 200px;
  width: 200px;
  position: relative;
  margin: 0 30px 30px 0;
}

.pie::before {
  content: "";
  display: block;
  position: absolute;
  z-index: 1;
  width: 100px;
  height: 100px;
  background: #EEE;
  border-radius: 50%;
  top: 50px;
  left: 50px;
}

.pie::after {
  content: "";
  display: block;
  width: 120px;
  height: 2px;
  background: rgba(0, 0, 0, 0.1);
  border-radius: 50%;
  box-shadow: 0 0 3px 4px rgba(0, 0, 0, 0.1);
  margin: 220px auto;
}

.slice {
  position: absolute;
  width: 200px;
  height: 200px;
  clip: rect(0px, 200px, 200px, 100px);
  animation: bake-pie 1s;
}

.slice span {
  display: block;
  position: absolute;
  top: 0;
  left: 0;
  background-color: black;
  width: 200px;
  height: 200px;
  border-radius: 50%;
  clip: rect(0px, 200px, 200px, 100px);
}

.legend {
  list-style-type: none;
  padding: 0;
  margin: 0;
  background: #FFF;
  padding: 15px;
  font-size: 13px;
  box-shadow: 1px 1px 0 #DDD,
  2px 2px 0 #BBB;
}

.legend li {
  height: 1.25em;
  width: max-content;
  margin-bottom: 0.7em;
  padding-left: 0.5em;
  border-left: 1.25em solid black;
}

.legend em {
  font-style: normal;
}

.legend span {
  float: right;
}

.chart-title {
  font-weight: bolder;
  font-size: 16px;
}

.avatar-lg {
  height: 4.5rem;
  width: 4.5rem;
}

.avatar-title {
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  height: 100%;
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  justify-content: center;
  width: 100%;
}

*,
*:before,
*:after {
  box-sizing: border-box;
}

@media (min-width: 901px) {
  section.charts_bar {
    padding: 25px;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    max-width: 1000px;
    margin: 0 auto;
    grid-column-gap: 32px;
    grid-row-gap: 40px;
  }

  section.charts_bar .chart_section {
    display: flex;
    flex-direction: column;
  }

  section.charts_bar .chart_section .chart_title {
    order: 2;
    margin-top: 16px;
    text-align: center;
    font-size: 14px;
    text-transform: uppercase;
  }

  section.charts_bar .chart_section > ul {
    display: grid;
    grid-column-gap: 16px;
    grid-template-columns: 1fr repeat(4, auto) 1fr;
    border-bottom: 1px solid #E3E3E3;
    padding: 0 16px;
  }

  section.charts_bar .chart_section > ul:before,
  section.charts_bar .chart_section > ul:after {
    content: '';
    display: block;
  }

  section.charts_bar .chart_section > ul > li {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    flex-direction: column;
    height: 1em;
    position: relative;
    font-size: 200px;
  }

  section.charts_bar .chart_section > ul > li i {
    display: block;
    background: black;
    flex: 0 0 0%;
    width: 16px;
    position: relative;
    z-index: 1;
    overflow: hidden;
    -webkit-animation: drawBar 0.5s forwards;
    opacity: 0;
  }

  section.charts_bar .chart_section > ul > li i:before {
    content: '';
    height: 100%;
    position: absolute;
    display: block;
    background: black;
    width: 100%;
    z-index: 3;
    mix-blend-mode: soft-light;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.18) 0%, rgba(0, 0, 0, 0) 0.25em);
    left: 0;
    bottom: 0;
  }

  section.charts_bar .chart_section > ul > li:before {
    content: attr(data-value);
    width: 32px;
    text-align: center;
    margin: 0 -16px;
    font-size: 14px;
    font-weight: bold;
    transition: all 0.15s linear;
    position: relative;
    z-index: 0;
  }

  section.charts_bar .chart_section > ul > li:after {
    content: attr(data-label);
    display: none;
  }

  section.charts_bar .chart_section > ul > li:nth-of-type(1) i {
    background: #FF6B00;
    animation-delay: 0.2s;
  }

  section.charts_bar .chart_section > ul > li:nth-of-type(2) i {
    background: #254F61;
    animation-delay: 0.3s;
  }

  section.charts_bar .chart_section > ul > li:nth-of-type(3) i {
    background: #4D4D4D;
    animation-delay: 0.4s;
  }

  @-webkit-keyframes drawBar {
    0% {
      flex: 0 0 0%;
      opacity: 1;
    }
    100% {
      opacity: 1;
    }
  }
  
  section.charts_bar .chart_key {
    grid-column: 1 / -1;
  }

  section.charts_bar .chart_key ul {
    display: inline-grid;
    grid-template-columns: auto;
    grid-column-gap: 32px;
    text-align: left;
  }

  section.charts_bar .chart_key ul li {
    grid-row: 1;
    display: flex;
    align-items: center;
  }

  section.charts_bar .chart_key ul li:before {
    content: '';
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: orange;
    display: block;
    margin-right: 12px;
  }

  section.charts_bar .chart_key ul li:nth-of-type(1):before {
    background: #FF6B00;
  }

  section.charts_bar .chart_key ul li:nth-of-type(2):before {
    background: #254F61;
  }

  section.charts_bar .chart_key ul li:nth-of-type(3):before {
    background: #4D4D4D;
  }
}

@media (max-width: 900px) {
  section.charts_bar {
    padding: 25px;
    display: grid;
    grid-template-rows: repeat(4, auto);
    grid-row-gap: 32px;
  }

  section.charts_bar .chart_section {
    display: flex;
    flex-direction: column;
  }

  section.charts_bar .chart_section .chart_title {
    margin-bottom: 16px;
    font-size: 14px;
    text-transform: uppercase;
  }

  section.charts_bar .chart_section .chart_title br {
    display: none;
  }

  section.charts_bar .chart_section > ul {
    display: grid;
    grid-row-gap: 16px;
    grid-template-rows: repeat(4, auto);
    grid-template-columns: auto;
  }

  section.charts_bar .chart_section > ul > li {
    display: flex;
    width: 260px;
  }

  section.charts_bar .chart_section > ul > li i {
    display: block;
    background: black;
    height: 16px;
    transition: all 0.2s ease-in-out 0.2s;
  }

  section.charts_bar .chart_section > ul > li:after {
    content: attr(data-label);
    padding-left: 10px;
    font-size: 12px;
    text-transform: uppercase;
    display: block;
  }

  section.charts_bar .chart_section > ul > li:nth-of-type(1) i {
    background: #FF6B00;
  }

  section.charts_bar .chart_section > ul > li:nth-of-type(2) i {
    background: #254F61;
  }

  section.charts_bar .chart_section > ul > li:nth-of-type(3) i {
    background: #4D4D4D;
  }

  section.charts_bar .chart_section > ul > li:nth-of-type(4) i {
    background: #A6A6A6;
  }

  section.charts_bar .chart_key {
    display: none;
  }
}

.card-header {
  padding: .75rem 1.25rem;
  margin-bottom: 0;
  border-bottom: 1px solid;
  background-color: #fff;
  border-color: #d8dbe0;
}

.card-body {
  -webkit-box-flex: 1;
  -ms-flex: 1 1 auto;
  flex: 1 1 auto;
  min-height: 1px;
  padding: 1.25rem;
}

.card-header:first-child {
  border-radius: calc(.25rem - 1px) calc(.25rem - 1px) 0 0;
}

dl {
  display: block;
  margin-block-start: 1em;
  margin-block-end: 1em;
  margin-inline-start: 0px;
  margin-inline-end: 0px;
  unicode-bidi: isolate;
}

/* Badge styles */
.badge {
  display: inline-block;
  padding: 0.25em 0.4em;
  font-size: 75%;
  font-weight: 700;
  line-height: 1;
  text-align: center;
  white-space: nowrap;
  vertical-align: baseline;
  border-radius: 0.25rem;
}

.badge-success {
  color: #fff;
  background-color: #28a745;
}

.badge-warning {
  color: #212529;
  background-color: #ffc107;
}

/* Table styles */
.table {
  width: 100%;
  margin-bottom: 1rem;
  color: #212529;
  border-collapse: collapse;
}

.table th,
.table td {
  padding: 0.75rem;
  vertical-align: top;
  border-top: 1px solid #dee2e6;
}

.table thead th {
  vertical-align: bottom;
  border-bottom: 2px solid #dee2e6;
  border-top: 0;
}

.table tbody + tbody {
  border-top: 2px solid #dee2e6;
}

.table-sm th,
.table-sm td {
  padding: 0.3rem;
}

.text-center {
  text-align: center !important;
}

.text-left {
  text-align: left !important;
}

.text-right {
  text-align: right !important;
}

.text-bold-600 {
  font-weight: 600 !important;
}

.text-muted {
  color: #6c757d !important;
}

.text-dark {
  color: #343a40 !important;
}

.text-truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.bg-soft-primary {
  background-color: rgba(13, 110, 253, 0.1) !important;
}

.bg-soft-info {
  background-color: rgba(23, 162, 184, 0.1) !important;
}

.border-primary {
  border-color: #0d6efd !important;
}

.border-info {
  border-color: #17a2b8 !important;
}

.text-primary {
  color: #0d6efd !important;
}

.text-info {
  color: #17a2b8 !important;
}

.font-22 {
  font-size: 22px !important;
}

/* Bootstrap grid system */
.row {
  display: flex;
  flex-wrap: wrap;
  margin-right: -15px;
  margin-left: -15px;
}

.col-md-12 {
  position: relative;
  width: 100%;
  min-height: 1px;
  padding-right: 15px;
  padding-left: 15px;
}

.col-md-6 {
  position: relative;
  width: 50%;
  min-height: 1px;
  padding-right: 15px;
  padding-left: 15px;
}

.col-xl-4 {
  position: relative;
  width: 33.333333%;
  min-height: 1px;
  padding-right: 15px;
  padding-left: 15px;
}

.col-md-12 {
  position: relative;
  width: 100%;
  min-height: 1px;
  padding-right: 15px;
  padding-left: 15px;
}

.col-lg-5 {
  position: relative;
  width: 41.666667%;
  min-height: 1px;
  padding-right: 15px;
  padding-left: 15px;
}

.col-lg-7 {
  position: relative;
  width: 58.333333%;
  min-height: 1px;
  padding-right: 15px;
  padding-left: 15px;
}

.col-5 {
  position: relative;
  width: 41.666667%;
  min-height: 1px;
  padding-right: 15px;
  padding-left: 15px;
}

.col-7 {
  position: relative;
  width: 58.333333%;
  min-height: 1px;
  padding-right: 15px;
  padding-left: 15px;
}

/* Card styles */
.card {
  position: relative;
  display: flex;
  flex-direction: column;
  min-width: 0;
  word-wrap: break-word;
  background-color: #fff;
  background-clip: border-box;
  border: 1px solid rgba(0, 0, 0, 0.125);
  border-radius: 0.25rem;
}

.widget-rounded-circle .avatar-lg {
  font-size: 1.2rem;
}

.widget-rounded-circle .avatar-title {
  color: inherit;
}

.mb-5 {
  margin-bottom: 3rem !important;
}

.mb-1 {
  margin-bottom: 0.25rem !important;
}

.p-2 {
  padding: 0.5rem !important;
}

.pt-4 {
  padding-top: 1.5rem !important;
}

.pl-2 {
  padding-left: 0.5rem !important;
}

.px-2 {
  padding-right: 0.5rem !important;
  padding-left: 0.5rem !important;
}

.d-flex {
  display: flex !important;
}

.table-responsive {
  display: block;
  width: 100%;
  overflow-x: auto;
}

/* Hide scrollbars completely */
#dashboard-analytics {
  overflow: hidden;
  width: 100%;
  height: 100vh;
  box-sizing: border-box;
  position: relative;
}

/* Hide scrollbars for all browsers */
#dashboard-analytics::-webkit-scrollbar {
  display: none !important;
  width: 0 !important;
  height: 0 !important;
}

#dashboard-analytics {
  -ms-overflow-style: none !important;
  scrollbar-width: none !important;
}

/* Hide scrollbars from all child elements */
#dashboard-analytics *::-webkit-scrollbar {
  display: none !important;
  width: 0 !important;
  height: 0 !important;
}

#dashboard-analytics * {
  -ms-overflow-style: none !important;
  scrollbar-width: none !important;
}

/* Hide table scrollbars */
.table-responsive {
  overflow: hidden !important;
}

.table-responsive::-webkit-scrollbar {
  display: none !important;
  width: 0 !important;
  height: 0 !important;
}

.table-responsive {
  -ms-overflow-style: none !important;
  scrollbar-width: none !important;
}


/* Responsive adjustments */
@media (max-width: 1400px) {
  .swiper-container {
    padding: 0px !important;
  }

  .swiper-slide {
    width: 50%;
    min-height: 200px;
  }

  .swiper-slide:nth-child(2n) {
    width: 50%;
  }

  .swiper-slide:nth-child(3n) {
    width: 50%;
  }

  .swiper-slide h5 {
    font-size: 15px !important;
  }

  #dashboard-analytics h2 {
    font-size: 22px !important;
  }

  #dashboard-analytics h4, #dashboard-analytics h6 {
    font-size: 15px !important;
  }

  #dashboard-analytics span {
    font-size: 12px;
  }

  #dashboard-analytics table tr th {
    font-size: 13px;
  }
}

@media (max-width: 1200px) {
  .col-xl-4 {
    width: 50%;
  }
  
  #dashboard-analytics {
    padding: 15px;
  }
}

@media (max-width: 768px) {
  .col-md-6,
  .col-xl-4 {
    width: 100%;
  }
  
  #dashboard-analytics {
    padding: 10px;
  }
  
  .row {
    margin-right: -10px;
    margin-left: -10px;
  }
  
  .col-md-6,
  .col-md-12,
  .col-xl-4,
  .col-5,
  .col-7,
  .col-lg-5,
  .col-lg-7 {
    padding-right: 10px;
    padding-left: 10px;
  }
  
  .card {
    margin-bottom: 15px;
  }
  
  .avatar-lg {
    width: 50px;
    height: 50px;
  }
  
  #dashboard-analytics h3 {
    font-size: 18px !important;
  }
  
  .font-22 {
    font-size: 18px;
  }
}

@media (max-width: 640px) {
  .swiper-container {
    padding: 0px !important;
  }

  .swiper-slide {
    width: 100% !important;
    min-height: 200px;
  }

  .swiper-slide:nth-child(2n) {
    width: 100% !important;
  }

  .swiper-slide:nth-child(3n) {
    width: 100% !important;
  }

  #dashboard-analytics {
    padding: 5px;
  }
  
  .row {
    margin-right: -5px;
    margin-left: -5px;
  }
  
  .col-md-6,
  .col-md-12,
  .col-xl-4,
  .col-5,
  .col-7,
  .col-lg-5,
  .col-lg-7 {
    padding-right: 5px;
    padding-left: 5px;
  }
  
  .card {
    margin-bottom: 10px;
  }
  
  .avatar-lg {
    width: 40px;
    height: 40px;
  }
  
  #dashboard-analytics h3 {
    font-size: 16px !important;
  }
  
  .font-22 {
    font-size: 16px;
  }
  
  .table th,
  .table td {
    padding: 6px 8px;
    font-size: 12px;
  }
  
  .shadow-lg.p-2 {
    padding: 0 !important;
  }
}
</style>
