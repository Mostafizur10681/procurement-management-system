<template>
  <aside class="main-sidebar" :class="{ 'collapsed': isCollapsed }">
    <div class="sidebar-header">
      <div class="sidebar-toggle" @click="toggleSidebar">
        <i class="fas fa-bars" v-if="!isCollapsed"></i>
        <i class="fas fa-times" v-else></i>
      </div>
      <div class="sidebar-logo" v-if="!isCollapsed">
        <div class="sidebar-logo-placeholder">PMS</div>
        <span class="logo-text">Procurement</span>
      </div>
      <div class="sidebar-logo-collapsed" v-else>
        <div class="sidebar-logo-small">P</div>
      </div>
    </div>
    
    <div class="sidebar-content">
      <nav class="sidebar-nav">
        <!-- Dashboard -->
        <router-link to="/dashboard" class="nav-item" :class="{ active: isActive('/dashboard') }">
          <i class="fas fa-home"></i>
          <span class="nav-text" v-if="!isCollapsed">Dashboard</span>
        </router-link>
        
         <!-- Procurement Setup -->
        <div class="nav-group">
          <div class="nav-item" @click="toggleSubmenu('procurement')" :class="{ active: isSubmenuActive('procurement') }">
            <i class="fas fa-cogs"></i>
            <span class="nav-text" v-if="!isCollapsed">Procurement Setup</span>
            <i class="fas fa-chevron-down submenu-arrow" :class="{ 'rotated': isSubmenuActive('procurement') }"></i>
          </div>
          <div class="submenu" :class="{ 'show': isSubmenuActive('procurement') }">
            <router-link to="/procurement-setup/workflow-team" class="submenu-item">
              <i class="fas fa-users"></i>
              <span class="nav-text" v-if="!isCollapsed">Workflow Team</span>
            </router-link>
            <router-link to="/procurement-setup/vendor-onboard" class="submenu-item">
              <i class="fas fa-user-plus"></i>
              <span class="nav-text" v-if="!isCollapsed">Vendor Onboard</span>
            </router-link>
            <router-link to="/procurement-setup/amount-range" class="submenu-item">
              <i class="fas fa-dollar-sign"></i>
              <span class="nav-text" v-if="!isCollapsed">Amount Range</span>
            </router-link>
            <router-link to="/procurement-setup/attachment-types" class="submenu-item">
              <i class="fas fa-paperclip"></i>
              <span class="nav-text" v-if="!isCollapsed">Attachment Types</span>
            </router-link>
            <router-link to="/procurement-setup/vendor-relationships" class="submenu-item">
              <i class="fas fa-handshake"></i>
              <span class="nav-text" v-if="!isCollapsed">Vendor Relationships</span>
            </router-link>
            <router-link to="/procurement-setup/publications" class="submenu-item">
              <i class="fas fa-newspaper"></i>
              <span class="nav-text" v-if="!isCollapsed">Publications</span>
            </router-link>
          </div>
        </div>
        
        <!-- Purchase Requests -->
        <div class="nav-group">
          <div class="nav-item" @click="toggleSubmenu('requests')" :class="{ active: isSubmenuActive('requests') }">
            <i class="fas fa-file-alt"></i>
            <span class="nav-text" v-if="!isCollapsed">Purchase Requests</span>
            <i class="fas fa-chevron-down submenu-arrow" :class="{ 'rotated': isSubmenuActive('requests') }"></i>
          </div>
          <div class="submenu" :class="{ 'show': isSubmenuActive('requests') }">
            <router-link to="/purchase-requisitions" class="submenu-item">
              <i class="fas fa-arrow-right"></i>
              <span class="nav-text" v-if="!isCollapsed">All Requests</span>
            </router-link>
            <router-link to="/purchase-requisitions/pending" class="submenu-item">
              <i class="fas fa-arrow-right"></i>
              <span class="nav-text" v-if="!isCollapsed">Pending Requests</span>
            </router-link>
            <router-link to="/purchase-requisitions/approved" class="submenu-item">
              <i class="fas fa-arrow-right"></i>
              <span class="nav-text" v-if="!isCollapsed">Approved Requests</span>
            </router-link>
          </div>
        </div>
        
        <!-- Purchase Orders -->
        <div class="nav-group">
          <div class="nav-item" @click="toggleSubmenu('orders')" :class="{ active: isSubmenuActive('orders') }">
            <i class="fas fa-file-invoice"></i>
            <span class="nav-text" v-if="!isCollapsed">Purchase Orders</span>
            <i class="fas fa-chevron-down submenu-arrow" :class="{ 'rotated': isSubmenuActive('orders') }"></i>
          </div>
          <div class="submenu" :class="{ 'show': isSubmenuActive('orders') }">
            <router-link to="/purchase-orders" class="submenu-item">
              <i class="fas fa-arrow-right"></i>
              <span class="nav-text" v-if="!isCollapsed">All Orders</span>
            </router-link>
            <router-link to="/purchase-orders/pending" class="submenu-item">
              <i class="fas fa-arrow-right"></i>
              <span class="nav-text" v-if="!isCollapsed">Pending Orders</span>
            </router-link>
            <router-link to="/purchase-orders/approved" class="submenu-item">
              <i class="fas fa-arrow-right"></i>
              <span class="nav-text" v-if="!isCollapsed">Approved Orders</span>
            </router-link>
          </div>
        </div>
        
        <!-- Vendors -->
        <div class="nav-group">
          <div class="nav-item" @click="toggleSubmenu('vendors')" :class="{ active: isSubmenuActive('vendors') }">
            <i class="fas fa-users"></i>
            <span class="nav-text" v-if="!isCollapsed">Vendors</span>
            <i class="fas fa-chevron-down submenu-arrow" :class="{ 'rotated': isSubmenuActive('vendors') }"></i>
          </div>
          <div class="submenu" :class="{ 'show': isSubmenuActive('vendors') }">
            <router-link to="/vendors" class="submenu-item">
              <i class="fas fa-arrow-right"></i>
              <span class="nav-text" v-if="!isCollapsed">All Vendors</span>
            </router-link>
            <router-link to="/vendors/register" class="submenu-item">
              <i class="fas fa-arrow-right"></i>
              <span class="nav-text" v-if="!isCollapsed">Register Vendor</span>
            </router-link>
          </div>
        </div>
        
        <!-- Reports -->
        <div class="nav-group">
          <div class="nav-item" @click="toggleSubmenu('reports')" :class="{ active: isSubmenuActive('reports') }">
            <i class="fas fa-chart-bar"></i>
            <span class="nav-text" v-if="!isCollapsed">Reports</span>
            <i class="fas fa-chevron-down submenu-arrow" :class="{ 'rotated': isSubmenuActive('reports') }"></i>
          </div>
          <div class="submenu" :class="{ 'show': isSubmenuActive('reports') }">
            <router-link to="/reports/purchase" class="submenu-item">
              <i class="fas fa-arrow-right"></i>
              <span class="nav-text" v-if="!isCollapsed">Purchase Reports</span>
            </router-link>
            <router-link to="/reports/vendor" class="submenu-item">
              <i class="fas fa-arrow-right"></i>
              <span class="nav-text" v-if="!isCollapsed">Vendor Reports</span>
            </router-link>
            <router-link to="/reports/summary" class="submenu-item">
              <i class="fas fa-arrow-right"></i>
              <span class="nav-text" v-if="!isCollapsed">Summary Reports</span>
            </router-link>
          </div>
        </div>
        
        <!-- Settings -->
        <div class="nav-group">
          <div class="nav-item" @click="toggleSubmenu('settings')" :class="{ active: isSubmenuActive('settings') }">
            <i class="fas fa-cog"></i>
            <span class="nav-text" v-if="!isCollapsed">Settings</span>
            <i class="fas fa-chevron-down submenu-arrow" :class="{ 'rotated': isSubmenuActive('settings') }"></i>
          </div>
          <div class="submenu" :class="{ 'show': isSubmenuActive('settings') }">
            <router-link to="/settings/company" class="submenu-item">
              <i class="fas fa-arrow-right"></i>
              <span class="nav-text" v-if="!isCollapsed">Company Settings</span>
            </router-link>
            <router-link to="/settings/users" class="submenu-item">
              <i class="fas fa-arrow-right"></i>
              <span class="nav-text" v-if="!isCollapsed">User Management</span>
            </router-link>
            <router-link to="/settings/system" class="submenu-item">
              <i class="fas fa-arrow-right"></i>
              <span class="nav-text" v-if="!isCollapsed">System Settings</span>
            </router-link>
          </div>
        </div>
        
        <!-- User Manual -->
        <div class="nav-group">
          <div class="nav-item" @click="toggleSubmenu('manual')" :class="{ active: isSubmenuActive('manual') }">
            <i class="fas fa-file-alt"></i>
            <span class="nav-text" v-if="!isCollapsed">User Manual</span>
            <i class="fas fa-chevron-down submenu-arrow" :class="{ 'rotated': isSubmenuActive('manual') }"></i>
          </div>
          <div class="submenu" :class="{ 'show': isSubmenuActive('manual') }">
            <a href="/manual/bn.pdf" class="submenu-item" target="_blank">
              <i class="fas fa-arrow-right"></i>
              <span class="nav-text" v-if="!isCollapsed">Bengali Version</span>
            </a>
            <a href="/manual/en.pdf" class="submenu-item" target="_blank">
              <i class="fas fa-arrow-right"></i>
              <span class="nav-text" v-if="!isCollapsed">English Version</span>
            </a>
          </div>
        </div>
      </nav>
    </div>
  </aside>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const isCollapsed = ref(false)
