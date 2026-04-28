import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import authService from '@/services/auth'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token'))

  const isAuthenticated = computed(() => !!token.value)

  const login = async (credentials) => {
    try {
      const response = await authService.login(credentials)
      user.value = response.user
      token.value = response.token
      localStorage.setItem('token', response.token)
      return response
    } catch (error) {
      throw error
    }
  }

  const register = async (userData) => {
    try {
      const response = await authService.register(userData)
      user.value = response.user
      token.value = response.token
      localStorage.setItem('token', response.token)
      return response
    } catch (error) {
      throw error
    }
  }

  const logout = async () => {
    try {
      await authService.logout()
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      user.value = null
      token.value = null
      localStorage.removeItem('token')
    }
    return Promise.resolve()
  }

  const getProfile = async () => {
    try {
      const response = await authService.getProfile()
      user.value = response.user
      return response
    } catch (error) {
      logout()
      throw error
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    login,
    register,
    logout,
    getProfile
  }
})
