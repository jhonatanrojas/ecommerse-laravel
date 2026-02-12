<template>
  <CustomerLayout
    title="Órdenes realizadas"
    subtitle="Consulta el historial completo de tus compras y revisa el estado de cada pedido."
    eyebrow="Mi cuenta"
  >
    <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-md sm:p-6">
      <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
        <div>
          <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">Estado</label>
          <select
            v-model="filters.status"
            class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-100"
          >
            <option value="">Todos</option>
            <option value="pending">Pendiente</option>
            <option value="processing">Pagado</option>
            <option value="shipped">Enviado</option>
            <option value="delivered">Completado</option>
          </select>
        </div>

        <div>
          <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">Desde</label>
          <input
            v-model="filters.dateFrom"
            type="date"
            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-100"
          />
        </div>

        <div>
          <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">Hasta</label>
          <input
            v-model="filters.dateTo"
            type="date"
            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-100"
          />
        </div>

        <div class="flex items-end gap-2">
          <button
            type="button"
            class="inline-flex w-full items-center justify-center rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
            @click="resetFilters"
          >
            Limpiar filtros
          </button>
          <button
            type="button"
            class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
            :disabled="customerStore.loading.orders"
            @click="refreshOrders"
          >
            {{ customerStore.loading.orders ? 'Cargando...' : 'Actualizar' }}
          </button>
        </div>
      </div>
    </div>

    <div class="mt-6 rounded-2xl border border-gray-100 bg-white p-4 shadow-md sm:p-6">
      <div class="mb-4 flex items-center justify-between gap-3 border-b border-gray-100 pb-4">
        <p class="text-sm text-gray-600">
          <span class="font-semibold text-gray-900">{{ filteredOrders.length }}</span>
          órdenes encontradas
        </p>
        <a href="/customer" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">Volver a mi cuenta</a>
      </div>

      <div v-if="customerStore.loading.orders" class="space-y-3">
        <div v-for="n in 5" :key="`order-skeleton-${n}`" class="animate-pulse rounded-xl border border-gray-100 p-4">
          <div class="h-4 w-44 rounded bg-gray-100"></div>
          <div class="mt-3 h-3 w-56 rounded bg-gray-100"></div>
          <div class="mt-4 h-9 rounded bg-gray-100"></div>
        </div>
      </div>

      <div v-else-if="customerStore.errors.message" class="rounded-xl border border-red-200 bg-red-50 p-5 text-sm text-red-700">
        <p class="font-semibold text-red-800">No pudimos cargar tus órdenes.</p>
        <p class="mt-1">{{ customerStore.errors.message }}</p>
        <button
          type="button"
          class="mt-3 inline-flex rounded-lg border border-red-300 bg-white px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-100"
          @click="refreshOrders"
        >
          Reintentar
        </button>
      </div>

      <div v-else-if="filteredOrders.length === 0" class="py-10 text-center">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-500">
          <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 11H4L5 9z" />
          </svg>
        </div>
        <p class="mt-4 text-base font-semibold text-gray-900">No hay órdenes para estos filtros</p>
        <p class="mt-1 text-sm text-gray-500">Prueba con otro rango de fechas o estado.</p>
        <a href="/" class="btn-primary mt-5 inline-flex px-5 py-2.5 text-sm">Explorar productos</a>
      </div>

      <div v-else>
        <div class="hidden overflow-x-auto md:block">
          <table class="min-w-full divide-y divide-gray-100">
            <thead>
              <tr class="text-left text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">
                <th class="px-3 py-3">Orden</th>
                <th class="px-3 py-3">Fecha</th>
                <th class="px-3 py-3">Estado</th>
                <th class="px-3 py-3 text-right">Total</th>
                <th class="px-3 py-3 text-right">Acción</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
              <tr v-for="order in paginatedOrders" :key="order.uuid" class="hover:bg-gray-50/70">
                <td class="px-3 py-4 font-semibold text-gray-900">#{{ order.order_number }}</td>
                <td class="px-3 py-4 text-gray-600">{{ formatDate(order.created_at) }}</td>
                <td class="px-3 py-4">
                  <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="statusClass(order.status)">
                    {{ statusLabel(order.status) }}
                  </span>
                </td>
                <td class="px-3 py-4 text-right font-semibold text-gray-900">{{ formatCurrency(order.total) }}</td>
                <td class="px-3 py-4 text-right">
                  <router-link
                    :to="`/customer/orders/${order.uuid}`"
                    class="inline-flex rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100"
                  >
                    Ver detalles
                  </router-link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="space-y-3 md:hidden">
          <article
            v-for="order in paginatedOrders"
            :key="order.uuid"
            class="rounded-xl border border-gray-100 p-4"
          >
            <div class="flex items-start justify-between gap-3">
              <div>
                <p class="text-sm font-semibold text-gray-900">Orden #{{ order.order_number }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ formatDate(order.created_at) }}</p>
              </div>
              <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="statusClass(order.status)">
                {{ statusLabel(order.status) }}
              </span>
            </div>

            <div class="mt-4 flex items-center justify-between">
              <p class="text-xs uppercase tracking-[0.12em] text-gray-500">Total</p>
              <p class="text-base font-bold text-gray-900">{{ formatCurrency(order.total) }}</p>
            </div>

            <router-link
              :to="`/customer/orders/${order.uuid}`"
              class="mt-4 inline-flex w-full justify-center rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-100"
            >
              Ver detalles
            </router-link>
          </article>
        </div>

        <div v-if="totalPages > 1" class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-4">
          <p class="text-xs text-gray-500">Página {{ currentPage }} de {{ totalPages }}</p>
          <div class="inline-flex items-center gap-2">
            <button
              type="button"
              class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 disabled:cursor-not-allowed disabled:opacity-40"
              :disabled="currentPage === 1"
              @click="currentPage--"
            >
              Anterior
            </button>
            <button
              type="button"
              class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 disabled:cursor-not-allowed disabled:opacity-40"
              :disabled="currentPage === totalPages"
              @click="currentPage++"
            >
              Siguiente
            </button>
          </div>
        </div>
      </div>
    </div>
  </CustomerLayout>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue';
