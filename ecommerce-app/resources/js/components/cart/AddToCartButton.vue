<template>
  <button
    @click="handleAddToCart"
    :disabled="isAdding || isOutOfStock"
    :class="buttonClass"
    class="relative overflow-hidden transition-all duration-200"
  >
    <!-- Loading State -->
    <span v-if="isAdding" class="flex items-center justify-center gap-2">
      <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
      Añadiendo...
    </span>

    <!-- Success State -->
    <span v-else-if="showSuccess" class="flex items-center justify-center gap-2">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
      </svg>
      ¡Añadido!
    </span>

    <!-- Out of Stock -->
    <span v-else-if="isOutOfStock" class="flex items-center justify-center gap-2">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
      Sin stock
    </span>

    <!-- Default State -->
    <span v-else class="flex items-center justify-center gap-2">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
      </svg>
      {{ buttonText }}
    </span>
  </button>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useCartStore } from '../../stores/cart';

const props = defineProps({
  productId: {
    type: [Number, String],
    required: true,
  },
  variantId: {
    type: [Number, String, null],
    default: null,
  },
  quantity: {
    type: Number,
    default: 1,
  },
  stock: {
    type: Number,
    default: null,
  },
  buttonText: {
    type: String,
    default: 'Añadir al carrito',
  },
  buttonClass: {
    type: String,
    default: 'btn-primary w-full',
  },
});

const emit = defineEmits(['added', 'error']);

const cartStore = useCartStore();
const isAdding = ref(false);
const showSuccess = ref(false);

const isOutOfStock = computed(() => {
  return props.stock !== null && props.stock <= 0;
});

const handleAddToCart = async () => {
  if (isAdding.value || isOutOfStock.value) return;

  isAdding.value = true;

  const result = await cartStore.addItem(
    props.productId,
    props.variantId,
    props.quantity
  );

  isAdding.value = false;

  if (result.success) {
    showSuccess.value = true;
    emit('added', { productId: props.productId, variantId: props.variantId });
    
    setTimeout(() => {
      showSuccess.value = false;
    }, 2000);
  } else {
    emit('error', result.error);
  }
};
</script>
