<template>
  <el-menu
    :default-active="$route.path"
    class="sidebar-menu"
    router
  >
    <el-menu-item index="/dashboard">
      <el-icon><House /></el-icon>
      <span>Dashboard</span>
    </el-menu-item>

    <el-sub-menu index="procurement">
      <template #title>
        <el-icon><Document /></el-icon>
        <span>Procurement</span>
      </template>
      <el-menu-item index="/procurement-requests">
        <el-icon><List /></el-icon>
        <span>Requests</span>
      </el-menu-item>
      <el-menu-item index="/procurement-requests/create">
        <el-icon><Plus /></el-icon>
        <span>New Request</span>
      </el-menu-item>
    </el-sub-menu>

    <el-menu-item index="/vendors">
      <el-icon><Shop /></el-icon>
      <span>Vendors</span>
    </el-menu-item>

    <el-sub-menu index="setup" v-if="['admin', 'manager'].includes(authStore.user?.role)">
      <template #title>
        <el-icon><Setting /></el-icon>
        <span>Setup</span>
      </template>
      <el-menu-item index="/workflows">
        <el-icon><Operation /></el-icon>
        <span>Workflow Setup</span>
      </el-menu-item>
      <el-menu-item index="/committees">
        <el-icon><UserFilled /></el-icon>
        <span>Committee Setup</span>
      </el-menu-item>
      <el-menu-item index="/attachment-types">
        <el-icon><Paperclip /></el-icon>
        <span>Attachment Types</span>
      </el-menu-item>
      <el-menu-item index="/publications">
        <el-icon><Promotion /></el-icon>
        <span>Publications</span>
      </el-menu-item>
      <el-menu-item index="/vendor-relationships">
        <el-icon><Connection /></el-icon>
        <span>Vendor Relationships</span>
      </el-menu-item>
      <el-menu-item index="/categories">
        <el-icon><Collection /></el-icon>
        <span>Categories</span>
      </el-menu-item>
      <el-menu-item index="/departments">
        <el-icon><OfficeBuilding /></el-icon>
        <span>Departments</span>
      </el-menu-item>
    </el-sub-menu>

    <el-menu-item index="/reports" v-if="['admin', 'manager'].includes(authStore.user?.role)">
      <el-icon><DataAnalysis /></el-icon>
      <span>Reports</span>
    </el-menu-item>
  </el-menu>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { 
  House, 
  Document, 
  List, 
  Plus, 
  Shop, 
  Setting, 
  Collection, 
  OfficeBuilding, 
  DataAnalysis,
  Operation,
  UserFilled,
  Paperclip,
  Promotion,
  Connection
} from '@element-plus/icons-vue'

const authStore = useAuthStore()
</script>

<style scoped>
.el-container > .el-aside {
  background: #163069 !important;
}

.sidebar-menu {
  height: 100%;
  border-right: none;
  background: #163069 !important;
  border-radius: 0 24px 24px 0;
  box-shadow: 
    8px 0 32px rgba(102, 126, 234, 0.3),
    4px 0 16px rgba(118, 75, 162, 0.2),
    inset -1px 0 0 rgba(255, 255, 255, 0.1);
  padding: 24px 0;
  position: relative;
  overflow: hidden;
}

@keyframes sidebarGradientShift {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

.sidebar-menu::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
  pointer-events: none;
}