import CustomerLayout from '../../components/customer/CustomerLayout.vue';
import { useCustomerStore } from '../../stores/customer';

const customerStore = useCustomerStore();

const filters = reactive({
  status: '',
  dateFrom: '',
  dateTo: '',
});

const currentPage = ref(1);
const perPage = 8;

const filteredOrders = computed(() => {
  return (customerStore.orders || []).filter((order) => {
    if (filters.status && order.status !== filters.status) {
      return false;
    }

    const orderDate = order.created_at ? new Date(order.created_at) : null;

    if (filters.dateFrom && orderDate) {
      const from = new Date(`${filters.dateFrom}T00:00:00`);
      if (orderDate < from) {
        return false;
      }
    }

    if (filters.dateTo && orderDate) {
      const to = new Date(`${filters.dateTo}T23:59:59`);
      if (orderDate > to) {
        return false;
      }
    }

    return true;
  });
});

const totalPages = computed(() => {
  return Math.max(1, Math.ceil(filteredOrders.value.length / perPage));
});

const paginatedOrders = computed(() => {
  const start = (currentPage.value - 1) * perPage;
  return filteredOrders.value.slice(start, start + perPage);
});

watch(filteredOrders, () => {
  if (currentPage.value > totalPages.value) {
    currentPage.value = totalPages.value;
  }
});

watch(() => [filters.status, filters.dateFrom, filters.dateTo], () => {
  currentPage.value = 1;
});

const resetFilters = () => {
  filters.status = '';
  filters.dateFrom = '';
  filters.dateTo = '';
};

const refreshOrders = async () => {
  await customerStore.fetchOrders();
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

const formatDate = (value) => {
  if (!value) return '—';

  return new Intl.DateTimeFormat('es-ES', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  }).format(new Date(value));
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat('es-ES', {
    style: 'currency',
    currency: 'USD',
  }).format(Number(value || 0));
};

onMounted(async () => {
  if (!customerStore.user) {
    await customerStore.fetchUser();
  }

  await customerStore.fetchOrders();
});
</script>
