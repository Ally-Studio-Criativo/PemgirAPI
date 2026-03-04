import { api } from 'src/boot/axios'

export const paletteService = {
  /**
   * Get all palettes
   */
  async getAll(params = {}) {
    const response = await api.get('/palettes', { params })
    return response.data
  },

  /**
   * Get palette by ID
   */
  async getById(id) {
    const response = await api.get(`/palettes/${id}`)
    return response.data
  },

  /**
   * Create new palette
   */
  async create(paletteData) {
    const response = await api.post('/palettes', paletteData)
    return response.data
  },

  /**
   * Update palette
   */
  async update(id, paletteData) {
    const response = await api.put(`/palettes/${id}`, paletteData)
    return response.data
  },

  /**
   * Delete or inactivate palette
   * If palette has colors, it will be inactivated instead of deleted
   */
  async delete(id) {
    const response = await api.delete(`/palettes/${id}`)
    return response.data
  }
}
