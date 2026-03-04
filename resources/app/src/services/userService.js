import { api } from 'src/boot/axios'

export const userService = {
  /**
   * Get all users
   */
  async getAll() {
    const response = await api.get('/users')
    return response.data
  },

  /**
   * Get user by ID
   */
  async getById(id) {
    const response = await api.get(`/users/${id}`)
    return response.data
  },

  /**
   * Create new user
   */
  async create(userData) {
    const response = await api.post('/users', userData)
    return response.data
  },

  /**
   * Update user
   */
  async update(id, userData) {
    const response = await api.put(`/users/${id}`, userData)
    return response.data
  },

  /**
   * Delete user
   */
  async delete(id) {
    const response = await api.delete(`/users/${id}`)
    return response.data
  }
}
