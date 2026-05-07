import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token'))

  const isAuthenticated = computed(() => !!token.value)

  // Get users from localStorage or initialize with default data
  const getUsers = () => {
    const storedUsers = localStorage.getItem('users')
    if (storedUsers) {
      return JSON.parse(storedUsers)
    }
    
    // Initialize with default users
    const defaultUsers = [
      {
        id: 1,
        name: "Admin User",
        email: "admin@example.com",
        role: "admin",
        password: "password123",
        created_at: new Date().toISOString()
      },
      {
        id: 2,
        name: "Manager User", 
        email: "manager@example.com",
        role: "manager",
        password: "password123",
        created_at: new Date().toISOString()
      },
      {
        id: 3,
        name: "Regular User",
        email: "user@example.com", 
        role: "user",
        password: "password123",
        created_at: new Date().toISOString()
      }
    ]
    
    localStorage.setItem('users', JSON.stringify(defaultUsers))
    return defaultUsers
  }

  // Save users to localStorage
  const saveUsers = (users) => {
    localStorage.setItem('users', JSON.stringify(users))
  }

  const login = async (credentials) => {
    try {
      const users = getUsers()
      const foundUser = users.find(u => u.email === credentials.email && u.password === credentials.password)
      
      if (foundUser) {
        const userResponse = { ...foundUser }
        delete userResponse.password // Remove password from response
        
        user.value = userResponse
        token.value = btoa(foundUser.id + ':' + Date.now())
        localStorage.setItem('token', token.value)
        localStorage.setItem('currentUser', JSON.stringify(userResponse))
        
        return { user: userResponse, token: token.value }
      } else {
        throw new Error('Invalid credentials')
      }
    } catch (error) {
      throw error
    }
  }

  const register = async (userData) => {
    try {
      const users = getUsers()
      
      // Check if email already exists
      if (users.find(u => u.email === userData.email)) {
        throw new Error('Email already exists')
      }
      
      // Create new user
      const newUser = {
        id: users.length + 1,
        name: userData.name,
        email: userData.email,
        role: userData.role || 'user',
        password: userData.password,
        created_at: new Date().toISOString()
      }
      
      users.push(newUser)
      saveUsers(users)
      
      const userResponse = { ...newUser }
      delete userResponse.password // Remove password from response
      
      user.value = userResponse
      token.value = btoa(newUser.id + ':' + Date.now())
      localStorage.setItem('token', token.value)
      localStorage.setItem('currentUser', JSON.stringify(userResponse))
      
      return { user: userResponse, token: token.value }
    } catch (error) {
      throw error
    }
  }

  const logout = async () => {
    user.value = null
    token.value = null
    localStorage.removeItem('token')
    localStorage.removeItem('currentUser')
    return Promise.resolve()
  }

  const getProfile = async () => {
    try {
      const storedUser = localStorage.getItem('currentUser')
      if (storedUser) {
        user.value = JSON.parse(storedUser)
        return { user: user.value }
      } else {
        throw new Error('No user found')
      }
    } catch (error) {
      logout()
      throw error
    }
  }

  // Initialize user from localStorage if token exists
  const initializeAuth = () => {
    const storedUser = localStorage.getItem('currentUser')
    if (storedUser && token.value) {
      user.value = JSON.parse(storedUser)
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    login,
    register,
    logout,
    getProfile,
    initializeAuth
  }
})
