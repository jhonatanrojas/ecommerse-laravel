<template>
  <div class="customer-data-section bg-blue-50 border border-blue-200 rounded-lg p-4">
    <!-- Authenticated User -->
    <div v-if="isAuthenticated" class="flex items-start space-x-3">
      <div class="flex-shrink-0">
        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
          {{ userInitials }}
        </div>
      </div>
      <div class="flex-1">
        <h3 class="text-sm font-medium text-gray-900">
          Comprando como:
        </h3>
        <p class="text-base font-semibold text-gray-900 mt-1">
          {{ user.name }}
        </p>
        <p class="text-sm text-gray-600">
          {{ user.email }}
        </p>
      </div>
    </div>

    <!-- Guest User -->
    <div v-else class="space-y-3">
      <div class="flex items-center space-x-2">
        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3 class="text-sm font-medium text-gray-900">
          {{ guestCheckoutTitle }}
        </h3>
      </div>
      <p class="text-sm text-gray-600">
        {{ guestCheckoutMessage }}
        <a v-if="!allowGuestCheckout" href="/login" class="text-blue-600 hover:text-blue-700 font-medium">
          Inicia sesión
        </a>
        <span v-else>
          ¿Ya tienes una cuenta?
          <a href="/login" class="text-blue-600 hover:text-blue-700 font-medium">
            Inicia sesión
          </a>
          para una experiencia más rápida.
        </span>
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useAuthStore } from '../../stores/auth';
import { useCheckoutStore } from '../../stores/checkout';

const authStore = useAuthStore();
const checkoutStore = useCheckoutStore();

// User data
const isAuthenticated = computed(() => authStore.isAuthenticated);
const user = computed(() => authStore.user);

// Store configuration
const allowGuestCheckout = computed(() => checkoutStore.allowGuestCheckout);

// Guest checkout messages
const guestCheckoutTitle = computed(() => {
  return allowGuestCheckout.value 
    ? 'Checkout como invitado' 
    : 'Inicio de sesión requerido';
});

const guestCheckoutMessage = computed(() => {
  return allowGuestCheckout.value
    ? ''
    : 'Debes iniciar sesión para completar tu compra. ';
});

// User initials for avatar
const userInitials = computed(() => {
  if (!user.value?.name) return '?';
  const names = user.value.name.split(' ');
  if (names.length >= 2) {
    return `${names[0][0]}${names[1][0]}`.toUpperCase();
  }
  return user.value.name.substring(0, 2).toUpperCase();
});
</script>
