<template>
  <div class="register-container">
    <div class="floating-shapes">
      <div class="shape shape-1"></div>
      <div class="shape shape-2"></div>
      <div class="shape shape-3"></div>
      <div class="shape shape-4"></div>
    </div>
    
    <div class="register-card">
      <div class="card-header">
        <div class="register-icon">
          <el-icon><User /></el-icon>
        </div>
        <h1 class="main-title">Join Our Team</h1>
        <p class="subtitle">Create your account and start your journey</p>
      </div>
      
      <form class="register-form" @submit.prevent="handleRegister">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">
              <User class="label-icon" />
              Full Name
            </label>
            <input
              v-model="registerForm.name"
              type="text"
              class="form-input"
              placeholder="Enter your full name"
              required
            />
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">
              <Message class="label-icon" />
              Email Address
            </label>
            <input
              v-model="registerForm.email"
              type="email"
              class="form-input"
              placeholder="your@email.com"
              required
            />
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">
              <OfficeBuilding class="label-icon" />
              Department
            </label>
            <input
              v-model="registerForm.department"
              type="text"
              class="form-input"
              placeholder="Enter your department"
              required
            />
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">
              <svg viewBox="0 0 24 24" fill="currentColor" class="label-icon">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
              </svg>
              Role
            </label>
            <select v-model="registerForm.role" class="form-input" required>
              <option value="">Select your role</option>
              <option value="user">User</option>
              <option value="manager">Manager</option>
              <option value="admin">Admin</option>
            </select>
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">
              <Lock class="label-icon" />
              Password
            </label>
            <input
              v-model="registerForm.password"
              type="password"
              class="form-input"
              placeholder="Create a strong password"
              required
            />
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">
              <Lock class="label-icon" />
              Confirm Password
            </label>
            <input
              v-model="registerForm.password_confirmation"
              type="password"
              class="form-input"
              placeholder="Confirm your password"
              required
            />
          </div>
        </div>
        
        <button type="submit" class="register-button" :disabled="loading">
          <span v-if="loading" class="loading-spinner">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <path d="M12,4V2A10,10 0 0,0 2,12H4A8,8 0 0,1 12,4Z"/>
            </svg>
          </span>
          <span v-else>Create Account</span>
        </button>
      </form>
      
      <div class="register-footer">
        <p>Already have an account?</p>
        <router-link to="/login" class="login-link">Sign In</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { ElMessage } from 'element-plus'
import { User, Message, Lock, OfficeBuilding } from '@element-plus/icons-vue'

const router = useRouter()
const authStore = useAuthStore()

const registerFormRef = ref()
const loading = ref(false)

const registerForm = reactive({
  name: '',
  email: '',
  department: '',
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
  // Simple test to verify click is working
  alert('Register button clicked! This is a test.')
  console.log('Register button clicked')
  console.log('Form data:', registerForm)
  
  if (!registerFormRef.value) {
    console.error('Form ref not available')
    return
  }
  
  // Check if all required fields are filled
  if (!registerForm.name || !registerForm.email || !registerForm.department || !registerForm.role || !registerForm.password) {
    ElMessage.error('Please fill in all required fields')
    return
  }
  
  if (registerForm.password !== registerForm.password_confirmation) {
    ElMessage.error('Passwords do not match')
    return
  }
  
  try {
    loading.value = true
    
    console.log('Calling register API...')
    const response = await authStore.register(registerForm)
    console.log('Registration successful:', response)
    
    ElMessage.success('Registration successful')
    router.push('/dashboard')
  } catch (error) {
    console.error('Registration error:', error)
    if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      ElMessage.error(errors[0])
    } else if (error.response?.data?.error) {
      ElMessage.error(error.response.data.error)
    } else {
      ElMessage.error('Registration failed. Please try again.')
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.register-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #122B5A;
  padding: 20px;
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
  animation: float 20s infinite ease-in-out;
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
  top: 20%;
  right: 30%;
  animation-delay: 6s;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0) rotate(0deg);
    opacity: 0.7;
  }
  25% {
    transform: translateY(-20px) rotate(90deg);
    opacity: 0.9;
  }
  50% {
    transform: translateY(0) rotate(180deg);
    opacity: 0.7;
  }
  75% {
    transform: translateY(20px) rotate(270deg);
    opacity: 0.9;
  }
}

.register-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 24px;
  padding: 40px;
  width: 100%;
  max-width: 480px;
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

.card-header {
  text-align: center;
  margin-bottom: 40px;
}

.register-icon {
  margin-bottom: 16px;
  margin-top: 0;
  display: flex;
  justify-content: center;
}

.register-icon .el-icon {
  font-size: 48px;
  color: #193478;
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
    box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
  }
  50% {
    transform: scale(1.05);
    box-shadow: 0 8px 40px rgba(102, 126, 234, 0.5);
  }
}

.main-title {
  font-size: 32px;
  font-weight: 800;
  background: #193478;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin: 0 0 12px 0;
  letter-spacing: -0.5px;
}

.subtitle {
  color: #64748b;
  font-size: 16px;
  margin: 0;
  font-weight: 500;
}

.register-form {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.form-row {
  animation: fadeInUp 0.6s ease-out backwards;
}

.form-row:nth-child(1) { animation-delay: 0.1s; }
.form-row:nth-child(2) { animation-delay: 0.2s; }
.form-row:nth-child(3) { animation-delay: 0.3s; }
.form-row:nth-child(4) { animation-delay: 0.4s; }
.form-row:nth-child(5) { animation-delay: 0.5s; }
.form-row:nth-child(6) { animation-delay: 0.6s; }

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
  color: #334155;
  margin-bottom: 8px;
  font-size: 14px;
}

.label-icon {
  width: 16px;
  height: 16px;
  color: #667eea;
}

.form-input {
  width: 100%;
  padding: 16px 20px;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  font-size: 16px;
  transition: all 0.3s ease;
  background: white;
  color: #1e293b;
  pointer-events: auto !important;
  cursor: text !important;
  user-select: text !important;
  -webkit-user-select: text !important;
  -moz-user-select: text !important;
  -ms-user-select: text !important;
  opacity: 1 !important;
  z-index: 1000 !important;
  position: relative !important;
}

.form-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
  transform: translateY(-2px);
}

.form-input::placeholder {
  color: #94a3b8;
}

.register-button {
  width: 100%;
  padding: 18px 24px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  margin-top: 8px;
  pointer-events: auto !important;
  z-index: 1000 !important;
  position: relative !important;
}

.register-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 12px 24px rgba(102, 126, 234, 0.4);
}

.register-button:active:not(:disabled) {
  transform: translateY(0);
}

.register-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.register-button::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s;
}

.register-button:hover::before {
  left: 100%;
}

.loading-spinner {
  display: inline-block;
  width: 20px;
  height: 20px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.register-footer {
  text-align: center;
  margin-top: 32px;
  padding-top: 24px;
  border-top: 1px solid #e2e8f0;
}

.register-footer p {
  color: #64748b;
  margin: 0 0 8px 0;
  font-size: 14px;
}

.login-link {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s ease;
}

.login-link:hover {
  color: #764ba2;
  text-decoration: underline;
}
</style>
