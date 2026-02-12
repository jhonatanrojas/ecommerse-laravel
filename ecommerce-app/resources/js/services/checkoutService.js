import api from './api';

/**
 * Checkout Service
 * Handles all API calls related to cart and checkout
 */

export const checkoutService = {
  /**
   * Get store configuration
   * @returns {Promise} Store config
   */
  async getStoreConfig() {
    const response = await api.get('/store-config');
    return response.data;
  },

  /**
   * Get current cart
   * @returns {Promise} Cart data
   */
  async getCart() {
    const response = await api.get('/cart');
    return response.data;
  },

  /**
   * Submit checkout
   * @param {Object} payload - Checkout data
   * @returns {Promise} Order data
   */
  async submitCheckout(payload) {
    const response = await api.post('/cart/checkout', payload);
    return response.data;
  },

  /**
   * Add item to cart
   * @param {Object} data - Item data
   * @returns {Promise} Updated cart
   */
  async addItem(data) {
    const response = await api.post('/cart/items', data);
    return response.data;
  },

  /**
   * Update cart item
   * @param {string} uuid - Item UUID
   * @param {Object} data - Update data
   * @returns {Promise} Updated cart
   */
  async updateItem(uuid, data) {
    const response = await api.put(`/cart/items/${uuid}`, data);
    return response.data;
  },

  /**
   * Remove item from cart
   * @param {string} uuid - Item UUID
   * @returns {Promise} Updated cart
   */
  async removeItem(uuid) {
    const response = await api.delete(`/cart/items/${uuid}`);
    return response.data;
  },

  /**
   * Clear cart
   * @returns {Promise}
   */
  async clearCart() {
    const response = await api.delete('/cart');
    return response.data;
  },

  /**
   * Apply coupon
   * @param {string} code - Coupon code
   * @returns {Promise} Updated cart
   */
  async applyCoupon(code) {
    const response = await api.post('/cart/coupon', { code });
    return response.data;
  },

  /**
   * Remove coupon
   * @returns {Promise} Updated cart
   */
  async removeCoupon() {
    const response = await api.delete('/cart/coupon');
    return response.data;
  },

  /**
   * Get user addresses
   * @returns {Promise} User addresses
   */
  async getUserAddresses() {
    const response = await api.get('/customer/addresses');
    return response.data;
  },
};

export default checkoutService;
