<template>
  <div class="flex gap-4 p-4 bg-white rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
    <!-- Product Image -->
    <div class="flex-shrink-0 w-20 h-20 bg-gray-100 rounded-lg overflow-hidden">
      <img
        v-if="item.product?.image"
        :src="item.product.image"
        :alt="item.product?.name"
        class="w-full h-full object-cover"
      />
      <div v-else class="w-full h-full flex items-center justify-center">
        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
      </div>
    </div>

    <!-- Product Info -->
    <div class="flex-1 min-w-0">
      <h3 class="text-sm font-semibold text-gray-900 mb-1 truncate">
        {{ item.product?.name }}
      </h3>
      
      <!-- Variant Info -->
      <p v-if="item.variant" class="text-xs text-gray-500 mb-2">
        {{ item.variant.name }}
      </p>

      <!-- Price -->
      <p class="text-sm font-bold text-indigo-600 mb-3">
        ${{ formatPrice(item.price) }}
      </p>

      <!-- Quantity Controls -->
      <div class="flex items-center gap-2">
        <div class="flex items-center border border-gray-300 rounded-lg">
          <button
            @click="decreaseQuantity"
            :disabled="isUpdating || item.quantity <= 1"
            class="px-3 py-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            aria-label="Disminuir cantidad"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
            </svg>
          </button>
          
          <span class="px-4 py-1.5 text-sm font-medium text-gray-900 min-w-[3rem] text-center">
            {{ isUpdating ? '...' : item.quantity }}
          </span>
          
          <button
            @click="increaseQuantity"
            :disabled="isUpdating || (availableStock !== null && item.quantity >= availableStock)"
            class="px-3 py-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            aria-label="Aumentar cantidad"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
          </button>
        </div>

        <!-- Remove Button -->
        <button
          @click="handleRemove"
          :disabled="isRemoving"
          class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors disabled:opacity-50"
          aria-label="Eliminar producto"
        >
          <svg v-if="!isRemoving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
          <div v-else class="w-4 h-4 border-2 border-red-200 border-t-red-500 rounded-full animate-spin"></div>
        </button>
      </div>

      <!-- Stock Warning -->
      <p v-if="stockWarning" class="text-xs text-amber-600 mt-2 flex items-center gap-1">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3l-6.928-12c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        {{ stockWarning }}
      </p>
    </div>

    <!-- Subtotal -->
    <div class="flex-shrink-0 text-right">
      <p class="text-sm font-bold text-gray-900">
        ${{ formatPrice(item.subtotal) }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useCartStore } from '../../stores/cart';

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
});

const cartStore = useCartStore();

const isUpdating = computed(() => cartStore.updatingItem === props.item.uuid);
const isRemoving = computed(() => cartStore.removingItem === props.item.uuid);

const availableStock = computed(() => {
  // Check variant stock first
  if (props.item.variant && props.item.variant.stock !== null && props.item.variant.stock !== undefined) {
    return props.item.variant.stock;
  }
  // Then check product stock
  if (props.item.product && props.item.product.stock !== null && props.item.product.stock !== undefined) {
    return props.item.product.stock;
  }
  // No stock limit
  return null;
});

const stockWarning = computed(() => {
  if (availableStock.value !== null && availableStock.value < 5) {
    return `Solo quedan ${availableStock.value} unidades`;
  }
  return null;
});

const formatPrice = (price) => {
  return parseFloat(price).toFixed(2);
};

const increaseQuantity = async () => {
  await cartStore.updateItem(props.item.uuid, props.item.quantity + 1);
};

const decreaseQuantity = async () => {
  if (props.item.quantity > 1) {
    await cartStore.updateItem(props.item.uuid, props.item.quantity - 1);
  }
};

const handleRemove = async () => {
  await cartStore.removeItem(props.item.uuid);
};
</script>
