import { api } from 'src/boot/axios'

export const colorService = {
  /**
   * Get all colors (optionally filtered by year)
   */
  async getAll(params = {}) {
    const response = await api.get('/colors', { params })
    return response.data
  },

  /**
   * Get color by ID
   */
  async getById(id) {
    const response = await api.get(`/colors/${id}`)
    return response.data
  },

  /**
   * Create new color with image upload
   */
  async create(colorData) {
    const formData = new FormData()
    formData.append('palette_id', colorData.palette_id)
    formData.append('name', colorData.name)
    formData.append('order', colorData.order)

    if (colorData.ref) {
      formData.append('ref', colorData.ref)
    }

    if (colorData.description) {
      formData.append('description', colorData.description)
    }

    if (colorData.type) {
      formData.append('type', colorData.type)
    }

    if (colorData.image) {
      formData.append('image', colorData.image)
    }

    const response = await api.post('/colors', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    return response.data
  },

  /**
   * Update color
   */
  async update(id, colorData) {
    const formData = new FormData()
    formData.append('palette_id', colorData.palette_id)
    formData.append('name', colorData.name)
    formData.append('order', colorData.order)

    if (colorData.ref) {
      formData.append('ref', colorData.ref)
    }

    if (colorData.description) {
      formData.append('description', colorData.description)
    }

    if (colorData.type) {
      formData.append('type', colorData.type)
    }

    if (colorData.image) {
      formData.append('image', colorData.image)
    }

    // Laravel accepts PUT via POST with _method
    formData.append('_method', 'PUT')

    const response = await api.post(`/colors/${id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    return response.data
  },

  /**
   * Delete color
   */
  async delete(id) {
    const response = await api.delete(`/colors/${id}`)
    return response.data
  }
}
