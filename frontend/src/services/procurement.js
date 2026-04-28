import { api } from './auth'

export default {
  // Procurement Requests
  getProcurementRequests: (params = {}) => 
    api.get('/procurement-requests', { params }).then(res => res.data),
  
  getProcurementRequest: (id) => 
    api.get(`/procurement-requests/${id}`).then(res => res.data),
  
  createProcurementRequest: (data) => 
    api.post('/procurement-requests', data).then(res => res.data),
  
  updateProcurementRequest: (id, data) => 
    api.put(`/procurement-requests/${id}`, data).then(res => res.data),
  
  deleteProcurementRequest: (id) => 
    api.delete(`/procurement-requests/${id}`).then(res => res.data),

  // Workflow Actions
  submitRequest: (id, data) => 
    api.post(`/procurement-requests/${id}/submit`, data).then(res => res.data),
  
  approveRequest: (id, data) => 
    api.post(`/procurement-requests/${id}/approve`, data).then(res => res.data),
  
  rejectRequest: (id, data) => 
    api.post(`/procurement-requests/${id}/reject`, data).then(res => res.data),

  // Vendors
  getVendors: (params = {}) => 
    api.get('/vendors', { params }).then(res => res.data),
  
  getVendor: (id) => 
    api.get(`/vendors/${id}`).then(res => res.data),
  
  createVendor: (data) => 
    api.post('/vendors', data).then(res => res.data),
  
  updateVendor: (id, data) => 
    api.put(`/vendors/${id}`, data).then(res => res.data),
  
  deleteVendor: (id) => 
    api.delete(`/vendors/${id}`).then(res => res.data),

  // Categories
  getCategories: () => 
    api.get('/categories').then(res => res.data),
  
  createCategory: (data) => 
    api.post('/categories', data).then(res => res.data),
  
  updateCategory: (id, data) => 
    api.put(`/categories/${id}`, data).then(res => res.data),
  
  deleteCategory: (id) => 
    api.delete(`/categories/${id}`).then(res => res.data),

  // Departments
  getDepartments: () => 
    api.get('/departments').then(res => res.data),
  
  createDepartment: (data) => 
    api.post('/departments', data).then(res => res.data),
  
  updateDepartment: (id, data) => 
    api.put(`/departments/${id}`, data).then(res => res.data),
  
  deleteDepartment: (id) => 
    api.delete(`/departments/${id}`).then(res => res.data),

  // Reports
  getProcurementSummary: (params = {}) => 
    api.get('/reports/procurement-summary', { params }).then(res => res.data),
  
  getVendorPerformance: (params = {}) => 
    api.get('/reports/vendor-performance', { params }).then(res => res.data),
  
  getBudgetAnalysis: (params = {}) => 
    api.get('/reports/budget-analysis', { params }).then(res => res.data),
}
