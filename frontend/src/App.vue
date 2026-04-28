<template>
  <div id="app">
    <el-container v-if="authStore.isAuthenticated">
      <el-header>
        <AppHeader />
      </el-header>
      <el-container>
        <el-aside width="250px">
          <AppSidebar />
        </el-aside>
        <el-main>
          <router-view />
        </el-main>
      </el-container>
    </el-container>
    <div v-else>
      <router-view />
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import AppHeader from '@/components/layout/AppHeader.vue'
import AppSidebar from '@/components/layout/AppSidebar.vue'

const authStore = useAuthStore()
</script>

<style>
#app {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: white;
  height: 100vh;
  background-color: #122B5A;
}

.el-header {
  background: linear-gradient(135deg, #122B5A 0%, #1e3a8a 100%);
  color: white;
  display: flex;
  align-items: center;
  padding: 0 20px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  font-weight: 600;
}

.el-aside {
  background-color: transparent;
  border-right: none;
}

.el-main {
  padding: 20px;
  background-color: #122B5A;
  background: linear-gradient(135deg, #122B5A 0%, #1e3a8a 100%);
  min-height: calc(100vh - 60px);
  color: white;
}

/* Override Element Plus default styles */
.el-container {
  background-color: #122B5A;
  color: white;
}

.el-container .el-container {
  background-color: #122B5A;
  color: white;
}

/* Proper contrast-based text styling */
/* Default text color for main content (dark background) */
h1, h2, h3, h4, h5, h6 {
  color: white !important;
  font-weight: 600;
}

p, span, div, label {
  color: white !important;
  font-weight: 500;
}

/* White background elements get black text */
.el-card,
.el-dialog,
.el-popper,
.el-select-dropdown,
.el-table,
.el-input__wrapper,
.el-textarea__inner,
.el-select__wrapper,
.el-picker-panel,
.el-message,
.el-notification {
  color: #333 !important;
}

/* Force black text on white backgrounds */
.el-card *,
.el-dialog *,
.el-popper *,
.el-select-dropdown *,
.el-table *,
.el-input__wrapper *,
.el-textarea__inner *,
.el-select__wrapper *,
.el-picker-panel *,
.el-message *,
.el-notification * {
  color: #333 !important;
}

/* Ensure main layout text stays white - HIGHER SPECIFICITY */
#app,
#app > .el-container,
#app > .el-container > .el-header,
#app > .el-container > .el-container > .el-aside,
#app > .el-container > .el-container > .el-main,
.el-header,
.el-aside,
.el-main,
.el-container {
  color: white !important;
}

/* Main layout children should stay white */
#app > .el-container > .el-header *,
#app > .el-container > .el-container > .el-aside *,
#app > .el-container > .el-container > .el-main > *,
.el-header > *,
.el-aside > *,
.el-main > * {
  color: white !important;
}

/* ONLY white background elements get black text */
.el-input__wrapper *,
.el-textarea__inner *,
.el-select__wrapper *,
.el-form-item__content *,
.el-form-item * {
  color: #333 !important;
}

/* Override any white text specifically */
.el-card .el-card__header *,
.el-card .el-card__body *,
.el-dialog .el-dialog__header *,
.el-dialog .el-dialog__body *,
.el-dialog .el-dialog__footer,
.el-form-item__label,
.el-form-item__content,
.el-input__wrapper,
.el-input__wrapper *,
.el-input__inner,
.el-textarea__inner,
.el-select__wrapper,
.el-select__wrapper *,
input, textarea, select {
  color: #333 !important;
  background: white !important;
  border: 1px solid #dcdfe6 !important;
  pointer-events: auto !important;
  cursor: text !important;
  user-select: text !important;
  -webkit-user-select: text !important;
  -moz-user-select: text !important;
  -ms-user-select: text !important;
}

/* Ensure input fields are clickable and focusable */
.el-input__wrapper:hover,
.el-input__wrapper:focus,
.el-input__wrapper:focus-within,
.el-textarea__inner:hover,
.el-textarea__inner:focus,
.el-select__wrapper:hover,
.el-select__wrapper:focus,
input:hover, input:focus,
textarea:hover, textarea:focus,
select:hover, select:focus {
  border-color: #409eff !important;
  outline: none !important;
  box-shadow: 0 0 0 2px rgba(64, 158, 255, 0.2) !important;
}

/* Remove any pointer-events issues */
.register-container *,
.register-container .el-input__wrapper,
.register-container .el-textarea__inner,
.register-container .el-select__wrapper,
.register-container input,
.register-container textarea,
.register-container select {
  pointer-events: auto !important;
  cursor: text !important;
  user-select: text !important;
}

/* Force black text on any element with rgba(255,255,255 background */
[style*="rgba(255, 255, 255"],
[style*="rgba(255,255,255"] {
  color: #333 !important;
}

[style*="rgba(255, 255, 255"] *,
[style*="rgba(255,255,255"] * {
  color: #333 !important;
}

/* Card styling - white cards with black text */
.el-card {
  background: rgba(255, 255, 255, 0.95) !important;
  color: #333 !important;
}

.el-card .el-card__header {
  color: #333 !important;
  font-weight: 600;
  background: rgba(255, 255, 255, 0.95) !important;
}

.el-card .el-card__body {
  color: #333 !important;
  background: rgba(255, 255, 255, 0.95) !important;
}

/* Button styling */
.el-button {
  font-weight: 600 !important;
}

/* Table styling - white background with black text */
.el-table {
  color: #333 !important;
  background: white !important;
}

.el-table th {
  color: #333 !important;
  font-weight: 600;
  background: #f5f5f5 !important;
}

.el-table td {
  color: #333 !important;
  background: white !important;
}

/* Form styling - white inputs with black text */
.el-form-item__label {
  color: white !important;
  font-weight: 600;
}

.el-input__inner {
  color: #333 !important;
  background: white !important;
  font-weight: 500;
}

/* Select dropdown styling */
.el-select-dropdown__item {
  color: #333 !important;
  background: white !important;
}

/* Tag styling */
.el-tag {
  color: white !important;
  font-weight: 600;
}

/* Stat card styling */
.stat-card-title {
  font-size: 1.2rem;
  font-weight: 700;
  color: #333 !important;
}

.stat-card-value {
  font-size: 2rem;
  font-weight: 800;
  color: #333 !important;
}

/* FINAL AGGRESSIVE OVERRIDE - Force black text on white backgrounds */
.el-card *,
.el-dialog *,
.el-table *,
.el-input__wrapper *,
.el-textarea__inner *,
.el-select__wrapper *,
.el-form-item__content *,
.el-form-item *,
.el-popover *,
.el-tooltip__popper *,
.el-select-dropdown *,
.el-picker-panel *,
.el-message *,
.el-notification *,
.el-drawer *,
.el-loading-mask *,
.el-overlay *,
[style*="background: white"] *,
[style*="background: #fff"] *,
[style*="background: #ffffff"] *,
[style*="background-color: white"] *,
[style*="background-color: #fff"] *,
[style*="background-color: #ffffff"] *,
[style*="rgba(255, 255, 255"] *,
[style*="rgba(255,255,255"] *,
.bg-white *,
.bg-light * {
  color: #333 !important;
}

/* Ensure main layout stays white */
#app,
.el-header,
.el-aside,
.el-main,
.el-container,
#app > *,
.el-header > *,
.el-aside > *,
.el-main > *,
.el-container > * {
  color: white !important;
}

/* Specific login button styling */
.login-container .el-button,
.login-container .el-button *,
.login-container .el-button--primary,
.login-container .el-button--primary *,
.login-container .el-form .el-button,
.login-container .el-form .el-button *,
button[type="submit"],
button[type="submit"] *,
.el-button--primary,
.el-button--primary * {
  color: white !important;
}

/* Additional button text overrides */
.el-button span,
.el-button .el-icon,
.el-button .el-button__text,
.el-button .el-button__icon {
  color: white !important;
}

/* Specific register button styling */
.register-container .el-button,
.register-container .el-button *,
.register-container .el-button--primary,
.register-container .el-button--primary *,
.register-container .el-form .el-button,
.register-container .el-form .el-button *,
.register-page .el-button,
.register-page .el-button *,
.register-page .el-button--primary,
.register-page .el-button--primary *,
.register-page .el-form .el-button,
.register-page .el-form .el-button *,
button[class*="register"],
button[class*="Register"],
button[id*="register"],
button[id*="Register"] {
  color: white !important;
}

/* Registration form buttons */
.el-form button[type="submit"],
.el-form button[type="submit"] *,
.el-form .el-button,
.el-form .el-button * {
  color: white !important;
}
</style>
