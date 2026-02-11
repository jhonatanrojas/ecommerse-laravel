import { defineStore } from 'pinia';
import axios from 'axios';

// Initialize CSRF token
let csrfInitialized = false;

async function initializeCsrf() {
  if (!csrfInitialized) {
    try {
      await axios.get('/sanctum/csrf-cookie');
      csrfInitialized = true;
    } catch (error) {
      console.error('Error initializing CSRF:', error);
    }
  }
}

export const useCartStore = defineStore('cart', {
  state: () => ({
    cart: null,
    summary: null,
    loading: false,
    error: null,
    isDrawerOpen: false,
    addingItem: false,
    updatingItem: null,
    removingItem: null,
    applyingCoupon: false,
  }),

  getters: {
    items: (state) => state.cart?.items || [],
    itemCount: (state) => state.summary?.item_count || 0,
    subtotal: (state) => state.summary?.subtotal || 0,
    discount: (state) => state.summary?.discount || 0,
    tax: (state) => state.summary?.tax || 0,
    shippingCost: (state) => state.summary?.shipping_cost || 0,
    total: (state) => state.summary?.total || 0,
    coupon: (state) => state.cart?.coupon_code ? { code: state.cart.coupon_code } : null,
    isEmpty: (state) => !state.cart?.items || state.cart.items.length === 0,
  },

  actions: {
    async fetchCart() {
      this.loading = true;
      this.error = null;
      try {
        await initializeCsrf();
        const response = await axios.get('/api/cart');
        const data = response.data.data || response.data;
        
        // Handle both response formats
        if (data.cart && data.summary) {
          this.cart = data.cart;
          this.summary = data.summary;
        } else if (data.items !== undefined) {
          // Empty cart response
          this.cart = { items: data.items };
          this.summary = data.summary;
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Error al cargar el carrito';
        console.error('Error fetching cart:', error);
      } finally {
        this.loading = false;
      }
    },

    async addItem(productId, variantId = null, quantity = 1) {
      this.addingItem = true;
      this.error = null;
      try {
        await initializeCsrf();
        const response = await axios.post('/api/cart/items', {
          product_id: productId,
          variant_id: variantId,
          quantity: quantity,
        });
        
        const data = response.data.data || response.data;
        this.cart = data.cart;
        this.summary = data.summary;
        this.isDrawerOpen = true;
        
        return { success: true };
      } catch (error) {
        this.error = error.response?.data?.message || 'Error al añadir el producto';
        console.error('Error adding item:', error);
        return { success: false, error: this.error };
      } finally {
        this.addingItem = false;
      }
    },

    async updateItem(itemUuid, quantity) {
      this.updatingItem = itemUuid;
      this.error = null;
      try {
        await initializeCsrf();
        const response = await axios.put(`/api/cart/items/${itemUuid}`, {
          quantity: quantity,
        });
        
        const data = response.data.data || response.data;
        this.cart = data.cart;
        this.summary = data.summary;
        
        return { success: true };
      } catch (error) {
        this.error = error.response?.data?.message || 'Error al actualizar el producto';
        console.error('Error updating item:', error);
        return { success: false, error: this.error };
      } finally {
        this.updatingItem = null;
      }
    },

    async removeItem(itemUuid) {
      this.removingItem = itemUuid;
      this.error = null;
      try {
        await initializeCsrf();
        const response = await axios.delete(`/api/cart/items/${itemUuid}`);
        
        const data = response.data.data || response.data;
        this.cart = data.cart;
        this.summary = data.summary;
        
        return { success: true };
      } catch (error) {
        this.error = error.response?.data?.message || 'Error al eliminar el producto';
        console.error('Error removing item:', error);
        return { success: false, error: this.error };
      } finally {
        this.removingItem = null;
      }
    },

    async clearCart() {
      this.loading = true;
      this.error = null;
      try {
        await initializeCsrf();
        await axios.delete('/api/cart');
        this.cart = null;
        this.summary = null;
        return { success: true };
      } catch (error) {
        this.error = error.response?.data?.message || 'Error al vaciar el carrito';
        console.error('Error clearing cart:', error);
        return { success: false, error: this.error };
      } finally {
        this.loading = false;
      }
    },

    async applyCoupon(code) {
      this.applyingCoupon = true;
      this.error = null;
      try {
        await initializeCsrf();
        const response = await axios.post('/api/cart/coupon', {
          code: code,
        });
        
        const data = response.data.data || response.data;
        this.cart = data.cart;
        this.summary = data.summary;
        
        return { success: true, message: 'Cupón aplicado correctamente' };
      } catch (error) {
        this.error = error.response?.data?.message || 'Error al aplicar el cupón';
        console.error('Error applying coupon:', error);
        return { success: false, error: this.error };
      } finally {
        this.applyingCoupon = false;
      }
    },

    async removeCoupon() {
      this.applyingCoupon = true;
      this.error = null;
      try {
        await initializeCsrf();
        const response = await axios.delete('/api/cart/coupon');
        
        const data = response.data.data || response.data;
        this.cart = data.cart;
        this.summary = data.summary;
        
        return { success: true };
      } catch (error) {
        this.error = error.response?.data?.message || 'Error al eliminar el cupón';
        console.error('Error removing coupon:', error);
        return { success: false, error: this.error };
      } finally {
        this.applyingCoupon = false;
      }
    },

    openDrawer() {
      this.isDrawerOpen = true;
    },

    closeDrawer() {
      this.isDrawerOpen = false;
    },

    toggleDrawer() {
      this.isDrawerOpen = !this.isDrawerOpen;
    },
  },
});
