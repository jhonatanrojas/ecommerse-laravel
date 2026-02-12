<template>
  <CustomerLayout
    title="Detalle de orden"
    subtitle="Consulta todos los datos de tu compra y el resumen final."
    eyebrow="Mi cuenta"
  >
    <template #header-actions>
      <router-link
        to="/customer/orders"
        class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
      >
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Volver a mis órdenes
      </router-link>
    </template>

    <div v-if="customerStore.loading.orderDetail" class="space-y-4">
      <div v-for="n in 3" :key="`detail-skeleton-${n}`" class="animate-pulse rounded-2xl border border-gray-100 bg-white p-6 shadow-md">
        <div class="h-4 w-48 rounded bg-gray-100"></div>
        <div class="mt-3 h-3 w-64 rounded bg-gray-100"></div>
        <div class="mt-6 grid grid-cols-1 gap-3 md:grid-cols-2">
          <div class="h-10 rounded bg-gray-100"></div>
          <div class="h-10 rounded bg-gray-100"></div>
        </div>
      </div>
    </div>

    <div v-else-if="errorMessage" class="rounded-2xl border border-red-200 bg-red-50 p-6 text-red-700 shadow-md">
      <p class="text-lg font-semibold text-red-800">No se pudo cargar la orden</p>
      <p class="mt-2 text-sm">{{ errorMessage }}</p>
      <div class="mt-4 flex flex-wrap gap-2">
        <button
          type="button"
          class="rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-100"
          @click="loadOrder"
        >
          Reintentar
        </button>
        <router-link
          to="/customer/orders"
          class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
        >
          Volver a mis órdenes
        </router-link>
      </div>
    </div>

    <div v-else-if="!order" class="rounded-2xl border border-gray-200 bg-white p-8 text-center shadow-md">
      <p class="text-lg font-semibold text-gray-900">Esta orden no existe.</p>
      <p class="mt-2 text-sm text-gray-500">Verifica el enlace o vuelve al listado de órdenes.</p>
      <router-link to="/customer/orders" class="btn-primary mt-5 inline-flex px-5 py-2.5 text-sm">
        Volver a mis órdenes
      </router-link>
    </div>

    <div v-else class="grid grid-cols-1 gap-6 xl:grid-cols-12">
      <div class="space-y-6 xl:col-span-8">
        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-md sm:p-6">
          <div class="flex flex-wrap items-start justify-between gap-4 border-b border-gray-100 pb-4">
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600">Orden</p>
              <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-gray-900">#{{ order.order_number }}</h2>
              <p class="mt-1 text-sm text-gray-500">Creada el {{ formatDateTime(order.created_at) }}</p>
            </div>
            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold" :class="statusClass(order.status)">
              {{ statusLabel(order.status) }}
            </span>
          </div>

          <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
              <p class="text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">Método de pago</p>
              <p class="mt-2 text-sm font-semibold text-gray-900">{{ order.payment_method || 'No especificado' }}</p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
              <p class="text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">Estado de pago</p>
              <p class="mt-2 text-sm font-semibold text-gray-900">{{ paymentStatusLabel(order.payment_status) }}</p>
            </div>
          </div>
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-md sm:p-6">
          <h3 class="text-lg font-bold text-gray-900">Dirección de envío</h3>
          <div v-if="order.shipping_address || order.shippingAddress" class="mt-4 rounded-xl border border-gray-100 bg-gray-50 p-4 text-sm text-gray-700">
            <p class="font-semibold text-gray-900">{{ fullName(shippingAddress) }}</p>
            <p class="mt-1">{{ shippingAddress.address_line1 }}</p>
            <p v-if="shippingAddress.address_line2">{{ shippingAddress.address_line2 }}</p>
            <p>{{ shippingAddress.city }}, {{ shippingAddress.state }} {{ shippingAddress.postal_code }}</p>
            <p>{{ shippingAddress.country }}</p>
            <p v-if="shippingAddress.phone" class="mt-1">Tel: {{ shippingAddress.phone }}</p>
          </div>
          <p v-else class="mt-3 text-sm text-gray-500">No hay dirección de envío registrada.</p>
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-md sm:p-6">
          <h3 class="text-lg font-bold text-gray-900">Productos</h3>

          <div v-if="!order.items?.length" class="mt-4 rounded-xl border border-dashed border-gray-200 bg-gray-50 p-4 text-sm text-gray-500">
            Esta orden no tiene productos asociados.
          </div>

          <div v-else class="mt-4 space-y-3">
            <article
              v-for="item in order.items"
              :key="item.uuid"
              class="flex flex-col gap-3 rounded-xl border border-gray-100 p-3 sm:flex-row sm:items-center"
            >
              <div class="h-20 w-20 overflow-hidden rounded-lg bg-gray-100">
                <img
                  v-if="item.product?.image_url"
                  :src="item.product.image_url"
                  :alt="item.product_name"
                  class="h-full w-full object-cover"
                />
                <div v-else class="flex h-full w-full items-center justify-center text-xs text-gray-400">Sin imagen</div>
              </div>

              <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-gray-900">{{ item.product_name }}</p>
                <p class="mt-1 text-xs text-gray-500">Cantidad: {{ item.quantity }}</p>
                <p class="text-xs text-gray-500">Precio unitario: {{ formatCurrency(item.price) }}</p>
              </div>

              <p class="text-sm font-semibold text-gray-900">{{ formatCurrency(item.subtotal) }}</p>
            </article>
          </div>
        </section>
      </div>

      <aside class="xl:col-span-4">
        <div class="space-y-4 xl:sticky xl:top-24">
          <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-md sm:p-6">
            <h3 class="text-lg font-bold text-gray-900">Resumen</h3>
            <div class="mt-4 space-y-3 text-sm">
              <div class="flex items-center justify-between text-gray-600">
                <span>Subtotal</span>
                <span class="text-gray-900">{{ formatCurrency(order.subtotal) }}</span>
              </div>
              <div class="flex items-center justify-between text-gray-600">
                <span>Envío</span>
                <span class="text-gray-900">{{ formatCurrency(order.shipping_cost || order.shipping_amount) }}</span>
              </div>
              <div class="flex items-center justify-between text-gray-600">
                <span>Impuestos</span>
                <span class="text-gray-900">{{ formatCurrency(order.tax || order.tax_amount) }}</span>
              </div>
              <div v-if="Number(order.discount || 0) > 0" class="flex items-center justify-between text-gray-600">
                <span>Descuento</span>
                <span class="font-semibold text-emerald-600">-{{ formatCurrency(order.discount) }}</span>
              </div>
              <div class="border-t border-gray-100 pt-3">
                <div class="flex items-center justify-between text-base font-bold text-gray-900">
                  <span>Total</span>
                  <span>{{ formatCurrency(order.total) }}</span>
                </div>
              </div>
            </div>
          </section>
        </div>
      </aside>
    </div>
  </CustomerLayout>
