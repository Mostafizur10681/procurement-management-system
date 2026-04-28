<template>
  <div class="create-procurement-request">
    <div class="page-header">
      <h1>Create Procurement Request</h1>
      <el-button @click="$router.back()">
        <el-icon><ArrowLeft /></el-icon>
        Back
      </el-button>
    </div>

    <el-card>
      <el-form
        ref="formRef"
        :model="form"
        :rules="rules"
        label-width="140px"
        @submit.prevent="handleSubmit"
      >
        <el-row :gutter="20">
          <el-col :span="12">
            <el-form-item label="Department" prop="department_id">
              <el-select
                v-model="form.department_id"
                placeholder="Select Department"
                style="width: 100%"
              >
                <el-option
                  v-for="dept in departments"
                  :key="dept.id"
                  :label="dept.name"
                  :value="dept.id"
                />
              </el-select>
            </el-form-item>
          </el-col>
          
          <el-col :span="12">
            <el-form-item label="Category" prop="category_id">
              <el-select
                v-model="form.category_id"
                placeholder="Select Category"
                style="width: 100%"
              >
                <el-option
                  v-for="category in categories"
                  :key="category.id"
                  :label="category.name"
                  :value="category.id"
                />
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>

        <el-form-item label="Title" prop="title">
          <el-input
            v-model="form.title"
            placeholder="Enter request title"
            maxlength="255"
            show-word-limit
          />
        </el-form-item>

        <el-form-item label="Description" prop="description">
          <el-input
            v-model="form.description"
            type="textarea"
            :rows="4"
            placeholder="Enter detailed description"
            maxlength="1000"
            show-word-limit
          />
        </el-form-item>

        <el-row :gutter="20">
          <el-col :span="12">
            <el-form-item label="Priority" prop="priority">
              <el-select v-model="form.priority" style="width: 100%">
                <el-option label="Low" value="low" />
                <el-option label="Medium" value="medium" />
                <el-option label="High" value="high" />
                <el-option label="Urgent" value="urgent" />
              </el-select>
            </el-form-item>
          </el-col>
          
          <el-col :span="12">
            <el-form-item label="Required Date" prop="required_date">
              <el-date-picker
                v-model="form.required_date"
                type="date"
                placeholder="Select date"
                style="width: 100%"
                :disabled-date="disablePastDates"
              />
            </el-form-item>
          </el-col>
        </el-row>

        <el-form-item label="Justification">
          <el-input
            v-model="form.justification"
            type="textarea"
            :rows="3"
            placeholder="Enter justification for this request"
            maxlength="1000"
            show-word-limit
          />
        </el-form-item>

        <el-divider>Procurement Items</el-divider>

        <div class="items-section">
          <div
            v-for="(item, index) in form.items"
            :key="index"
            class="item-row"
          >
            <el-card>
              <template #header>
                <div class="item-header">
                  <span>Item {{ index + 1 }}</span>
                  <el-button
                    v-if="form.items.length > 1"
                    type="danger"
                    size="small"
                    @click="removeItem(index)"
                  >
                    <el-icon><Delete /></el-icon>
                    Remove
                  </el-button>
                </div>
              </template>

              <el-row :gutter="20">
                <el-col :span="12">
                  <el-form-item
                    :label="`Item ${index + 1} Name`"
                    :prop="`items.${index}.item_name`"
                    :rules="[{ required: true, message: 'Item name is required' }]"
                  >
                    <el-input
                      v-model="item.item_name"
                      placeholder="Enter item name"
                    />
                  </el-form-item>
                </el-col>
                
                <el-col :span="12">
                  <el-form-item
                    :label="`Item ${index + 1} Description`"
                    :prop="`items.${index}.description`"
                  >
                    <el-input
                      v-model="item.description"
                      placeholder="Enter item description"
                    />
                  </el-form-item>
                </el-col>
              </el-row>

              <el-row :gutter="20">
                <el-col :span="8">
                  <el-form-item
                    :label="`Quantity`"
                    :prop="`items.${index}.quantity`"
                    :rules="[
                      { required: true, message: 'Quantity is required' },
                      { type: 'number', min: 1, message: 'Quantity must be at least 1' }
                    ]"
                  >
                    <el-input-number
                      v-model="item.quantity"
                      :min="1"
                      style="width: 100%"
                    />
                  </el-form-item>
                </el-col>
                
                <el-col :span="8">
                  <el-form-item
                    :label="`Unit of Measure`"
                    :prop="`items.${index}.unit_of_measure`"
                    :rules="[{ required: true, message: 'Unit of measure is required' }]"
                  >
                    <el-input
                      v-model="item.unit_of_measure"
                      placeholder="e.g., pcs, kg, m"
                    />
                  </el-form-item>
                </el-col>
                
                <el-col :span="8">
                  <el-form-item
                    :label="`Unit Price`"
                    :prop="`items.${index}.unit_price`"
                    :rules="[
                      { required: true, message: 'Unit price is required' },
                      { type: 'number', min: 0, message: 'Unit price must be positive' }
                    ]"
                  >
                    <el-input-number
                      v-model="item.unit_price"
                      :min="0"
                      :precision="2"
                      style="width: 100%"
                      @change="updateItemTotal(index)"
                    />
                  </el-form-item>
                </el-col>
              </el-row>

              <el-form-item :label="`Specifications`">
                <el-input
                  v-model="item.specifications"
                  type="textarea"
                  :rows="2"
                  placeholder="Enter technical specifications"
                />
              </el-form-item>

              <el-row :gutter="20">
                <el-col :span="12">
                  <el-form-item :label="`Total Price`">
                    <el-input
                      :model-value="formatCurrency(item.total_price)"
                      readonly
                      style="background: #f5f5f5"
                    />
                  </el-form-item>
                </el-col>
                
                <el-col :span="12">
                  <el-form-item :label="`Vendor (Optional)`">
                    <el-select
                      v-model="item.vendor_id"
                      placeholder="Select vendor"
                      style="width: 100%"
                      clearable
                    >
                      <el-option
                        v-for="vendor in vendors"
                        :key="vendor.id"
                        :label="vendor.name"
                        :value="vendor.id"
                      />
                    </el-select>
                  </el-form-item>
                </el-col>
              </el-row>
            </el-card>
          </div>

          <div class="item-actions">
            <el-button type="primary" @click="addItem">
              <el-icon><Plus /></el-icon>
              Add Item
            </el-button>
          </div>
        </div>

        <el-divider />

        <el-form-item label="Estimated Total Cost">
          <el-input
            :model-value="formatCurrency(totalEstimatedCost)"
            readonly
            style="background: #f5f5f5; max-width: 300px"
          />
        </el-form-item>

        <el-form-item>
          <el-button type="primary" @click="handleSubmit" :loading="loading">
            <el-icon><Check /></el-icon>
            Create Request
          </el-button>
          <el-button @click="handleSaveDraft" :loading="loading">
            <el-icon><Document /></el-icon>
            Save as Draft
          </el-button>
          <el-button @click="$router.back()">
            <el-icon><Close /></el-icon>
            Cancel
          </el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import procurementService from '@/services/procurement'
