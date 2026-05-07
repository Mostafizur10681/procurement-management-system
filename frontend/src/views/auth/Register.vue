<template>
  <div class="min-vh-100 d-flex align-items-center justify-content-center py-4" style="background-color: #1A3578;">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 col-xl-5">
          <!-- Header -->
          
          
          <!-- Registration Card -->
          <div class="card shadow-sm">
            <div class="text-center mb-4">
            <div class="mb-3 mt-3">
              <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                <el-icon class="text-white" style="font-size: 32px;"><User /></el-icon>
              </div>
            </div>
            <h2 class="h3 mb-2 text-dark">Create your account</h2>
            <p class="text-muted">Or sign in to your existing account</p>
          </div>
            <div class="card-body p-4">
              <form @submit.prevent="handleRegister">
                <!-- Name and Email Row -->
                <div class="row mb-3">
                  <div class="col-md-12 mb-3 mb-md-0">
                    <label for="name" class="form-label text-dark">Full Name</label>
                    <input
                      id="name"
                      v-model="registerForm.name"
                      type="text"
                      class="form-control"
                      placeholder="Full Name"
                      required
                    />
                  </div>
                  <div class="col-md-12 mt-3">
                    <label for="email" class="form-label text-dark">Email address</label>
                    <input
                      id="email"
                      v-model="registerForm.email"
                      type="email"
                      class="form-control"
                      placeholder="you@example.com"
                      autocomplete="email"
                      required
                    />
                  </div>
                </div>

                <!-- Role Field -->
                <div class="row mb-3">
                  <div class="col-md-12">
                    <label for="role" class="form-label text-dark">Role</label>
                    <select
                      id="role"
                      v-model="registerForm.role"
                      class="form-select"
                      required
                    >
                      <option value="">Select a role</option>
                      <option value="user">User</option>
                      <option value="manager">Manager</option>
                      <option value="admin">Admin</option>
                    </select>
                  </div>
                </div>

                <!-- Password Fields -->
                <div class="row mb-3">
                  <div class="col-12">
                    <label for="password" class="form-label text-dark">Password</label>
                    <input
                      id="password"
                      v-model="registerForm.password"
                      type="password"
                      class="form-control"
                      placeholder="••••••••"
                      autocomplete="new-password"
                      required
                    />
                  </div>
                </div>

                <div class="row mb-4">
                  <div class="col-12">
                    <label for="password_confirmation" class="form-label text-dark">Confirm Password</label>
                    <input
                      id="password_confirmation"
                      v-model="registerForm.password_confirmation"
                      type="password"
                      class="form-control"
                      placeholder="••••••••"
                      autocomplete="new-password"
                      required
                    />
                  </div>
                </div>

                <!-- Submit Button -->
                <div class="d-grid">
                  <button
                    type="submit"
                    :disabled="loading"
                    class="register-button btn-lg"
                    @click="handleRegister"
                  >
                    <span v-if="loading" class="d-flex align-items-center">
                      <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                      Processing...
                    </span>
                    <span v-else>Sign Up</span>
                  </button>
                </div>
              </form>
            </div>
            <!-- Footer Link -->
          <div class="text-center mt-1 mb-3">
            <div class="position-relative">
              <small class="text-muted">
                  Already have an account? 
                  <router-link to="/login" class="text-primary text-decoration-none">
                    Sign in
                  </router-link>
                </small>
            </div>
          </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { ElMessage } from 'element-plus'
import { User, Message, OfficeBuilding, Lock, ArrowRight } from '@element-plus/icons-vue'

const router = useRouter()
const authStore = useAuthStore()

const registerFormRef = ref()
const loading = ref(false)

const registerForm = reactive({
  name: '',
  email: '',
  role: 'user',
  password: '',
  password_confirmation: ''
})

const rules = {
  name: [
    { required: true, message: 'Please enter your name', trigger: 'blur' },
    { min: 2, message: 'Name must be at least 2 characters', trigger: 'blur' }
  ],
  email: [
    { required: true, message: 'Please enter your email', trigger: 'blur' },
    { type: 'email', message: 'Please enter a valid email', trigger: 'blur' }
  ],
  department: [
    { required: true, message: 'Please enter your department', trigger: 'blur' }
  ],
  role: [
    { required: true, message: 'Please select your role', trigger: 'change' }
  ],
  password: [
    { required: true, message: 'Please enter your password', trigger: 'blur' },
    { min: 8, message: 'Password must be at least 8 characters', trigger: 'blur' }
  ],
  password_confirmation: [
    { required: true, message: 'Please confirm your password', trigger: 'blur' },
    {
      validator: (rule, value, callback) => {
        if (value !== registerForm.password) {
          callback(new Error('Passwords do not match'))
        } else {
          callback()
        }
      },
      trigger: 'blur'
    }
  ]
}

const handleRegister = async () => {
  // Check if all required fields are filled
  if (!registerForm.name || !registerForm.email || !registerForm.role || !registerForm.password) {
    ElMessage.error('Please fill in all required fields')
    return
  }
  
  if (registerForm.password !== registerForm.password_confirmation) {
    ElMessage.error('Passwords do not match')
    return
  }
  
  try {
    loading.value = true
    
    console.log('Form data being sent:', registerForm)
    console.log('Calling register API...')
    const response = await authStore.register(registerForm)
    console.log('Registration successful:', response)
    
    ElMessage.success('Registration successful! Redirecting to login...')
    
    // Clear form
    Object.keys(registerForm).forEach(key => {
      registerForm[key] = ''
    })
    registerForm.role = 'user'
    
    // Redirect to login page for user to sign in
    setTimeout(() => {
      router.push('/login')
    }, 1500)
  } catch (error) {
    console.error('Registration error:', error)
    console.error('Error response:', error.response?.data)
    
    if (error.response?.status === 0) {
      ElMessage.error('Cannot connect to server. Please check if the backend is running.')
    } else if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      ElMessage.error(errors[0])
    } else if (error.response?.data?.error) {
      ElMessage.error(error.response.data.error)
    } else if (error.response?.data?.message) {
      ElMessage.error(error.response.data.message)
    } else {
      ElMessage.error(`Registration failed: ${error.message || 'Please try again.'}`)
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Bootstrap custom styles */
.card {
  border: none;
  border-radius: 0.5rem;
}

.form-control:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.form-select:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.btn-primary {
  background-color: #0d6efd;
  border-color: #0d6efd;
}

.btn-primary:hover {
  background-color: #0b5ed7;
  border-color: #0a58ca;
}

.btn-primary:disabled {
  background-color: #6c757d;
  border-color: #6c757d;
}

.spinner-border-sm {
  width: 1rem;
  height: 1rem;
  border-width: 0.15em;
}

.register-button {
  width: 100%;
  padding: 12px 16px;
  background: #193478;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  width: 100%;
  box-sizing: border-box;
}
</style>
