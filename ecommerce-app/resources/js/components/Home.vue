<template>
  <div class="home-page bg-white">

    <!-- ===== PROMO BAR ===== -->
    <div v-if="showPromoBar" class="bg-indigo-600 text-white text-center text-xs sm:text-sm py-2 px-4 relative">
      <span>🚀 <strong>Envío gratis</strong> en compras desde $49 — <a href="/products" class="underline hover:no-underline font-semibold">Comprar ahora</a></span>
      <button @click="showPromoBar = false" class="absolute right-3 top-1/2 -translate-y-1/2 text-white/70 hover:text-white" aria-label="Cerrar">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <!-- ===== HEADER ===== -->
    <header
      class="sticky top-0 z-50 transition-all duration-300"
      :class="scrolled ? 'bg-white shadow-md' : 'bg-white/95 backdrop-blur-md'"
    >
      <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16 md:h-18 gap-4">

          <!-- Logo -->
          <a href="/" class="flex-shrink-0">
            <span class="text-xl sm:text-2xl font-extrabold tracking-tight" style="color: var(--color-primary);">
              {{ appName }}
            </span>
          </a>

          <!-- Desktop Search Bar -->
          <div class="hidden md:flex flex-1 max-w-xl mx-8">
            <div class="relative w-full">
              <input
                v-model="searchQuery"
                type="search"
                placeholder="¿Qué estás buscando?"
                class="w-full pl-11 pr-4 py-2.5 bg-gray-100 border-0 rounded-xl text-sm focus:ring-2 focus:ring-indigo-200 focus:bg-white transition-all duration-200"
                aria-label="Buscar productos"
                @keyup.enter="handleSearch"
              />
              <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>
          </div>

          <!-- Desktop Navigation -->
          <div class="hidden lg:block">
            <Navigation location="header" variant="header" />
          </div>

          <!-- Action Icons -->
          <div class="flex items-center gap-1">
            <!-- Mobile search toggle -->
            <button @click="mobileSearchOpen = !mobileSearchOpen" class="md:hidden p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors" aria-label="Buscar">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </button>

            <!-- Account - Desktop with dropdown when authenticated -->
            <div v-if="authStore.isAuthenticated" class="hidden sm:block relative">
              <button 
                @click="showUserMenu = !showUserMenu"
                class="flex items-center gap-2 p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                aria-label="Mi cuenta"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-sm font-medium hidden lg:inline">{{ authStore.user?.name?.split(' ')[0] || 'Usuario' }}</span>
                <svg class="w-4 h-4 hidden lg:inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>
              
              <!-- Dropdown Menu -->
              <transition
                enter-active-class="transition ease-out duration-100"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
              >
                <div 
                  v-if="showUserMenu"
                  v-click-outside="() => showUserMenu = false"
                  class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
                >
                  <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-900">{{ authStore.user?.name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ authStore.user?.email }}</p>
                  </div>
                  <a href="/customer" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Mi Cuenta
                  </a>
                  <button @click="handleLogout" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Cerrar Sesión
                  </button>
                </div>
              </transition>
            </div>

            <!-- Account - Simple link when not authenticated -->
            <a v-else :href="accountUrl" class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors" aria-label="Mi cuenta">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
            </a>

            <!-- Account - Mobile (always simple link) -->
            <a :href="accountUrl" class="sm:hidden p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors" aria-label="Mi cuenta">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
            </a>

            <!-- Cart -->
            <CartButton />

            <!-- Mobile Menu Toggle -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors" aria-label="Menú">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Search Bar -->
        <transition
          enter-active-class="transition ease-out duration-200"
          enter-from-class="opacity-0 -translate-y-2"
          enter-to-class="opacity-100 translate-y-0"
          leave-active-class="transition ease-in duration-150"
          leave-from-class="opacity-100 translate-y-0"
          leave-to-class="opacity-0 -translate-y-2"
        >
          <div v-if="mobileSearchOpen" class="md:hidden pb-3">
            <div class="relative">
              <input
                v-model="searchQuery"
                type="search"
                placeholder="¿Qué estás buscando?"
                class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border-0 rounded-xl text-sm focus:ring-2 focus:ring-indigo-200"
                aria-label="Buscar productos"
                @keyup.enter="handleSearch"
              />
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>
          </div>
        </transition>

        <!-- Mobile Navigation Drawer -->
        <transition
          enter-active-class="transition ease-out duration-200"
          enter-from-class="opacity-0"
          enter-to-class="opacity-100"
          leave-active-class="transition ease-in duration-150"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <div v-if="mobileMenuOpen" class="lg:hidden border-t border-gray-100 py-4 max-h-[70vh] overflow-y-auto">
            <Navigation location="header" variant="mobile" />
            <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
              <a :href="accountUrl" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Mi Cuenta
              </a>
            </div>
          </div>
        </transition>
      </div>
    </header>

    <!-- ===== MAIN CONTENT ===== -->

    <!-- Loading State -->
    <div v-if="loading" class="flex flex-col justify-center items-center min-h-[60vh] gap-4">
      <div class="w-12 h-12 border-3 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
      <p class="text-sm text-gray-400">Cargando contenido...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="container mx-auto px-4 py-16">
      <div class="max-w-md mx-auto text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
          <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
          </svg>
        </div>
        <h2 class="text-lg font-bold text-gray-900 mb-2">Error al cargar la página</h2>
        <p class="text-sm text-gray-500 mb-4">{{ error }}</p>
        <button @click="fetchConfiguration" class="btn-primary text-sm">Reintentar</button>
      </div>
    </div>

    <!-- Sections -->
    <main v-else>
      <component
        v-for="section in sections"
        :key="section.uuid"
        :is="getSectionComponent(section.type)"
        :section="section"
      />

      <HomeProductsSection @show-toast="showToast" />

      <!-- Static sections (always rendered after dynamic sections) -->
      <BenefitsSection />
      <NewsletterSection />
      <TrustBadges />
    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="bg-gray-900 text-white">
      <div class="container mx-auto px-4">
        <!-- Main Footer -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12 py-12 md:py-16">
          <!-- Brand -->
          <div>
            <a href="/" class="inline-block mb-4">
              <span class="text-2xl font-extrabold text-white">{{ appName }}</span>
            </a>
            <p class="text-gray-400 text-sm leading-relaxed mb-4">
              Tu tienda en línea de confianza. Descubre productos exclusivos con la mejor calidad y precio.
            </p>
            <!-- Social Links -->
            <div class="flex gap-3">
              <a href="#" class="w-9 h-9 rounded-lg bg-gray-800 hover:bg-indigo-600 flex items-center justify-center transition-colors duration-200" aria-label="Facebook">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
              </a>
              <a href="#" class="w-9 h-9 rounded-lg bg-gray-800 hover:bg-indigo-600 flex items-center justify-center transition-colors duration-200" aria-label="Instagram">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
              </a>
              <a href="#" class="w-9 h-9 rounded-lg bg-gray-800 hover:bg-indigo-600 flex items-center justify-center transition-colors duration-200" aria-label="Twitter">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
              </a>
            </div>
          </div>

          <!-- Quick Links -->
          <div>
            <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Tienda</h3>
            <Navigation location="footer" variant="footer" />
          </div>

          <!-- Help -->
          <div>
            <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Ayuda</h3>
            <ul class="space-y-3">
              <li><a href="#" class="text-gray-400 hover:text-white text-sm transition-colors duration-200">Centro de Ayuda</a></li>
              <li><a href="#" class="text-gray-400 hover:text-white text-sm transition-colors duration-200">Seguimiento de Pedido</a></li>
              <li><a href="#" class="text-gray-400 hover:text-white text-sm transition-colors duration-200">Política de Envío</a></li>
              <li><a href="#" class="text-gray-400 hover:text-white text-sm transition-colors duration-200">Devoluciones</a></li>
              <li><a href="#" class="text-gray-400 hover:text-white text-sm transition-colors duration-200">Contacto</a></li>
            </ul>
          </div>

          <!-- Contact & Hours -->
          <div>
            <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Contacto</h3>
            <ul class="space-y-3 text-sm text-gray-400">
              <li class="flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                info@tutienda.com
              </li>
              <li class="flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                (123) 456-7890
              </li>
              <li class="flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Lun - Vie: 9:00 - 18:00
              </li>
            </ul>
          </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-gray-800 py-6 flex flex-col md:flex-row items-center justify-between gap-4">
          <p class="text-xs text-gray-500">
            &copy; {{ currentYear }} {{ appName }}. Todos los derechos reservados.
          </p>
          <div class="flex gap-4 text-xs text-gray-500">
            <a href="#" class="hover:text-gray-300 transition-colors">Privacidad</a>
            <a href="#" class="hover:text-gray-300 transition-colors">Términos</a>
            <a href="#" class="hover:text-gray-300 transition-colors">Cookies</a>
          </div>
        </div>
      </div>
    </footer>

    <!-- Cart Drawer -->
    <CartDrawer />

    <!-- Toast Notifications -->
    <CartToast
      :show="toast.show"
      :type="toast.type"
      :title="toast.title"
      :message="toast.message"
      @close="toast.show = false"
    />

  </div>
