import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/auth/Login.vue'),
    meta: { requiresGuest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/views/auth/Register.vue'),
    meta: { requiresGuest: true }
  },
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('@/views/Dashboard.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/procurement-requests',
    name: 'ProcurementRequests',
    component: () => import('@/views/procurement/ProcurementRequests.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/procurement-requests/create',
    name: 'CreateProcurementRequest',
    component: () => import('@/views/procurement/CreateProcurementRequest.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/procurement-requests/:id',
    name: 'ProcurementRequestDetail',
    component: () => import('@/views/procurement/ProcurementRequestDetail.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/vendors',
    name: 'Vendors',
    component: () => import('@/views/vendors/Vendors.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/vendors/create',
    name: 'CreateVendor',
    component: () => import('@/views/vendors/CreateVendor.vue'),
    meta: { requiresAuth: true, requiresRole: ['admin', 'manager'] }
  },
  {
    path: '/vendors/:id',
    name: 'VendorDetail',
    component: () => import('@/views/vendors/VendorDetail.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/categories',
    name: 'Categories',
    component: () => import('@/views/admin/Categories.vue'),
    meta: { requiresAuth: true, requiresRole: ['admin'] }
  },
  {
    path: '/departments',
    name: 'Departments',
    component: () => import('@/views/admin/Departments.vue'),
    meta: { requiresAuth: true, requiresRole: ['admin'] }
  },
  {
    path: '/reports',
    name: 'Reports',
    component: () => import('@/views/reports/Reports.vue'),
    meta: { requiresAuth: true, requiresRole: ['admin', 'manager'] }
  },
  // Setup Routes
  {
    path: '/workflows',
    name: 'Workflows',
    component: () => import('@/views/setup/Workflows.vue'),
    meta: { requiresAuth: true, requiresRole: ['admin', 'manager'] }
  },
  {
    path: '/committees',
    name: 'Committees',
    component: () => import('@/views/setup/Committees.vue'),
    meta: { requiresAuth: true, requiresRole: ['admin', 'manager'] }
  },
  {
    path: '/attachment-types',
    name: 'AttachmentTypes',
    component: () => import('@/views/setup/AttachmentTypes.vue'),
    meta: { requiresAuth: true, requiresRole: ['admin', 'manager'] }
  },
  {
    path: '/publications',
    name: 'Publications',
    component: () => import('@/views/setup/Publications.vue'),
    meta: { requiresAuth: true, requiresRole: ['admin', 'manager'] }
  },
  {
    path: '/vendor-relationships',
    name: 'VendorRelationships',
    component: () => import('@/views/setup/VendorRelationships.vue'),
    meta: { requiresAuth: true, requiresRole: ['admin', 'manager'] }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next('/dashboard')
  } else if (to.meta.requiresRole && !to.meta.requiresRole.includes(authStore.user?.role)) {
    next('/dashboard')
  } else {
    next()
  }
})

export default router
