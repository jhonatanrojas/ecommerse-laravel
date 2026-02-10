<template>
  <!-- Header variant -->
  <nav v-if="variant === 'header' && menu && menu.items" class="flex items-center gap-1">
    <template v-for="item in menu.items" :key="item.id">
      <!-- With Children — Dropdown -->
      <div
        v-if="item.children && item.children.length > 0"
        class="relative"
        @mouseenter="openDropdown(item.id)"
        @mouseleave="closeDropdown(item.id)"
      >
        <button
          class="nav-link flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-indigo-600 transition-colors duration-200"
          :class="{ 'text-indigo-600': activeDropdown === item.id }"
          @click="toggleDropdown(item.id)"
          :aria-expanded="activeDropdown === item.id"
          aria-haspopup="true"
        >
          <span>{{ item.label }}</span>
          <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': activeDropdown === item.id }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>

        <!-- Dropdown Menu -->
        <transition
          enter-active-class="transition ease-out duration-150"
          enter-from-class="opacity-0 translate-y-1"
          enter-to-class="opacity-100 translate-y-0"
          leave-active-class="transition ease-in duration-100"
          leave-from-class="opacity-100 translate-y-0"
          leave-to-class="opacity-0 translate-y-1"
        >
          <div
            v-show="activeDropdown === item.id"
            class="absolute top-full left-0 mt-1 w-56 bg-white rounded-xl shadow-lg ring-1 ring-black/5 py-2 z-50"
          >
            <a
              v-for="child in item.children"
              :key="child.id"
              :href="child.url || '#'"
              :target="child.target"
              class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-150"
            >
              <span>{{ child.label }}</span>
              <span
                v-if="child.badge_text"
                class="ml-auto text-xs font-bold px-1.5 py-0.5 rounded-full"
                :style="{ backgroundColor: child.badge_color || '#EF4444', color: 'white' }"
              >{{ child.badge_text }}</span>
            </a>
          </div>
        </transition>
      </div>

      <!-- Regular Link -->
      <a
        v-else
        :href="item.url || '#'"
        :target="item.target"
        class="nav-link px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-indigo-600 transition-colors duration-200 flex items-center gap-1.5"
      >
        <span>{{ item.label }}</span>
        <span
          v-if="item.badge_text"
          class="text-xs font-bold px-1.5 py-0.5 rounded-full"
          :style="{ backgroundColor: item.badge_color || '#EF4444', color: 'white' }"
        >{{ item.badge_text }}</span>
      </a>
    </template>
  </nav>

  <!-- Footer variant -->
  <nav v-else-if="variant === 'footer' && menu && menu.items" class="space-y-3">
    <template v-for="item in menu.items" :key="item.id">
      <div>
        <a
          :href="item.url || '#'"
          :target="item.target"
          class="text-gray-400 hover:text-white text-sm transition-colors duration-200 block"
        >{{ item.label }}</a>
        <!-- Sub items -->
        <div v-if="item.children && item.children.length > 0" class="mt-2 ml-3 space-y-2">
          <a
            v-for="child in item.children"
            :key="child.id"
            :href="child.url || '#'"
            :target="child.target"
            class="text-gray-500 hover:text-gray-300 text-sm transition-colors duration-200 block"
          >{{ child.label }}</a>
        </div>
      </div>
    </template>
  </nav>

  <!-- Mobile variant -->
  <nav v-else-if="variant === 'mobile' && menu && menu.items" class="space-y-1">
    <template v-for="item in menu.items" :key="item.id">
      <!-- With children -->
      <div v-if="item.children && item.children.length > 0">
        <button
          @click="toggleDropdown(item.id)"
          class="w-full flex items-center justify-between px-4 py-3 text-base font-medium text-gray-800 hover:bg-gray-50 rounded-lg transition-colors"
        >
          <span>{{ item.label }}</span>
          <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': activeDropdown === item.id }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <div v-show="activeDropdown === item.id" class="ml-4 mt-1 space-y-1 border-l-2 border-indigo-100 pl-4">
          <a
            v-for="child in item.children"
            :key="child.id"
            :href="child.url || '#'"
            :target="child.target"
            class="block px-3 py-2 text-sm text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
          >{{ child.label }}</a>
        </div>
      </div>

      <!-- Regular link -->
      <a
        v-else
        :href="item.url || '#'"
        :target="item.target"
        class="block px-4 py-3 text-base font-medium text-gray-800 hover:bg-gray-50 rounded-lg transition-colors"
      >
        {{ item.label }}
        <span
          v-if="item.badge_text"
          class="ml-2 text-xs font-bold px-1.5 py-0.5 rounded-full"
          :style="{ backgroundColor: item.badge_color || '#EF4444', color: 'white' }"
        >{{ item.badge_text }}</span>
      </a>
    </template>
  </nav>

  <!-- Loading skeleton -->
  <div v-else-if="loading" class="flex gap-4">
    <div class="skeleton h-4 w-16 rounded"></div>
    <div class="skeleton h-4 w-20 rounded"></div>
    <div class="skeleton h-4 w-14 rounded"></div>
  </div>
</template>

<script>
export default {
  name: 'Navigation',
  props: {
    location: {
      type: String,
      required: true,
    },
    variant: {
      type: String,
      default: 'header',
      validator: (v) => ['header', 'footer', 'mobile'].includes(v),
    },
  },
  data() {
    return {
      menu: null,
      loading: true,
      activeDropdown: null,
      closeTimeout: null,
    };
  },
  mounted() {
    this.fetchMenu();
  },
  methods: {
    async fetchMenu() {
      try {
        const response = await fetch(`/api/menus/location/${this.location}`);
        if (response.ok) {
          this.menu = await response.json();
        }
      } catch (error) {
        console.error('Error fetching menu:', error);
      } finally {
        this.loading = false;
      }
    },
    toggleDropdown(itemId) {
      this.activeDropdown = this.activeDropdown === itemId ? null : itemId;
    },
    openDropdown(itemId) {
      if (this.closeTimeout) {
        clearTimeout(this.closeTimeout);
        this.closeTimeout = null;
      }
      this.activeDropdown = itemId;
    },
    closeDropdown(itemId) {
      this.closeTimeout = setTimeout(() => {
        if (this.activeDropdown === itemId) {
          this.activeDropdown = null;
        }
      }, 150);
    },
  },
};
</script>
