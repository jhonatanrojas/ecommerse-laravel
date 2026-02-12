<template>
  <div class="space-y-4">
    <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-md sm:p-6">
      <CheckoutSectionTitle
        title="Resumen del pedido"
        subtitle="Revisa tu compra antes de confirmar"
        eyebrow="Total"
      />

      <div v-if="loading" class="space-y-3">
        <div v-for="n in 3" :key="`summary-skeleton-${n}`" class="animate-pulse rounded-xl border border-gray-100 p-3">
          <div class="h-3 w-2/3 rounded bg-gray-100"></div>
          <div class="mt-2 h-3 w-1/3 rounded bg-gray-100"></div>
        </div>
      </div>

      <div v-else-if="isEmpty" class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">
        Tu carrito está vacío.
      </div>

      <div v-else class="space-y-5">
        <div class="max-h-[320px] space-y-3 overflow-y-auto pr-1">
          <article
            v-for="item in items"
            :key="item.uuid"
            class="flex items-center gap-3 rounded-xl border border-gray-100 p-3"
          >
            <div class="h-16 w-16 overflow-hidden rounded-lg bg-gray-100">
              <img
                v-if="item.product.image"
                :src="item.product.image"
                :alt="item.product.name"
                class="h-full w-full object-cover"
              />
              <div v-else class="flex h-full w-full items-center justify-center text-xs text-gray-400">Sin imagen</div>
            </div>

            <div class="min-w-0 flex-1">
              <p class="truncate text-sm font-semibold text-gray-900">{{ item.product.name }}</p>
              <p class="mt-0.5 text-xs text-gray-500">Cantidad: {{ item.quantity }}</p>
              <p class="text-xs text-gray-500">{{ formatCurrency(item.price) }} c/u</p>
            </div>

            <p class="text-sm font-semibold text-gray-900">{{ formatCurrency(item.subtotal) }}</p>
          </article>
        </div>

        <div class="space-y-2 border-t border-gray-100 pt-4 text-sm">
          <div class="flex items-center justify-between text-gray-600">
            <span>Subtotal</span>
            <span class="text-gray-900">{{ formatCurrency(subtotal) }}</span>
          </div>
          <div v-if="discount > 0" class="flex items-center justify-between text-gray-600">
            <span>Descuento</span>
            <span class="font-medium text-green-600">-{{ formatCurrency(discount) }}</span>
          </div>
          <div class="flex items-center justify-between text-gray-600">
            <span>Envío</span>
            <span class="text-gray-900">{{ shippingCost > 0 ? formatCurrency(shippingCost) : 'Por calcular' }}</span>
          </div>
          <div class="flex items-center justify-between border-t border-gray-100 pt-3 text-base font-semibold text-gray-900">
            <span>Total</span>
            <span>{{ formatCurrency(totalAmount) }}</span>
          </div>
        </div>

        <div v-if="coupon" class="rounded-xl border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-700">
          Cupón aplicado: <span class="font-semibold">{{ coupon.code }}</span>
        </div>
      </div>
    </section>

    <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-md sm:p-6">
      <CheckoutButton
        :loading="submitting"
        :disabled="isDisabled"
        loading-text="Procesando pedido..."
        @click="handleSubmit"
      >
        Confirmar y pagar
      </CheckoutButton>

      <div v-if="hasValidationErrors" class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
        <p class="font-semibold text-red-800">Revisa estos campos antes de continuar:</p>
        <ul class="mt-2 space-y-1">
          <li v-for="(errors, field) in validationErrors" :key="field">
            <span class="font-medium">{{ getFieldLabel(field) }}:</span>
            {{ errors[0] }}
          </li>
        </ul>
      </div>

      <div v-if="generalError" class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
        {{ generalError }}
      </div>

      <p class="mt-4 text-center text-xs text-gray-500">Pago seguro y cifrado</p>
    </section>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useCheckoutStore } from '../../stores/checkout';
import CheckoutButton from './CheckoutButton.vue';
import CheckoutSectionTitle from './CheckoutSectionTitle.vue';

const emit = defineEmits(['submit']);

const router = useRouter();
const checkoutStore = useCheckoutStore();

const loading = computed(() => checkoutStore.loading);
const submitting = computed(() => checkoutStore.submitting);
const isEmpty = computed(() => checkoutStore.isEmpty);
const items = computed(() => checkoutStore.items);
const subtotal = computed(() => checkoutStore.subtotal);
const discount = computed(() => checkoutStore.discount);
const shippingCost = computed(() => checkoutStore.shippingCost);
const totalAmount = computed(() => checkoutStore.totalAmount);
const coupon = computed(() => {
  return checkoutStore.cart?.coupon_code ? { code: checkoutStore.cart.coupon_code } : null;
});

const validationErrors = computed(() => {
  const errors = { ...checkoutStore.errors };
  delete errors.general;
  return errors;
});

const generalError = computed(() => checkoutStore.errors.general?.[0] || '');
const hasValidationErrors = computed(() => Object.keys(validationErrors.value).length > 0);

const isDisabled = computed(() => submitting.value || isEmpty.value);

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('es-ES', {
    style: 'currency',
    currency: 'EUR',
  }).format(amount);
};

const getFieldLabel = (field) => {
  const labels = {
    shipping_address: 'Dirección de envío',
    billing_address: 'Dirección de facturación',
    shipping_method: 'Método de envío',
    payment_method: 'Método de pago',
  };

  return labels[field] || field;
};

const handleSubmit = async () => {
  if (isDisabled.value) return;

  checkoutStore.clearErrors();
  const result = await checkoutStore.submitCheckout();

  if (result.success) {
    emit('submit', result.order);
    router.push({
      name: 'order-success',
      params: { orderId: result.order.id },
    });
  }
};
</script>
