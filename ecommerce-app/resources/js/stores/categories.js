import { defineStore } from 'pinia';
import api from '../services/api';

function pick(value, fallback = null) {
  if (Array.isArray(value)) {
    const first = value.find((item) => item !== null && item !== undefined);
    return first ?? fallback;
  }
  return value ?? fallback;
}

function normalizeCategory(raw = {}) {
  return {
    id: raw.id,
    name: raw.name ?? raw.nombre ?? '',
    slug: raw.slug ?? '',
    image: raw.image ?? raw.imagen ?? null,
    description: raw.description ?? raw.descripcion ?? '',
    productCount: Number(raw.product_count ?? raw.cantidad_productos ?? raw.products_count ?? 0),
  };
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

export const useCategoriesStore = defineStore('categories', {
  state: () => ({
    categories: [],
    products: [],
    currentCategory: null,
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 12,
      total: 0,
    },
    links: null,
    loadingCategories: false,
    loadingProducts: false,
    errors: {
      categories: null,
      products: null,
    },
    productFilters: {
      min_price: '',
      max_price: '',
      sort: 'newest',
      page: 1,
      per_page: 12,
    },
  }),

  actions: {
    async fetchCategories() {
      this.loadingCategories = true;
      this.errors.categories = null;

      try {
        const response = await api.get('/categories');
        const payload = response.data || {};
        const rawCategories = payload.data || payload;

        this.categories = Array.isArray(rawCategories)
          ? rawCategories.map(normalizeCategory)
          : [];
      } catch (error) {
        this.errors.categories = error.response?.data?.message || 'No se pudieron cargar las categorías.';
      } finally {
        this.loadingCategories = false;
      }
    },

    updateProductFilters(partial = {}) {
      this.productFilters = {
        ...this.productFilters,
        ...partial,
        page: partial.page ?? 1,
      };
    },

    resetProductFilters() {
      this.productFilters = {
        min_price: '',
        max_price: '',
        sort: 'newest',
        page: 1,
        per_page: this.productFilters.per_page || 12,
      };
    },

    async fetchCategoryProducts(slug, filters = {}) {
      this.loadingProducts = true;
      this.errors.products = null;

      const activeFilters = {
        ...this.productFilters,
        ...filters,
      };

      const params = {
        sort: activeFilters.sort || 'newest',
        page: activeFilters.page || 1,
        per_page: activeFilters.per_page || 12,
      };

      if (activeFilters.min_price !== '' && activeFilters.min_price !== null) {
        params.min_price = activeFilters.min_price;
      }

      if (activeFilters.max_price !== '' && activeFilters.max_price !== null) {
        params.max_price = activeFilters.max_price;
      }

      try {
        const response = await api.get(`/categories/${slug}/products`, { params });
        const payload = response.data || {};

        const rawProducts = payload.data || [];
        const rawMeta = payload.meta || payload.pagination || {};
        const rawCategory = payload.category || payload.categoria || null;

        this.products = Array.isArray(rawProducts)
          ? rawProducts.map(normalizeProduct)
          : [];

        this.pagination = {
          current_page: Number(pick(rawMeta.current_page, payload.current_page ?? 1)),
          last_page: Number(pick(rawMeta.last_page, payload.last_page ?? 1)),
          per_page: Number(pick(rawMeta.per_page, payload.per_page ?? 12)),
          total: Number(pick(rawMeta.total, payload.total ?? this.products.length)),
        };

        this.links = payload.links ?? null;
        this.productFilters = {
          ...this.productFilters,
          ...activeFilters,
          page: this.pagination.current_page,
        };

        if (rawCategory) {
          this.currentCategory = normalizeCategory(rawCategory);
        } else {
          await this.ensureCategoryFromList(slug);
        }
      } catch (error) {
        this.errors.products = error.response?.data?.message || 'No se pudieron cargar los productos de la categoría.';
        this.products = [];
      } finally {
        this.loadingProducts = false;
      }
    },

    async ensureCategoryFromList(slug) {
      if (!this.categories.length) {
        await this.fetchCategories();
      }

      this.currentCategory = this.categories.find((category) => category.slug === slug) || null;
    },
  },
});
