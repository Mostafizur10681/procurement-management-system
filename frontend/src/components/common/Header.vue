<template>
  <header class="main-header">
    <div class="header-container">
      <div class="header-left">
        <div class="logo">
          <div class="logo-placeholder">PMS</div>
          <span class="logo-text">Procurement Management System</span>
        </div>
      </div>
      
      <div class="header-right">
        <div class="user-dropdown">
          <el-dropdown @command="handleCommand" trigger="click">
            <div class="user-info">
              <div class="user-avatar">
                <i class="fas fa-user"></i>
              </div>
              <div class="user-details">
                <span class="user-name">{{ authStore.user?.name || 'User' }}</span>
                <span class="user-role">{{ authStore.user?.role || 'Role' }}</span>
              </div>
              <i class="fas fa-chevron-down"></i>
            </div>
            <template #dropdown>
              <el-dropdown-menu>
                <el-dropdown-item command="profile">
                  <i class="fas fa-user"></i>
                  Profile
                </el-dropdown-item>
                <el-dropdown-item command="settings">
                  <i class="fas fa-cog"></i>
                  Settings
                </el-dropdown-item>
                <el-dropdown-item divided command="logout">
                  <i class="fas fa-sign-out-alt"></i>
                  Logout
                </el-dropdown-item>
              </el-dropdown-menu>
            </template>
          </el-dropdown>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const isActive = (path) => {
  return route.path === path
}

const handleCommand = (command) => {
  switch (command) {
    case 'profile':
      router.push('/profile')
      break
    case 'settings':
      router.push('/settings')
      break
    case 'logout':
      authStore.logout()
      router.push('/login')
      break
  }
}
</script>

<style scoped>
.main-header {
  background: linear-gradient(135deg, #122B5A 0%, #1e3a8a 100%);
  color: white;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  height: 70px;
}

.header-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 100%;
  padding: 0 30px;
}

.header-left {
  display: flex;
  align-items: center;
}

.logo {
  display: flex;
  align-items: center;
  text-decoration: none;
  color: white;
}

.logo-placeholder {
  height: 40px;
  margin-right: 15px;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  font-weight: bold;
  font-size: 16px;
  width: 40px;
}

.logo-text {
  font-size: 18px;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.header-center {
  flex: 1;
  display: flex;
  justify-content: center;
}

.nav-menu {
  display: flex;
  align-items: center;
  gap: 30px;
}

.nav-link {
  display: flex;
  align-items: center;
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  padding: 10px 15px;
  border-radius: 8px;
  transition: all 0.3s ease;
  font-weight: 500;
}

.nav-link:hover {
  background: rgba(255, 255, 255, 0.1);
  color: white;
  transform: translateY(-2px);
}

.nav-link.active {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border-bottom: 2px solid #4CAF50;
}

.nav-link i {
  margin-right: 8px;
  font-size: 16px;
}

.header-right {
  display: flex;
  align-items: center;
}

.user-dropdown {
  cursor: pointer;
}

.user-info {
  display: flex;
  align-items: center;
  padding: 8px 15px;
  border-radius: 25px;
  background: rgba(255, 255, 255, 0.1);
  transition: all 0.3s ease;
}

.user-info:hover {
  background: rgba(255, 255, 255, 0.2);
}

.user-avatar {
  width: 35px;
  height: 35px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 12px;
}

.user-avatar i {
  font-size: 16px;
}

.user-details {
  display: flex;
  flex-direction: column;
  margin-right: 10px;
}

.user-name {
  font-weight: 600;
  font-size: 14px;
}

.user-role {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.7);
}

/* Mobile responsive */
@media (max-width: 768px) {
  .header-container {
    padding: 0 15px;
  }
  
  .header-center {
    display: none;
  }
  
  .logo-text {
    display: none;
  }
  
  .user-details {
    display: none;
  }
}
</style>
