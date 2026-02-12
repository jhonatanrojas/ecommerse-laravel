import { defineStore } from 'pinia';
import api from '../services/api';

function normalizeApiError(error) {
  const status = error?.response?.status ?? null;
  const message =
    error?.response?.data?.message ||
    (status === 401 ? 'No autorizado. Inicia sesión nuevamente.' : null) ||
    'Ocurrió un error inesperado. Intenta de nuevo.';

  const fields = error?.validationErrors || error?.response?.data?.errors || {};

  return { status, message, fields };
}

export const useCustomerStore = defineStore('customer', {
  state: () => ({
    user: null,
    addresses: [],
    orders: [],
    currentOrder: null,
    loading: {
      user: false,
      addresses: false,
      orders: false,
      orderDetail: false,
      password: false,
      addressMutation: false,
      defaultAddress: false,
    },
    errors: {
      message: null,
      fields: {},
    },
  }),

  getters: {
    hasErrors: (state) => Boolean(state.errors?.message) || Object.keys(state.errors?.fields || {}).length > 0,
    isLoading: (state) => Object.values(state.loading).some(Boolean),
    shippingAddresses: (state) => state.addresses.filter((a) => a.type === 'shipping'),
    billingAddresses: (state) => state.addresses.filter((a) => a.type === 'billing'),
    defaultShippingAddress: (state) => state.shippingAddresses.find((a) => a.is_default) || null,
    defaultBillingAddress: (state) => state.billingAddresses.find((a) => a.is_default) || null,
  },

  actions: {
    clearErrors() {
      this.errors = { message: null, fields: {} };
    },

    setError(error) {
      const normalized = normalizeApiError(error);
      this.errors = { message: normalized.message, fields: normalized.fields || {} };
      return normalized;
    },

    getFieldError(field) {
      return this.errors.fields?.[field]?.[0] || null;
    },

    async fetchUser() {
      this.loading.user = true;
      this.clearErrors();
      try {
        const { data } = await api.get('/user');
        // El backend devuelve UserResource (posiblemente envuelto). Normalizamos.
        this.user = data?.data ?? data;
        return { success: true, user: this.user };
      } catch (error) {
        const normalized = this.setError(error);
        this.user = null;
        return { success: false, ...normalized };
      } finally {
        this.loading.user = false;
      }
    },

    async updatePassword(payload) {
      this.loading.password = true;
      this.clearErrors();
      try {
        const { data } = await api.put('/user/password', payload);
        return { success: true, message: data?.message || 'Contraseña actualizada.' };
      } catch (error) {
        const normalized = this.setError(error);
        return { success: false, ...normalized };
      } finally {
        this.loading.password = false;
      }
    },

    async fetchOrders(params = {}) {
      this.loading.orders = true;
      this.clearErrors();
      try {
        const { data } = await api.get('/customer/orders', { params });
        this.orders = data?.data ?? data ?? [];
        return { success: true, orders: this.orders };
      } catch (error) {
        const normalized = this.setError(error);
        this.orders = [];
        return { success: false, ...normalized };
      } finally {
        this.loading.orders = false;
      }
    },

    async fetchOrder(orderId) {
      this.loading.orderDetail = true;
      this.clearErrors();
      this.currentOrder = null;

      try {
        const { data } = await api.get(`/customer/orders/${orderId}`);
        this.currentOrder = data?.data ?? data ?? null;
        return { success: true, order: this.currentOrder };
      } catch (error) {
        const normalized = this.setError(error);
        this.currentOrder = null;
        return { success: false, ...normalized };
      } finally {
        this.loading.orderDetail = false;
      }
    },

    async fetchAddresses() {
      this.loading.addresses = true;
      this.clearErrors();
      try {
        const { data } = await api.get('/customer/addresses');
        this.addresses = data?.data ?? data ?? [];
        return { success: true, addresses: this.addresses };
      } catch (error) {
        const normalized = this.setError(error);
        this.addresses = [];
        return { success: false, ...normalized };
      } finally {
        this.loading.addresses = false;
      }
    },

    async addAddress(payload) {
      this.loading.addressMutation = true;
      this.clearErrors();
      try {
        const { data } = await api.post('/customer/addresses', payload);
        const created = data?.data ?? data;
        // Refresh para asegurar defaults correctos
        await this.fetchAddresses();
        return { success: true, address: created };
      } catch (error) {
        const normalized = this.setError(error);
        return { success: false, ...normalized };
      } finally {
        this.loading.addressMutation = false;
      }
    },

    async updateAddress(id, payload) {
      this.loading.addressMutation = true;
      this.clearErrors();
      try {
        const { data } = await api.put(`/customer/addresses/${id}`, payload);
        const updated = data?.data ?? data;
        await this.fetchAddresses();
        return { success: true, address: updated };
      } catch (error) {
        const normalized = this.setError(error);
        return { success: false, ...normalized };
      } finally {
        this.loading.addressMutation = false;
      }
    },

    async deleteAddress(id) {
      this.loading.addressMutation = true;
      this.clearErrors();
      try {
        await api.delete(`/customer/addresses/${id}`);
        await this.fetchAddresses();
        return { success: true };
      } catch (error) {
        const normalized = this.setError(error);
        return { success: false, ...normalized };
      } finally {
        this.loading.addressMutation = false;
      }
    },

    async setDefaultAddress({ address_id, type }) {
      this.loading.defaultAddress = true;
      this.clearErrors();
      try {
        const { data } = await api.put('/customer/default-address', { address_id, type });
        await this.fetchAddresses();
        return { success: true, message: data?.message || 'Dirección por defecto actualizada.' };
      } catch (error) {
        const normalized = this.setError(error);
        return { success: false, ...normalized };
      } finally {
        this.loading.defaultAddress = false;
      }
    },
  },
});