</template>

<script setup>
import { computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import CustomerLayout from '../../components/customer/CustomerLayout.vue';
import { useCustomerStore } from '../../stores/customer';

const route = useRoute();
const customerStore = useCustomerStore();

const order = computed(() => customerStore.currentOrder);
const errorMessage = computed(() => customerStore.errors.message || '');

const shippingAddress = computed(() => {
  return order.value?.shipping_address || order.value?.shippingAddress || null;
});

const loadOrder = async () => {
  if (!customerStore.user) {
    await customerStore.fetchUser();
  }

  await customerStore.fetchOrder(route.params.id);
};

const fullName = (address) => {
  if (!address) return '—';

  const firstName = address.first_name || '';
  const lastName = address.last_name || '';
  const combined = `${firstName} ${lastName}`.trim();

  return combined || 'Sin nombre';
};

const statusLabel = (status) => {
  const labels = {
    pending: 'Pendiente',
    processing: 'Pagado',
    shipped: 'Enviado',
    delivered: 'Completado',
    cancelled: 'Cancelado',
    refunded: 'Reembolsado',
  };

  return labels[status] || status;
};

const paymentStatusLabel = (status) => {
  const labels = {
    pending: 'Pendiente',
    paid: 'Pagado',
    failed: 'Fallido',
    refunded: 'Reembolsado',
  };

  return labels[status] || status || 'No especificado';
};

const statusClass = (status) => {
  const classes = {
    pending: 'bg-amber-100 text-amber-700',
    processing: 'bg-sky-100 text-sky-700',
    shipped: 'bg-indigo-100 text-indigo-700',
    delivered: 'bg-emerald-100 text-emerald-700',
    cancelled: 'bg-red-100 text-red-700',
    refunded: 'bg-gray-200 text-gray-700',
  };

  return classes[status] || 'bg-gray-100 text-gray-700';
};

const formatDateTime = (value) => {
  if (!value) return '—';

  return new Intl.DateTimeFormat('es-ES', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(new Date(value));
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat('es-ES', {
    style: 'currency',
    currency: 'USD',
  }).format(Number(value || 0));
};

onMounted(async () => {
  await loadOrder();
});

watch(() => route.params.id, async () => {
  await loadOrder();
});
</script>
