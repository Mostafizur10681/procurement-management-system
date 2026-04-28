<template>
  <div class="app-header">
    <div class="header-left">
      <h2>Procurement Management System</h2>
    </div>
    <div class="header-right">
      <el-dropdown @command="handleCommand">
        <span class="user-info">
          <el-avatar :size="32" :src="userAvatar">
            {{ authStore.user?.name?.charAt(0) }}
          </el-avatar>
          <span class="username">{{ authStore.user?.name }}</span>
          <el-icon><ArrowDown /></el-icon>
        </span>
        <template #dropdown>
          <el-dropdown-menu>
            <el-dropdown-item command="profile">Profile</el-dropdown-item>
            <el-dropdown-item command="logout" divided>Logout</el-dropdown-item>
          </el-dropdown-menu>
        </template>
      </el-dropdown>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { ArrowDown } from '@element-plus/icons-vue'

const router = useRouter()
const authStore = useAuthStore()

const userAvatar = computed(() => {
  return authStore.user?.avatar || null
})

const handleCommand = async (command) => {
  if (command === 'logout') {
    await authStore.logout()
    router.push('/login')
  } else if (command === 'profile') {
    // Navigate to profile page
    router.push('/profile')
  }
}
</script>

<style scoped>
.app-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  height: 100%;
}

.header-left h2 {
  margin: 0;
  color: white;
  font-size: 1.5rem;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  color: white;
}

.username {
  font-size: 14px;
}

.user-info:hover {
  opacity: 0.8;
}
</style>
