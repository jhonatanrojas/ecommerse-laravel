import { defineStore } from 'pinia';
import api from '../services/api';

function normalizeProduct(raw = {}) {
  return {
    id: raw.id,
    uuid: raw.uuid,
    name: raw.name || raw.nombre || '',
    slug: raw.slug || '',
    description: raw.description || raw.descripcion || '',
    short_description: raw.short_description || '',
    image: raw.image || raw.images?.[0]?.url || null,
    price: Number(raw.price || raw.precio || 0),
    compare_price: raw.compare_price ? Number(raw.compare_price) : null,
    stock: Number(raw.stock || 0),
    low_stock: Boolean(raw.low_stock),
    rating: Number(raw.rating || 0),
    reviews_count: Number(raw.reviews_count || 0),
    sales_count: Number(raw.sales_count || 0),
    views_count: Number(raw.views_count || 0),
    discount_percentage: Number(raw.discount_percentage || 0),
    free_shipping: Boolean(raw.free_shipping),
    estimated_delivery: raw.estimated_delivery || '',
    installments: raw.installments || null,
    seller: raw.seller || null,
    images: raw.images || [],
    category: raw.category || null,
    vendor: raw.vendor || null,
  };
}

export const useMarketplaceStore = defineStore('marketplace', {
  state: () => ({
    products: [],
    vendorProducts: [],
    currentVendor: null,
    currentProduct: null,
    questions: [],
    loading: false,
    loadingMore: false,
    questionSubmitting: false,
    reviewsSubmitting: false,
    orderProcessing: false,
    error: null,
    reviews: [],
    reviewSummary: {
      rating: 0,
      reviews_count: 0,
    },
    filters: {
      q: '',
      search: '',
      category_id: '',
      vendor_uuid: '',
      price_min: '',
      price_max: '',
      location: '',
      per_page: 24,
      page: 1,
    },
    pagination: {
      current_page: 1,
      last_page: 1,
      total: 0,
    },
    vendors: [],
  }),

  getters: {
    hasMoreProducts: (state) => state.pagination.current_page < state.pagination.last_page,
  },

  actions: {
    buildProductParams(extra = {}) {
      const payload = { ...this.filters, ...extra };
      const params = {
        page: payload.page || 1,
        per_page: payload.per_page || 24,
      };

      ['q', 'search', 'category_id', 'vendor_uuid', 'location'].forEach((key) => {
        if (payload[key]) params[key] = payload[key];
      });

      const priceMin = payload.price_min ?? payload.min_price;
      const priceMax = payload.price_max ?? payload.max_price;

      if (priceMin !== '' && priceMin !== null) params.price_min = priceMin;
      if (priceMax !== '' && priceMax !== null) params.price_max = priceMax;

      return params;
    },

    async fetchMarketplaceProducts(filters = {}, append = false) {
      if (append) {
        this.loadingMore = true;
      } else {
        this.loading = true;
        this.error = null;
      }

      try {
        this.filters = { ...this.filters, ...filters };

        const response = await api.get('/marketplace/products', {
          params: this.buildProductParams(this.filters),
        });

        const list = (response.data?.data || []).map(normalizeProduct);
        const meta = response.data?.meta || {};

        this.products = append ? [...this.products, ...list] : list;
        this.pagination = {
          current_page: Number(meta.current_page || 1),
          last_page: Number(meta.last_page || 1),
          total: Number(meta.total || this.products.length),
        };
      } catch (error) {
        this.error = error.response?.data?.message || 'No se pudo cargar el marketplace.';
      } finally {
        this.loading = false;
        this.loadingMore = false;
      }
    },

    async searchProducts(query, filters = {}) {
      this.loading = true;
      this.error = null;

      try {
        const params = this.buildProductParams({ ...filters, q: query, search: query, page: 1 });
        const response = await api.get('/marketplace/search', { params });

        this.filters = { ...this.filters, ...filters, q: query, search: query, page: 1 };
        this.products = (response.data?.data || []).map(normalizeProduct);

        const meta = response.data?.meta || {};
        this.pagination = {
          current_page: Number(meta.current_page || 1),
          last_page: Number(meta.last_page || 1),
          total: Number(meta.total || this.products.length),
        };
      } catch (error) {
        this.error = error.response?.data?.message || 'No se pudo realizar la búsqueda.';
      } finally {
        this.loading = false;
      }
    },

    async fetchVendors(params = {}) {
      const response = await api.get('/marketplace/vendors', { params });
      this.vendors = response.data?.data || [];
      return this.vendors;
    },

    async fetchVendor(slug) {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.get(`/marketplace/vendors/${slug}`);
        this.currentVendor = response.data?.data || null;
        return this.currentVendor;
      } catch (error) {
        this.error = error.response?.data?.message || 'No se pudo cargar el vendedor.';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async fetchVendorProducts(slug, params = {}) {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.get(`/marketplace/vendors/${slug}/products`, { params });
        this.vendorProducts = (response.data?.data || []).map(normalizeProduct);
        return this.vendorProducts;
      } catch (error) {
        this.error = error.response?.data?.message || 'No se pudo cargar productos del vendedor.';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async fetchProduct(slug) {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.get(`/marketplace/products/${slug}`);
        this.currentProduct = normalizeProduct(response.data?.data || {});
        return this.currentProduct;
      } catch (error) {
        this.error = error.response?.data?.message || 'No se pudo cargar el producto.';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async createOrder(productId, vendorId = null, buyerId = null) {
      this.orderProcessing = true;
      try {
        const response = await api.post('/orders/direct', {
          product_id: productId,
          vendor_id: vendorId,
          buyer_id: buyerId,
        });
        return response.data?.data || null;
      } finally {
        this.orderProcessing = false;
      }
    },

    async fetchQuestions(productId) {
      const response = await api.get(`/products/${productId}/questions`);
      this.questions = response.data?.data || [];
      return this.questions;
    },

    async submitQuestion(productId, text) {
      this.questionSubmitting = true;
      try {
        const response = await api.post(`/products/${productId}/questions`, { text });
        const created = response.data?.data;
        if (created) {
          this.questions = [created, ...this.questions];
        }
        return created;
      } finally {
        this.questionSubmitting = false;
      }
    },

    async fetchReviews(slug, params = {}) {
      const response = await api.get(`/marketplace/products/${slug}/reviews`, { params });
      this.reviews = response.data?.data || [];
      this.reviewSummary = response.data?.meta?.summary || {
        rating: 0,
        reviews_count: this.reviews.length,
      };
      return this.reviews;
    },

    async submitReview(slug, payload) {
      this.reviewsSubmitting = true;
      try {
        const response = await api.post(`/marketplace/products/${slug}/reviews`, payload);
        const created = response.data?.data;
        if (created) {
          this.reviews = [created, ...this.reviews];
          this.reviewSummary = {
            ...this.reviewSummary,
            reviews_count: Number(this.reviewSummary.reviews_count || 0) + 1,
          };
        }
        return created;
      } finally {
        this.reviewsSubmitting = false;
      }
    },
  },
});
