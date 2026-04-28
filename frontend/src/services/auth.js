import axios from 'axios'

const API_BASE_URL = '/api'

const api = axios.create({
  baseURL: API_BASE_URL,
})

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export default {
  login: (credentials) => api.post('/login', credentials).then(res => res.data),
  register: (userData) => api.post('/register', userData).then(res => res.data),
  logout: () => api.post('/logout').then(res => res.data),
  getProfile: () => api.get('/user').then(res => res.data),
}

export { api }
