import { defineStore } from 'pinia'
import { api } from 'src/boot/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,
    loading: false,
    error: null
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    currentUser: (state) => state.user,
    isLoading: (state) => state.loading,
    hasError: (state) => state.error
  },

  actions: {
    // Initialize auth from localStorage
    initAuth() {
      const token = localStorage.getItem('auth_token')
      const user = localStorage.getItem('user')

      if (token && user) {
        this.token = token
        this.user = JSON.parse(user)
      }
    },

    // Login action
    async login(credentials) {
      this.loading = true
      this.error = null

      try {
        const response = await api.post('/login', credentials)

        const { user, access_token } = response.data

        // Save to state
        this.user = user
        this.token = access_token

        // Save to localStorage
        localStorage.setItem('auth_token', access_token)
        localStorage.setItem('user', JSON.stringify(user))

        return { success: true, user }
      } catch (error) {
        const errorMessage = error.response?.data?.message ||
                           error.response?.data?.errors?.email?.[0] ||
                           'Erro ao fazer login. Tente novamente.'

        this.error = errorMessage
        return { success: false, error: errorMessage }
      } finally {
        this.loading = false
      }
    },

    // Logout action
    async logout() {
      this.loading = true

      try {
        await api.post('/logout')
      } catch (error) {
        console.error('Erro ao fazer logout:', error)
      } finally {
        // Clear state
        this.user = null
        this.token = null
        this.error = null

        // Clear localStorage
        localStorage.removeItem('auth_token')
        localStorage.removeItem('user')

        this.loading = false
      }
    },

    // Get current user from API
    async fetchUser() {
      if (!this.token) return

      try {
        const response = await api.get('/me')
        this.user = response.data
        localStorage.setItem('user', JSON.stringify(response.data))
      } catch (error) {
        console.error('Erro ao buscar usuário:', error)
        this.logout()
      }
    },

    // Clear error
    clearError() {
      this.error = null
    }
  }
})
