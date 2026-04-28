<template>
  <div class="create-vendor">
    <div class="page-header">
      <h1>Create Vendor</h1>
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
            <el-form-item label="Vendor Name" prop="name">
              <el-input
                v-model="form.name"
                placeholder="Enter vendor name"
                maxlength="255"
              />
            </el-form-item>
          </el-col>
          
          <el-col :span="12">
            <el-form-item label="Contact Person" prop="contact_person">
              <el-input
                v-model="form.contact_person"
                placeholder="Enter contact person name"
                maxlength="255"
              />
            </el-form-item>
          </el-col>
        </el-row>

        <el-row :gutter="20">
          <el-col :span="12">
            <el-form-item label="Email" prop="email">
              <el-input
                v-model="form.email"
                type="email"
                placeholder="Enter email address"
                maxlength="255"
              />
            </el-form-item>
          </el-col>
          
          <el-col :span="12">
            <el-form-item label="Phone" prop="phone">
              <el-input
                v-model="form.phone"
                placeholder="Enter phone number"
                maxlength="20"
              />
            </el-form-item>
          </el-col>
        </el-row>

        <el-form-item label="Address" prop="address">
          <el-input
            v-model="form.address"
            type="textarea"
            :rows="3"
            placeholder="Enter vendor address"
            maxlength="500"
          />
        </el-form-item>

        <el-row :gutter="20">
          <el-col :span="12">
            <el-form-item label="Tax ID" prop="tax_id">
              <el-input
                v-model="form.tax_id"
                placeholder="Enter tax ID"
                maxlength="50"
              />
            </el-form-item>
          </el-col>
          
          <el-col :span="12">
            <el-form-item label="Credit Limit" prop="credit_limit">
              <el-input-number
                v-model="form.credit_limit"
                :min="0"
                :precision="2"
                style="width: 100%"
                placeholder="Enter credit limit"
              />
            </el-form-item>
          </el-col>
        </el-row>

        <el-form-item label="Status" prop="status">
          <el-radio-group v-model="form.status">
            <el-radio label="active">Active</el-radio>
            <el-radio label="inactive">Inactive</el-radio>
            <el-radio label="blacklisted">Blacklisted</el-radio>
          </el-radio-group>
        </el-form-item>

        <el-form-item label="Notes" prop="notes">
          <el-input
            v-model="form.notes"
            type="textarea"
            :rows="4"
            placeholder="Enter additional notes about this vendor"
            maxlength="1000"
            show-word-limit
          />
        </el-form-item>

        <el-form-item>
          <el-button type="primary" @click="handleSubmit" :loading="loading">
            <el-icon><Check /></el-icon>
            Create Vendor
          </el-button>
          <el-button @click="handleReset">
            <el-icon><Refresh /></el-icon>
            Reset
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
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import procurementService from '@/services/procurement'
import { ElMessage } from 'element-plus'
import { ArrowLeft, Check, Refresh, Close } from '@element-plus/icons-vue'

const router = useRouter()

const formRef = ref()
const loading = ref(false)

const form = reactive({
  name: '',
  contact_person: '',
  email: '',
  phone: '',
  address: '',
  tax_id: '',
  credit_limit: 0,
  status: 'active',
  notes: ''
})

const rules = {
  name: [
    { required: true, message: 'Vendor name is required', trigger: 'blur' },
    { min: 2, message: 'Vendor name must be at least 2 characters', trigger: 'blur' }
  ],
  email: [
    { type: 'email', message: 'Please enter a valid email address', trigger: 'blur' }
  ],
  phone: [
    { pattern: /^[\d\s\-\+\(\)]+$/, message: 'Please enter a valid phone number', trigger: 'blur' }
  ],
  credit_limit: [
    { type: 'number', min: 0, message: 'Credit limit must be positive', trigger: 'blur' }
  ],
  status: [
    { required: true, message: 'Please select a status', trigger: 'change' }
  ]
}

const handleSubmit = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()
    loading.value = true

    await procurementService.createVendor(form)
    ElMessage.success('Vendor created successfully')
    router.push('/vendors')
  } catch (error) {
    if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      ElMessage.error(errors[0])
    } else {
      ElMessage.error('Failed to create vendor')
    }
  } finally {
    loading.value = false
  }
}

const handleReset = () => {
  if (formRef.value) {
    formRef.value.resetFields()
  }
}
</script>

<style scoped>
.create-vendor {
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
</style>
