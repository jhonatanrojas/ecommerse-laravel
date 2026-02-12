import { defineStore } from 'pinia';
import api from '../services/api';

function pick(value, fallback = null) {
  if (Array.isArray(value)) {
    const first = value.find((item) => item !== null && item !== undefined);
    return first ?? fallback;
  }
  return value ?? fallback;
}

function normalizeProduct(raw = {}) {
  const category = raw.category ?? raw.categoria ?? null;
  const images = raw.images ?? raw.imagenes ?? [];
  const variants = raw.variants ?? raw.variantes ?? [];

  return {
    id: raw.id,
    name: raw.name ?? raw.nombre ?? '',
    slug: raw.slug ?? '',
    description: raw.description ?? raw.descripcion ?? '',
    price: Number(raw.price ?? raw.precio ?? 0),
    salePrice: Number(raw.precio_oferta ?? raw.sale_price ?? 0) || null,
    stock: Number(raw.stock ?? 0),
    status: raw.status ?? raw.estado ?? 'active',
    category: category
      ? {
          id: category.id,
          name: category.name ?? category.nombre ?? '',
          slug: category.slug ?? '',
        }
      : null,
    images: images.map((image) => ({
      id: image.id,
      url: image.url,
      thumbnailUrl: image.thumbnail_url ?? null,
      altText: image.alt_text ?? image.alt ?? null,
      isPrimary: Boolean(image.is_primary),
      order: image.order ?? image.orden ?? 0,
    })),
    variants: variants.map((variant) => ({
      id: variant.id,
      name: variant.name ?? variant.nombre ?? '',
      sku: variant.sku ?? null,
      price: Number(variant.price ?? variant.precio ?? 0),
      stock: Number(variant.stock ?? 0),
      attributes: variant.attributes ?? variant.atributos ?? {},
    })),
  };
}

export const useProductsStore = defineStore('products', {
  state: () => ({
    products: [],
    categories: [],
    meta: {
      current_page: 1,
      last_page: 1,
      per_page: 12,
      total: 0,
    },
    links: null,
    loading: false,
    error: null,
    filters: {
      search: '',
      category_id: '',
      min_price: '',
      max_price: '',
      sort: 'newest',
      page: 1,
      per_page: 12,
    },
  }),

  getters: {
    hasProducts: (state) => state.products.length > 0,
  },

  actions: {
    updateFilters(partial = {}) {
      this.filters = {
        ...this.filters,
        ...partial,
        page: partial.page ?? 1,
      };
    },

    resetFilters() {
      this.filters = {
        search: '',
        category_id: '',
        min_price: '',
        max_price: '',
        sort: 'newest',
        page: 1,
        per_page: this.filters.per_page || 12,
      };
    },

    async fetchProducts(overrides = {}) {
      this.loading = true;
      this.error = null;

      const activeFilters = {
        ...this.filters,
        ...overrides,
      };

      const params = {
        sort: activeFilters.sort || 'newest',
        page: activeFilters.page || 1,
        per_page: activeFilters.per_page || 12,
      };

      if (activeFilters.search?.trim()) params.search = activeFilters.search.trim();
      if (activeFilters.category_id) params.category_id = activeFilters.category_id;
      if (activeFilters.min_price !== '' && activeFilters.min_price !== null) params.min_price = activeFilters.min_price;
      if (activeFilters.max_price !== '' && activeFilters.max_price !== null) params.max_price = activeFilters.max_price;

      try {
        const response = await api.get('/products', { params });
        const payload = response.data || {};
        const rawProducts = payload.data || [];
        const rawMeta = payload.meta || {};

        this.products = rawProducts.map(normalizeProduct);
        this.meta = {
          current_page: Number(pick(rawMeta.current_page, 1)),
          last_page: Number(pick(rawMeta.last_page, 1)),
          per_page: Number(pick(rawMeta.per_page, 12)),
          total: Number(pick(rawMeta.total, rawProducts.length)),
        };
        this.links = payload.links ?? null;

        this.filters = {
          ...this.filters,
          ...activeFilters,
          page: this.meta.current_page,
        };

        this.mergeCategoriesFromProducts(this.products);
      } catch (error) {
        this.error = error.response?.data?.message || 'No se pudieron cargar los productos.';
      } finally {
        this.loading = false;
      }
    },

    async fetchCategories() {
      try {
        const response = await api.get('/products', {
          params: {
            per_page: 60,
            page: 1,
            sort: 'newest',
          },
        });

        const rawProducts = response.data?.data || [];
        const normalized = rawProducts.map(normalizeProduct);
        this.mergeCategoriesFromProducts(normalized);
      } catch {
        // Silent fail: categories can still be inferred from current products page.
      }
    },

    mergeCategoriesFromProducts(products = []) {
      const map = new Map(this.categories.map((category) => [category.id, category]));

      products.forEach((product) => {
        if (product.category?.id) {
          map.set(product.category.id, product.category);
        }
      });

      this.categories = Array.from(map.values()).sort((a, b) => a.name.localeCompare(b.name));
    },
  },
});