/* Override Element Plus default styles with higher specificity */
.sidebar-menu .el-menu-item,
.sidebar-menu .el-menu-item span,
.sidebar-menu .el-menu-item .el-icon {
  color: 163069 !important;
  font-weight: 600 !important;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.sidebar-menu .el-sub-menu__title,
.sidebar-menu .el-sub-menu__title span,
.sidebar-menu .el-sub-menu__title .el-icon {
  color: 163069 !important;
  font-weight: 600 !important;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.sidebar-menu .el-menu-item, .el-menu {
  border-radius: 16px;
  margin: 6px 16px;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
  backdrop-filter: blur(10px);
  background: #163069;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-menu .el-menu-item::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.6s ease;
}


.sidebar-menu .el-menu-item.is-active {
  background: #163069;
  color: rgb(255, 255, 255);
  font-weight: 700;
}

@keyframes activePulse {
  0%, 100% { 
    box-shadow: 
      0 8px 30px rgba(255, 255, 255, 0.2),
      0 4px 15px rgba(102, 126, 234, 0.4),
      inset 0 1px 0 rgba(255, 255, 255, 0.3);
  }
  50% { 
    box-shadow: 
      0 8px 40px rgba(255, 255, 255, 0.3),
      0 4px 20px rgba(102, 126, 234, 0.6),
      inset 0 1px 0 rgba(255, 255, 255, 0.4);
  }
}

/* Override Element Plus sub-menu styles */
.sidebar-menu .el-sub-menu,
.sidebar-menu .el-sub-menu__title,
.sidebar-menu .el-sub-menu__title span,
.sidebar-menu .el-sub-menu__title .el-icon {
  color: white !important;
  font-weight: 600 !important;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.sidebar-menu .el-sub-menu {
  border-radius: 16px;
  margin: 6px 16px;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
  backdrop-filter: blur(10px);
  background: #163069;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-menu .el-sub-menu__title {
  border-radius: 16px;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
  backdrop-filter: blur(10px);
  background: #163069;
  border: 1px solid rgba(255, 255, 255, 0.1);
}


.sidebar-menu .el-sub-menu.is-opened .el-sub-menu__title {
  background: #163069;
  color: white;
  font-weight: 700;
}

/* Override Element Plus sub-menu items styles */
.sidebar-menu .el-sub-menu .el-menu-item,
.sidebar-menu .el-sub-menu .el-menu-item span,
.sidebar-menu .el-sub-menu .el-menu-item .el-icon {
  color: #1A3579 !important;
  font-weight: 500 !important;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.sidebar-menu .el-sub-menu .el-menu-item {
  background: #1A3579;
  margin: 3px 12px;
  padding-left: 52px !important;
  font-size: 14px;
  border-radius: 12px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  backdrop-filter: blur(5px);
}


.sidebar-menu .el-sub-menu .el-menu-item.is-active {
  background: #163069;
  color: #163069;
  font-weight: 700;
}

.sidebar-menu .el-icon {
  font-size: 20px;
  margin-right: 14px;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.2));
}


.sidebar-menu .el-menu-item span,
.sidebar-menu .el-sub-menu__title span {
  font-weight: 500;
  letter-spacing: 0.3px;
}

/* Sub-menu arrow animation */
.sidebar-menu .el-sub-menu .el-icon-arrow {
  transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.2));
}

.sidebar-menu .el-sub-menu.is-opened .el-icon-arrow {
  transform: rotate(180deg) scale(1.1);
  filter: drop-shadow(0 2px 4px rgba(102, 126, 234, 0.4));
}

/* Active state animation */
.sidebar-menu .el-menu-item.is-active::after {
  content: '';
  position: absolute;
  top: 50%;
  right: 16px;
  width: 8px;
  height: 8px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  transform: translateY(-50%);
  animation: activePulse 2s infinite;
  box-shadow: 0 0 10px rgba(102, 126, 234, 0.5);
}

@keyframes activePulse {
  0% { 
    transform: translateY(-50%) scale(1);
    box-shadow: 0 0 10px rgba(102, 126, 234, 0.5);
  }
  50% { 
    transform: translateY(-50%) scale(1.2);
    box-shadow: 0 0 20px rgba(102, 126, 234, 0.8);
  }
  100% { 
    transform: translateY(-50%) scale(1);
    box-shadow: 0 0 10px rgba(102, 126, 234, 0.5);
  }
}

/* Enhanced menu text styling */
.sidebar-menu .el-menu-item span,
.sidebar-menu .el-sub-menu__title span {
  color: white !important;
  font-weight: 700;
  letter-spacing: 0.5px;
  text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
}

.sidebar-menu .el-menu-item.is-active span,
.sidebar-menu .el-sub-menu .el-menu-item.is-active span {
  color: white !important;
  font-weight: 800;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
  letter-spacing: 0.7px;
}

/* Floating particles effect */
.sidebar-menu::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.03) 0%, transparent 50%),
              radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.02) 0%, transparent 50%),
              radial-gradient(circle at 40% 20%, rgba(255, 255, 255, 0.03) 0%, transparent 50%);
  pointer-events: none;
  animation: particlesFloat 15s ease-in-out infinite;
}

@keyframes particlesFloat {
  0%, 100% { opacity: 0.5; }
  50% { opacity: 0.8; }
}
</style>
