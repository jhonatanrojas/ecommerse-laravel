<template>
  <div class="space-y-4">
    <div v-if="loading" class="space-y-3">
      <div class="skeleton h-32 w-full"></div>
      <div class="skeleton h-32 w-full"></div>
      <div class="skeleton h-32 w-full"></div>
    </div>

    <div v-else-if="!orders || orders.length === 0" class="text-center py-12">
      <svg
        class="mx-auto h-16 w-16 text-gray-400"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
        />
      </svg>
      <p class="mt-4 text-base text-gray-500">
        Aún no has realizado ninguna compra.
      </p>
      <a
        href="/"
        class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition"
      >
        Explorar productos
      </a>
    </div>

    <div v-else class="space-y-4">
      <div
        v-for="order in orders"
        :key="order.id"
        class="card p-5 hover:shadow-lg transition-all"
      >
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
          <div>
            <div class="flex items-center gap-3 mb-1">
              <h3 class="text-lg font-bold text-gray-900">
                Orden #{{ order.order_number }}
              </h3>
              <span
                class="text-xs font-semibold px-2.5 py-1 rounded-full"
                :class="getStatusClass(order.status)"
              >
                {{ getStatusLabel(order.status) }}
              </span>
            </div>
            <p class="text-sm text-gray-500">
              {{ formatDate(order.created_at) }}
            </p>
          </div>

          <div class="text-left sm:text-right">
            <p class="text-sm text-gray-500">Total</p>
            <p class="text-2xl font-bold text-gray-900">
              {{ formatCurrency(order.total) }}
            </p>
          </div>
        </div>

        <div v-if="order.items && order.items.length > 0" class="border-t pt-4">
          <p class="text-sm font-medium text-gray-700 mb-3">
            Productos ({{ order.items.length }})
          </p>
          <div class="space-y-2">
            <div
              v-for="item in order.items"
              :key="item.id"
              class="flex items-center gap-3 text-sm"
            >
              <div
                v-if="item.product?.image_url"
                class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100"
              >
                <img
                  :src="item.product.image_url"
                  :alt="item.product_name"
                  class="w-full h-full object-cover"
                />
              </div>
              <div class="flex-1 min-w-0">
                <p class="font-medium text-gray-900 truncate">
                  {{ item.product_name }}
                </p>
                <p class="text-gray-500">
                  Cantidad: {{ item.quantity }} × {{ formatCurrency(item.price) }}
                </p>
              </div>
              <p class="font-semibold text-gray-900 whitespace-nowrap">
                {{ formatCurrency(item.subtotal) }}
              </p>
            </div>
          </div>
        </div>

        <div class="border-t mt-4 pt-4 flex flex-col sm:flex-row gap-3">
          <button
            type="button"
            @click="viewOrderDetails(order)"
            class="flex-1 px-4 py-2 border border-gray-300 text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-50 transition"
          >
            Ver detalles
          </button>
          <button
            v-if="canReorder(order.status)"
            type="button"
            @click="reorder(order)"
            class="flex-1 px-4 py-2 bg-blue-600 text-sm font-semibold text-white rounded-lg hover:bg-blue-700 transition"
          >
            Volver a comprar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  orders: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
});

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-blue-100 text-blue-800',
    shipped: 'bg-purple-100 text-purple-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
    refunded: 'bg-gray-100 text-gray-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status) => {
  const labels = {
    pending: 'Pendiente',
    processing: 'Procesando',
    shipped: 'Enviado',
    delivered: 'Entregado',
    cancelled: 'Cancelado',
    refunded: 'Reembolsado',
  };
  return labels[status] || status;
};

const formatDate = (dateString) => {
  if (!dateString) return '—';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(date);
};

const formatCurrency = (amount) => {
  if (amount === null || amount === undefined) return '—';
  return new Intl.NumberFormat('es-ES', {
    style: 'currency',
    currency: 'USD',
  }).format(amount);
};

const canReorder = (status) => {
  return ['delivered', 'cancelled'].includes(status);
};

const viewOrderDetails = (order) => {
  // Navegar a la página de detalles de la orden
  window.location.href = `/customer/orders/${order.id}`;
};

const reorder = (order) => {
  // Lógica para volver a comprar (agregar items al carrito)
  console.log('Reordering:', order);
  // TODO: Implementar lógica de reorden
  alert('Funcionalidad de reorden en desarrollo');
};
</script>