</template>

<script>
import { onMounted, computed } from 'vue';
import HeroSection from './sections/HeroSection.vue';
import FeaturedProductsSection from './sections/FeaturedProductsSection.vue';
import FeaturedCategoriesSection from './sections/FeaturedCategoriesSection.vue';
import BannersSection from './sections/BannersSection.vue';
import TestimonialsSection from './sections/TestimonialsSection.vue';
import HtmlBlockSection from './sections/HtmlBlockSection.vue';
import BenefitsSection from './sections/BenefitsSection.vue';
import NewsletterSection from './sections/NewsletterSection.vue';
import TrustBadges from './sections/TrustBadges.vue';
import HomeProductsSection from './home/HomeProductsSection.vue';
import Navigation from '../Components/Navigation.vue';
import { useCartStore } from '../stores/cart';
import { useAuthStore } from '../stores/auth';

export default {
  name: 'Home',
  components: {
    HeroSection,
    FeaturedProductsSection,
    FeaturedCategoriesSection,
    BannersSection,
    TestimonialsSection,
    HtmlBlockSection,
    BenefitsSection,
    NewsletterSection,
    TrustBadges,
    HomeProductsSection,
    Navigation,
  },
  setup() {
    const cartStore = useCartStore();
    const authStore = useAuthStore();

    onMounted(() => {
      // Initialize cart on mount
      cartStore.fetchCart();
      // Check authentication status
      authStore.checkAuth();
    });

    // Computed property for user account URL
    const accountUrl = computed(() => {
      return authStore.isAuthenticated ? '/customer' : '/login';
    });

    return {
      cartStore,
      authStore,
      accountUrl,
    };
  },
  data() {
    return {
      sections: [],
      loading: true,
      error: null,
      appName: 'Mi Tienda',
      searchQuery: '',
      mobileMenuOpen: false,
      mobileSearchOpen: false,
      showPromoBar: true,
      scrolled: false,
      currentYear: new Date().getFullYear(),
      showUserMenu: false,
      toast: {
        show: false,
        type: 'info',
        title: '',
        message: '',
      },
    };
  },
  mounted() {
    this.fetchConfiguration();
    window.addEventListener('scroll', this.handleScroll, { passive: true });
  },
  beforeUnmount() {
    window.removeEventListener('scroll', this.handleScroll);
  },
  methods: {
    async fetchConfiguration() {
      try {
        this.loading = true;
        this.error = null;
        const response = await fetch('/api/home-configuration');
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const data = await response.json();
        this.sections = (data.data || data).sort((a, b) => a.display_order - b.display_order);
      } catch (err) {
        this.error = err.message;
        console.error('Error fetching home configuration:', err);
      } finally {
        this.loading = false;
      }
    },
    getSectionComponent(type) {
      const componentMap = {
        hero: 'HeroSection',
        featured_products: 'FeaturedProductsSection',
        featured_categories: 'FeaturedCategoriesSection',
        banners: 'BannersSection',
        testimonials: 'TestimonialsSection',
        html_block: 'HtmlBlockSection',
      };
      return componentMap[type] || null;
    },
    handleSearch() {
      if (this.searchQuery.trim()) {
        window.location.href = `/products?search=${encodeURIComponent(this.searchQuery.trim())}`;
      }
    },
    handleScroll() {
      this.scrolled = window.scrollY > 20;
    },
    showToast(type, message, title = '') {
      this.toast = {
        show: true,
        type,
        title,
        message,
      };
    },
    async handleLogout() {
      const result = await this.authStore.logout();
      if (result.success) {
        window.location.href = '/';
      } else {
        this.showToast('error', 'No se pudo cerrar sesión. Intenta de nuevo.', 'Error');
      }
    },
  },
  directives: {
    clickOutside: {
      mounted(el, binding) {
        el.clickOutsideEvent = (event) => {
          if (!(el === event.target || el.contains(event.target))) {
            binding.value();
          }
        };
        document.addEventListener('click', el.clickOutsideEvent);
      },
      unmounted(el) {
        document.removeEventListener('click', el.clickOutsideEvent);
      },
    },
  },
};
</script>

<style scoped>
.home-page {
  min-height: 100vh;
}
</style>