import { ElMessage } from 'element-plus'
import { ArrowLeft, Delete, Plus, Check, Document, Close } from '@element-plus/icons-vue'

const router = useRouter()

const formRef = ref()
const loading = ref(false)
const departments = ref([])
const categories = ref([])
const vendors = ref([])

const form = reactive({
  department_id: '',
  category_id: '',
  title: '',
  description: '',
  priority: 'medium',
  required_date: '',
  justification: '',
  items: [
    {
      item_name: '',
      description: '',
      specifications: '',
      quantity: 1,
      unit_of_measure: '',
      unit_price: 0,
      total_price: 0,
      vendor_id: null
    }
  ]
})

const rules = {
  department_id: [
    { required: true, message: 'Please select a department', trigger: 'change' }
  ],
  category_id: [
    { required: true, message: 'Please select a category', trigger: 'change' }
  ],
  title: [
    { required: true, message: 'Please enter a title', trigger: 'blur' },
    { min: 3, message: 'Title must be at least 3 characters', trigger: 'blur' }
  ],
  description: [
    { required: true, message: 'Please enter a description', trigger: 'blur' },
    { min: 10, message: 'Description must be at least 10 characters', trigger: 'blur' }
  ],
  priority: [
    { required: true, message: 'Please select a priority', trigger: 'change' }
  ],
  required_date: [
    { required: true, message: 'Please select a required date', trigger: 'change' }
  ]
}

const totalEstimatedCost = computed(() => {
  return form.items.reduce((total, item) => total + (item.total_price || 0), 0)
})

const formatCurrency = (value) => {
  return value ? value.toLocaleString('en-US', { minimumFractionDigits: 2 }) : '0.00'
}

const disablePastDates = (date) => {
  return date < new Date().setHours(0, 0, 0, 0)
}

const addItem = () => {
  form.items.push({
    item_name: '',
    description: '',
    specifications: '',
    quantity: 1,
    unit_of_measure: '',
    unit_price: 0,
    total_price: 0,
    vendor_id: null
  })
}

const removeItem = (index) => {
  form.items.splice(index, 1)
}

const updateItemTotal = (index) => {
  const item = form.items[index]
  item.total_price = item.quantity * item.unit_price
}

const handleSubmit = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()
    
    if (form.items.length === 0) {
      ElMessage.error('Please add at least one item')
      return
    }

    loading.value = true

    const requestData = {
      ...form,
      estimated_cost: totalEstimatedCost.value,
      required_date: form.required_date.toISOString().split('T')[0]
    }

    await procurementService.createProcurementRequest(requestData)
    ElMessage.success('Procurement request created successfully')
    router.push('/procurement-requests')
  } catch (error) {
    if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      ElMessage.error(errors[0])
    } else {
      ElMessage.error('Failed to create procurement request')
    }
  } finally {
    loading.value = false
  }
}

const handleSaveDraft = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()
    loading.value = true

    const requestData = {
      ...form,
      estimated_cost: totalEstimatedCost.value,
      required_date: form.required_date.toISOString().split('T')[0]
    }

    await procurementService.createProcurementRequest(requestData)
    ElMessage.success('Draft saved successfully')
    router.push('/procurement-requests')
  } catch (error) {
    if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      ElMessage.error(errors[0])
    } else {
      ElMessage.error('Failed to save draft')
    }
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

const loadCategories = async () => {
  try {
    const response = await procurementService.getCategories()
    categories.value = response
  } catch (error) {
    console.error('Failed to load categories:', error)
  }
}

const loadVendors = async () => {
  try {
    const response = await procurementService.getVendors()
    vendors.value = response.data || response
  } catch (error) {
    console.error('Failed to load vendors:', error)
  }
}

onMounted(() => {
  loadDepartments()
  loadCategories()
  loadVendors()
})
</script>

<style scoped>
.create-procurement-request {
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

.items-section {
  margin-bottom: 24px;
}

.item-row {
  margin-bottom: 16px;
}

.item-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.item-actions {
  margin-top: 16px;
  text-align: center;
}
</style>
