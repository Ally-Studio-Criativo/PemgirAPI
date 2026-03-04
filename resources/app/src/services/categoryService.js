import { api } from 'src/boot/axios'

export const categoryService = {
  /**
   * Get all categories
   */
  async getAll() {
    const response = await api.get('/categories')
    return response.data
  },

  /**
   * Get category by ID
   */
  async getById(id) {
    const response = await api.get(`/categories/${id}`)
    return response.data
  },

  /**
   * Create new category
   */
  async create(categoryData) {
    const response = await api.post('/categories', categoryData)
    return response.data
  },

  /**
   * Update category
   */
  async update(id, categoryData) {
    const response = await api.put(`/categories/${id}`, categoryData)
    return response.data
  },

  /**
   * Delete category
   */
  async delete(id) {
    const response = await api.delete(`/categories/${id}`)
    return response.data
  }
}