const activeSubmenu = ref('')

const isActive = (path) => {
  return route.path === path
}

const isSubmenuActive = (submenu) => {
  return activeSubmenu.value === submenu
}

const toggleSidebar = () => {
  isCollapsed.value = !isCollapsed.value
}

const toggleSubmenu = (submenu) => {
  if (isCollapsed.value) {
    isCollapsed.value = false
  }
  activeSubmenu.value = activeSubmenu.value === submenu ? '' : submenu
}

onMounted(() => {
  // Set active submenu based on current route
  const path = route.path
  if (path.includes('/purchase-requisitions')) {
    activeSubmenu.value = 'requests'
  } else if (path.includes('/purchase-orders')) {
    activeSubmenu.value = 'orders'
  } else if (path.includes('/vendors')) {
    activeSubmenu.value = 'vendors'
  } else if (path.includes('/reports')) {
    activeSubmenu.value = 'reports'
  } else if (path.includes('/settings')) {
    activeSubmenu.value = 'settings'
  } else if (path.includes('/manual')) {
    activeSubmenu.value = 'manual'
  } else if (path.includes('/procurement-setup')) {
    activeSubmenu.value = 'procurement'
  }
})
</script>

<style scoped>
.main-sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 280px;
  height: 100vh;
  background: #163069;
  z-index: 1000;
  transition: all 0.3s ease;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

