<template>
  <div class="login-container">
    <div class="floating-shapes">
      <div class="shape shape-1"></div>
      <div class="shape shape-2"></div>
      <div class="shape shape-3"></div>
      <div class="shape shape-4"></div>
    </div>
    
    <div class="login-card">
      <div class="card-header">
        <div class="login-icon">
          <el-icon><User /></el-icon>
        </div>
        <h1 class="main-title">Welcome Back</h1>
        <p class="subtitle">Sign in to access your procurement dashboard</p>
      </div>
      
      <form class="login-form" @submit.prevent="handleLogin">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Email Address</label>
            <input
              v-model="loginForm.email"
              type="email"
              class="form-input"
              placeholder="your@email.com"
              required
            />
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Password</label>
            <input
              v-model="loginForm.password"
              type="password"
              class="form-input"
              placeholder="Enter your password"
              required
            />
          </div>
        </div>
        
        <button type="submit" class="login-button" :disabled="loading">
          <span v-if="loading" class="loading-spinner">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <path d="M12,4V2A10,10 0 0,0 2,12H4A8,8 0 0,1 12,4Z"/>
            </svg>
          </span>
          <span v-else>Sign In</span>
        </button>
      </form>
      
      <div class="login-footer">
        <p>
          Don't have an account?
          <router-link to="/register" class="register-link">Register here</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { ElMessage } from 'element-plus'
import { User, Message, Lock } from '@element-plus/icons-vue'

const router = useRouter()
const authStore = useAuthStore()

const loginFormRef = ref()
const loading = ref(false)

const loginForm = reactive({
  email: '',
  password: ''
})

const rules = {
  email: [
    { required: true, message: 'Please enter your email', trigger: 'blur' },
    { type: 'email', message: 'Please enter a valid email', trigger: 'blur' }
  ],
  password: [
    { required: true, message: 'Please enter your password', trigger: 'blur' },
    { min: 6, message: 'Password must be at least 6 characters', trigger: 'blur' }
  ]
}

const handleLogin = async () => {
  // Basic validation
  if (!loginForm.email || !loginForm.password) {
    ElMessage.error('Please enter both email and password')
    return
  }
  
  if (!loginForm.email.includes('@')) {
    ElMessage.error('Please enter a valid email address')
    return
  }
  
  if (loginForm.password.length < 6) {
    ElMessage.error('Password must be at least 6 characters')
    return
  }
  
  try {
    loading.value = true
    
    await authStore.login(loginForm)
    ElMessage.success('Login successful')
    router.push('/dashboard')
  } catch (error) {
    console.error('Login error:', error)
    if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      ElMessage.error(errors[0])
    } else if (error.response?.data?.message) {
      ElMessage.error(error.response.data.message)
    } else {
      ElMessage.error('Login failed. Please check your credentials.')
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #1A3578;
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

.login-card {
  background: white;
  border-radius: 8px;
  padding: 32px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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

.login-footer {
  text-align: center;
  margin-top: 32px;
  padding-top: 24px;
  border-top: 1px solid #e2e8f0;
}

.login-footer p {
  color: #333 !important;
  margin: 0 0 8px 0;
  font-size: 14px;
}

.register-link {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s ease;
}

.register-link:hover {
  color: #764ba2;
  text-decoration: underline;
}

/* Card Header Styles */
.card-header {
  text-align: center;
  margin-bottom: 0;
  margin-top: 0;
  padding-top: 0;
}

.login-icon {
  margin-bottom: 16px;
  margin-top: 0;
  display: flex;
  justify-content: center;
}

.login-icon .el-icon {
  font-size: 48px;
  color: #193478;
}

.main-title {
  margin-top: 0;
  margin-bottom: 8px;
  color: black !important;
}

.subtitle {
  margin-top: 0;
  margin-bottom: 30px;
  color: black !important;
}

/* Form Styles */
.login-form {
  margin-top: 0;
}

.form-row {
  margin-bottom: 0;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 16px;
}

.form-label {
  font-size: 14px;
  font-weight: 500;
  color: black !important;
  display: block;
  margin-bottom: 4px;
}

.label-icon {
  width: 16px;
  height: 16px;
  color: #667eea;
}

.form-input {
  padding: 12px 16px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  color: black;
  background: white;
  width: 100%;
  box-sizing: border-box;
}

.form-input:focus {
  outline: none;
  border-color: #007bff;
}

.form-input::placeholder {
  color: #999;
}

.login-button {
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

.login-button:hover {
  background: #163069;
}

.login-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.loading-spinner {
  width: 20px;
  height: 20px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>
