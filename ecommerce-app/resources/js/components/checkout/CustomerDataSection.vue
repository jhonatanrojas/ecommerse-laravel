<template>
  <div class="rounded-2xl border border-indigo-100 bg-indigo-50/70 p-4 sm:p-5">
    <div v-if="isAuthenticated" class="flex items-start gap-3">
      <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-sm font-semibold text-white">
        {{ userInitials }}
      </div>

      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Sesión activa</p>
        <p class="mt-1 text-base font-semibold text-gray-900">{{ user.name }}</p>
        <p class="text-sm text-gray-600">{{ user.email }}</p>
      </div>
    </div>

    <div v-else class="space-y-2">
      <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Invitado</p>
      <h3 class="text-sm font-semibold text-gray-900">{{ guestCheckoutTitle }}</h3>

      <p class="text-sm text-gray-600">
        {{ guestCheckoutMessage }}
        <a v-if="!allowGuestCheckout" href="/login" class="font-semibold text-indigo-600 hover:text-indigo-700">
          Inicia sesión
        </a>
        <span v-else>
          ¿Ya tienes una cuenta?
          <a href="/login" class="font-semibold text-indigo-600 hover:text-indigo-700">Inicia sesión</a>
          para completar más rápido.
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

const isAuthenticated = computed(() => authStore.isAuthenticated);
const user = computed(() => authStore.user);
const allowGuestCheckout = computed(() => checkoutStore.allowGuestCheckout);

const guestCheckoutTitle = computed(() => {
  return allowGuestCheckout.value ? 'Checkout como invitado' : 'Inicio de sesión requerido';
});

const guestCheckoutMessage = computed(() => {
  return allowGuestCheckout.value ? '' : 'Debes iniciar sesión para completar tu compra. ';
});

const userInitials = computed(() => {
  if (!user.value?.name) return '?';

  const names = user.value.name.split(' ');
  if (names.length >= 2) {
    return `${names[0][0]}${names[1][0]}`.toUpperCase();
  }

  return user.value.name.substring(0, 2).toUpperCase();
});
</script>