.main-sidebar.collapsed {
  width: 80px;
}

.sidebar-header {
  display: flex;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  position: relative;
}

.sidebar-toggle {
  position: absolute;
  top: 20px;
  right: 20px;
  cursor: pointer;
  color: white;
  font-size: 18px;
  padding: 5px;
  border-radius: 5px;
  transition: background 0.3s ease;
}

.sidebar-toggle:hover {
  background: rgba(255, 255, 255, 0.1);
}

.sidebar-logo {
  display: flex;
  align-items: center;
}

.sidebar-logo-placeholder {
  height: 35px;
  margin-right: 10px;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  font-weight: bold;
  font-size: 16px;
  width: 35px;
}

.sidebar-logo-small {
  height: 30px;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  font-weight: bold;
  font-size: 14px;
  width: 30px;
}

.logo-text {
  font-size: 18px;
  font-weight: 600;
  color: white;
}

.sidebar-logo-collapsed {
  display: flex;
  justify-content: center;
}

.logo-img-small {
  height: 30px;
}

.sidebar-content {
  padding: 20px 0;
  overflow-y: auto;
  height: calc(100vh - 80px);
}

.sidebar-nav {
  list-style: none;
  padding: 0;
  margin: 0;
}

.nav-item {
  display: flex;
  align-items: center;
  color: white;
  text-decoration: none;
  padding: 15px 20px;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  font-weight: 500;
}

.nav-item:hover {
  background: rgba(255, 255, 255, 0.1);
  transform: translateX(5px);
}

.nav-item.active {
  background: rgba(255, 255, 255, 0.2);
  border-left: 3px solid #4CAF50;
}

.nav-item i {
  margin-right: 15px;
  width: 20px;
  text-align: center;
  font-size: 16px;
}

.nav-text {
  font-size: 14px;
  font-weight: 500;
}

.submenu-arrow {
  margin-left: auto;
  transition: transform 0.3s ease;
  font-size: 12px;
}

.submenu-arrow.rotated {
  transform: rotate(180deg);
}

.nav-group {
  margin-bottom: 5px;
}

.submenu {
  background: rgba(0, 0, 0, 0.1);
  border-radius: 8px;
  margin: 5px 10px;
  overflow: hidden;
  max-height: 0;
  transition: max-height 0.3s ease;
}

.submenu.show {
  max-height: 500px;
}

.submenu-item {
  display: flex;
  align-items: center;
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  padding: 12px 20px 12px 40px;
  transition: all 0.3s ease;
  font-size: 13px;
  border-radius: 6px;
  margin: 2px 5px;
}

.submenu-item:hover {
  background: rgba(255, 255, 255, 0.1);
  color: white;
  transform: translateX(3px);
}

.submenu-item.router-link-active {
  color: #4CAF50;
  background: rgba(76, 175, 80, 0.1);
}

.submenu-item i {
  margin-right: 10px;
  font-size: 12px;
}

/* Collapsed state */
.main-sidebar.collapsed .nav-text,
.main-sidebar.collapsed .submenu-arrow {
  display: none;
}

.main-sidebar.collapsed .nav-item {
  justify-content: center;
  padding: 15px;
}

.main-sidebar.collapsed .submenu {
  display: none;
}

/* Mobile responsive */
@media (max-width: 768px) {
  .main-sidebar {
    transform: translateX(-100%);
  }
  
  .main-sidebar.show {
    transform: translateX(0);
  }
  
  .sidebar-toggle {
    display: block;
  }
}

@media (min-width: 769px) {
  .sidebar-toggle {
    display: none;
  }
}
</style>
