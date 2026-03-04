import { api } from 'src/boot/axios'

export const productService = {
  /**
   * Get all products (optionally filtered by category)
   */
  async getAll(params = {}) {
    const response = await api.get('/products', { params })
    return response.data
  },

  /**
   * Get product by ID
   */
  async getById(id) {
    const response = await api.get(`/products/${id}`)
    return response.data
  },

  /**
   * Create new product
   */
  async create(productData) {
    const response = await api.post('/products', productData)
    return response.data
  },

  /**
   * Update product
   */
  async update(id, productData) {
    const response = await api.put(`/products/${id}`, productData)
    return response.data
  },

  /**
   * Delete product
   */
  async delete(id) {
    const response = await api.delete(`/products/${id}`)
    return response.data
  },

  /**
   * Get all images for a product
   */
  async getImages(productId) {
    const response = await api.get(`/products/${productId}/images`)
    return response.data
  },

  /**
   * Upload a new image for a product
   */
  async uploadImage(productId, imageData) {
    const formData = new FormData()
    formData.append('image', imageData.image)

    if (imageData.ref) formData.append('ref', imageData.ref)
    if (imageData.color_name) formData.append('color_name', imageData.color_name)
    if (imageData.image_type) formData.append('image_type', imageData.image_type)
    if (imageData.order !== undefined) formData.append('order', imageData.order)

    const response = await api.post(`/products/${productId}/images`, formData)
    return response.data
  },

  /**
   * Update image metadata
   */
  async updateImage(productId, imageId, imageData) {
    const response = await api.put(`/products/${productId}/images/${imageId}`, imageData)
    return response.data
  },

  /**
   * Reorder product images
   */
  async reorderImages(productId, images) {
    const response = await api.post(`/products/${productId}/images/reorder`, { images })
    return response.data
  },

  /**
   * Delete an image
   */
  async deleteImage(productId, imageId) {
    const response = await api.delete(`/products/${productId}/images/${imageId}`)
    return response.data
  }
}
