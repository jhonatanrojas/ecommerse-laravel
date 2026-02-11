<template>
  <div>
    <!-- Applied Coupon -->
    <div v-if="cartStore.coupon" class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-lg mb-3">
      <div class="flex items-center gap-2">
        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
          <p class="text-sm font-semibold text-green-900">{{ cartStore.coupon.code }}</p>
          <p class="text-xs text-green-700">Cupón aplicado</p>
        </div>
      </div>
      <button
        @click="handleRemoveCoupon"
        :disabled="cartStore.applyingCoupon"
        class="p-1.5 text-green-600 hover:text-green-800 hover:bg-green-100 rounded transition-colors disabled:opacity-50"
        aria-label="Eliminar cupón"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <!-- Coupon Input -->
    <div v-else>
      <button
        v-if="!showInput"
        @click="showInput = true"
        class="text-sm text-indigo-600 hover:text-indigo-700 font-medium flex items-center gap-2"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
        </svg>
        ¿Tienes un cupón?
      </button>

      <div v-else class="space-y-2">
        <div class="flex gap-2">
          <input
            v-model="couponCode"
            type="text"
            placeholder="Código del cupón"
            class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500"
            @keyup.enter="handleApplyCoupon"
          />
          <button
            @click="handleApplyCoupon"
            :disabled="!couponCode.trim() || cartStore.applyingCoupon"
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <span v-if="!cartStore.applyingCoupon">Aplicar</span>
            <div v-else class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
          </button>
        </div>
        
        <!-- Success Message -->
        <Transition
          enter-active-class="transition-all duration-200"
          enter-from-class="opacity-0 -translate-y-1"
          enter-to-class="opacity-100 translate-y-0"
          leave-active-class="transition-all duration-150"
          leave-from-class="opacity-100 translate-y-0"
          leave-to-class="opacity-0 -translate-y-1"
        >
          <p v-if="successMessage" class="text-xs text-green-600 flex items-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ successMessage }}
          </p>
        </Transition>

        <!-- Error Message -->
        <Transition
          enter-active-class="transition-all duration-200"
          enter-from-class="opacity-0 -translate-y-1"
          enter-to-class="opacity-100 translate-y-0"
          leave-active-class="transition-all duration-150"
          leave-from-class="opacity-100 translate-y-0"
          leave-to-class="opacity-0 -translate-y-1"
        >
          <p v-if="errorMessage" class="text-xs text-red-600 flex items-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ errorMessage }}
          </p>
        </Transition>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useCartStore } from '../../stores/cart';

const cartStore = useCartStore();
const showInput = ref(false);
const couponCode = ref('');
const successMessage = ref('');
const errorMessage = ref('');

const handleApplyCoupon = async () => {
  if (!couponCode.value.trim()) return;

  errorMessage.value = '';
  successMessage.value = '';

  const result = await cartStore.applyCoupon(couponCode.value.trim());
  
  if (result.success) {
    successMessage.value = result.message;
    couponCode.value = '';
    setTimeout(() => {
      successMessage.value = '';
      showInput.value = false;
    }, 2000);
  } else {
    errorMessage.value = result.error;
    setTimeout(() => {
      errorMessage.value = '';
    }, 5000);
  }
};

const handleRemoveCoupon = async () => {
  await cartStore.removeCoupon();
};
</script>
