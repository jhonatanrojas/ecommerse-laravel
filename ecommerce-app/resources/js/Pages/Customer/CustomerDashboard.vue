<template>
  <div class="min-h-screen bg-[var(--color-surface)]">
    <ToastContainer ref="toastRef" />

    <!-- Header Navigation -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <!-- Logo / Home Link -->
          <a href="/" class="flex items-center gap-2 text-gray-900 hover:text-indigo-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span class="font-semibold">Volver a la tienda</span>
          </a>

          <!-- User Info & Logout -->
          <div class="flex items-center gap-4">
            <div class="hidden sm:block text-right">
              <div class="text-sm font-semibold text-gray-900">
                {{ customerStore.user?.name || 'Usuario' }}
              </div>
              <div class="text-xs text-gray-500">
                {{ customerStore.user?.email || '' }}
              </div>
            </div>
            <button
              @click="handleLogout"
              :disabled="loggingOut"
              class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors disabled:opacity-50"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
              </svg>
              <span class="hidden sm:inline">{{ loggingOut ? 'Saliendo...' : 'Cerrar sesión' }}</span>
            </button>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
          Mi cuenta
        </h1>
        <p class="text-gray-500 mt-1">
          Gestiona tu perfil, contraseña, direcciones y órdenes.
        </p>
      </div>

      <FormError
        v-if="customerStore.errors.message"
        class="mb-6"
        type="error"
        :message="customerStore.errors.message"
        :errors="customerStore.errors.fields"
        dismissible
        @dismiss="customerStore.clearErrors()"
      />

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna izquierda -->
        <div class="lg:col-span-1 space-y-6">
          <div class="card p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">
              Perfil
            </h2>

            <div v-if="customerStore.loading.user" class="space-y-3">
              <div class="skeleton h-4 w-2/3"></div>
              <div class="skeleton h-4 w-5/6"></div>
              <div class="skeleton h-4 w-1/2"></div>
            </div>

            <div v-else class="space-y-3">
              <div class="text-sm">
                <div class="text-gray-500">Nombre</div>
                <div class="font-semibold text-gray-900">
                  {{ customerStore.user?.name || '—' }}
                </div>
              </div>

              <div class="text-sm">
                <div class="text-gray-500">Email</div>
                <div class="font-semibold text-gray-900 break-all">
                  {{ customerStore.user?.email || '—' }}
                </div>
              </div>

              <div class="text-sm">
                <div class="text-gray-500">Teléfono</div>
                <div class="font-semibold text-gray-900">
                  {{ customerStore.user?.phone || customerStore.user?.customer?.phone || '—' }}
                </div>
              </div>
            </div>
          </div>

          <div class="card p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">
              Contraseña
            </h2>
            <PasswordForm
              :loading="customerStore.loading.password"
              :field-errors="customerStore.errors.fields"
              @submit="onUpdatePassword"
            />
          </div>
        </div>

        <!-- Columna derecha -->
        <div class="lg:col-span-2 space-y-6">
          <div class="card p-6">
            <div class="flex items-start justify-between gap-4">
              <div>
                <h2 class="text-lg font-bold text-gray-900">
                  Direcciones
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                  Crea y administra tus direcciones de envío y facturación.
                </p>
              </div>

              <button
                type="button"
                class="btn-primary whitespace-nowrap"
                @click="openCreateAddress()"
              >
                Nueva dirección
              </button>
            </div>

            <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
              <div class="card p-5 border border-gray-100 shadow-none hover:shadow-none">
                <div class="flex items-center justify-between mb-3">
                  <h3 class="font-bold text-gray-900">Envío</h3>
                  <span
                    v-if="customerStore.defaultShippingAddress"
                    class="text-xs font-semibold px-2 py-1 rounded-full bg-green-50 text-green-700 border border-green-200"
                  >
                    Por defecto: {{ customerStore.defaultShippingAddress?.first_name }} {{ customerStore.defaultShippingAddress?.last_name }}
                  </span>
                </div>

                <AddressList
                  :addresses="customerStore.shippingAddresses"
                  type="shipping"
                  :loading="customerStore.loading.addresses || customerStore.loading.addressMutation || customerStore.loading.defaultAddress"
                  @edit="openEditAddress"
                  @delete="onDeleteAddress"
                  @set-default="onSetDefaultAddress"
                />
              </div>

              <div class="card p-5 border border-gray-100 shadow-none hover:shadow-none">
                <div class="flex items-center justify-between mb-3">
                  <h3 class="font-bold text-gray-900">Facturación</h3>
                  <span
                    v-if="customerStore.defaultBillingAddress"
                    class="text-xs font-semibold px-2 py-1 rounded-full bg-green-50 text-green-700 border border-green-200"
                  >
                    Por defecto: {{ customerStore.defaultBillingAddress?.first_name }} {{ customerStore.defaultBillingAddress?.last_name }}
                  </span>
                </div>

                <AddressList
                  :addresses="customerStore.billingAddresses"
                  type="billing"
                  :loading="customerStore.loading.addresses || customerStore.loading.addressMutation || customerStore.loading.defaultAddress"
                  @edit="openEditAddress"
                  @delete="onDeleteAddress"
                  @set-default="onSetDefaultAddress"
                />
              </div>
            </div>

            <div v-if="showAddressForm" class="mt-6 border-t pt-6">
              <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-900">
                  {{ editingAddress ? 'Editar dirección' : 'Nueva dirección' }}
                </h3>
                <button
                  type="button"
                  class="text-sm font-semibold text-gray-600 hover:text-gray-900"
                  @click="closeAddressForm"
                >
                  Cerrar
                </button>
              </div>

              <AddressForm
                :address="editingAddress"
                :default-type="prefillType"
                :loading="customerStore.loading.addressMutation"
                :field-errors="customerStore.errors.fields"
                @cancel="closeAddressForm"
                @submit="onSaveAddress"
              />
            </div>
          </div>

          <div class="card p-6">
            <div class="flex items-start justify-between gap-4">
              <div>
                <h2 class="text-lg font-bold text-gray-900">
                  Órdenes realizadas
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                  Revisa tus pedidos recientes o entra al historial completo.
                </p>
              </div>

              <router-link
                to="/customer/orders"
                class="px-4 py-2 rounded-xl border border-indigo-200 bg-indigo-50 text-sm font-semibold text-indigo-700 hover:bg-indigo-100 transition"
              >
                Ver todas
              </router-link>
            </div>

            <div v-if="customerStore.loading.orders" class="mt-6 space-y-3">
              <div v-for="n in 3" :key="`dashboard-order-skeleton-${n}`" class="skeleton h-20 w-full rounded-xl"></div>
            </div>

            <div v-else-if="!customerStore.orders?.length" class="mt-6 rounded-xl border border-dashed border-gray-200 bg-gray-50 p-6 text-center">
              <p class="text-sm font-semibold text-gray-800">Aún no tienes órdenes</p>
              <p class="mt-1 text-sm text-gray-500">Cuando completes una compra aparecerá aquí.</p>
              <a href="/" class="btn-primary mt-4 inline-flex px-4 py-2 text-sm">Explorar productos</a>
            </div>

            <div v-else class="mt-6 space-y-3">
              <article
                v-for="order in recentOrders"
                :key="order.uuid"
                class="rounded-xl border border-gray-100 p-4"
              >
                <div class="flex flex-wrap items-start justify-between gap-3">
                  <div>
                    <p class="text-sm font-semibold text-gray-900">Orden #{{ order.order_number }}</p>
                    <p class="mt-1 text-xs text-gray-500">{{ formatDate(order.created_at) }}</p>
                  </div>
                  <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="statusClass(order.status)">
                    {{ statusLabel(order.status) }}
                  </span>
                </div>

                <div class="mt-3 flex items-center justify-between">
                  <p class="text-xs uppercase tracking-[0.12em] text-gray-500">Total</p>
                  <p class="text-sm font-bold text-gray-900">{{ formatCurrency(order.total) }}</p>
                </div>

                <router-link
                  :to="`/customer/orders/${order.uuid}`"
                  class="mt-3 inline-flex rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                >
                  Ver detalle
                </router-link>
              </article>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useCustomerStore } from '../../stores/customer';
import { useAuthStore } from '../../stores/auth';
import { useToast } from '../../composables/useToast';

import ToastContainer from '../../components/ui/ToastContainer.vue';
import FormError from '../../components/forms/FormError.vue';

import PasswordForm from '../../components/customer/PasswordForm.vue';
import AddressForm from '../../components/customer/AddressForm.vue';
import AddressList from '../../components/customer/AddressList.vue';

const customerStore = useCustomerStore();
const authStore = useAuthStore();
const { setToastContainer, success, error: toastError } = useToast();

const toastRef = ref(null);
const loggingOut = ref(false);

const showAddressForm = ref(false);
const editingAddress = ref(null);
const prefillType = ref('shipping');
const recentOrders = computed(() => (customerStore.orders || []).slice(0, 3));

onMounted(async () => {
  setToastContainer(toastRef.value);

  // Cargar todo en paralelo para mejor UX
  await Promise.allSettled([
    customerStore.fetchUser(),
    customerStore.fetchAddresses(),
    customerStore.fetchOrders(),
  ]);
});

async function handleLogout() {
  loggingOut.value = true;
  const result = await authStore.logout();
  
  if (result.success) {
    window.location.href = '/';
  } else {
    toastError('Error', 'No se pudo cerrar sesión. Intenta de nuevo.');
    loggingOut.value = false;
  }
}

function openCreateAddress(type = 'shipping') {
  customerStore.clearErrors();
  prefillType.value = type;
  editingAddress.value = null;
  showAddressForm.value = true;
}

function openEditAddress(address) {
  customerStore.clearErrors();
  prefillType.value = address?.type || 'shipping';
  editingAddress.value = address;
  showAddressForm.value = true;
}

function closeAddressForm() {
  editingAddress.value = null;
  showAddressForm.value = false;
}

async function onSaveAddress(payload) {
  const isEdit = Boolean(editingAddress.value?.id);

  const result = isEdit
    ? await customerStore.updateAddress(editingAddress.value.id, payload)
    : await customerStore.addAddress(payload);

  if (result.success) {
    success('Listo', isEdit ? 'Dirección actualizada.' : 'Dirección creada.');
    closeAddressForm();
    return;
  }

  toastError('No se pudo guardar', result.message || 'Revisa los campos e intenta de nuevo.');
}

async function onDeleteAddress(address) {
  if (!address?.id) return;

  const confirmed = window.confirm('¿Eliminar esta dirección? Esta acción no se puede deshacer.');
  if (!confirmed) return;

  const result = await customerStore.deleteAddress(address.id);
  if (result.success) {
    success('Eliminada', 'La dirección se eliminó correctamente.');
    return;
  }
  toastError('No se pudo eliminar', result.message || 'Intenta de nuevo.');
}

async function onSetDefaultAddress({ address, type }) {
  if (!address?.uuid) return;

  const result = await customerStore.setDefaultAddress({ address_id: address.uuid, type });
  if (result.success) {
    success('Actualizado', 'Dirección por defecto actualizada.');
    return;
  }
  toastError('No se pudo actualizar', result.message || 'Intenta de nuevo.');
}

async function onUpdatePassword(payload) {
  const result = await customerStore.updatePassword(payload);
  if (result.success) {
    success('Contraseña actualizada', result.message || 'Se guardó correctamente.');
    return;
  }
  toastError('No se pudo cambiar', result.message || 'Revisa los campos e intenta de nuevo.');
}

function statusLabel(status) {
  const labels = {
    pending: 'Pendiente',
    processing: 'Pagado',
    shipped: 'Enviado',
    delivered: 'Completado',
    cancelled: 'Cancelado',
    refunded: 'Reembolsado',
  };

  return labels[status] || status;
}

function statusClass(status) {
  const classes = {
    pending: 'bg-amber-100 text-amber-700',
    processing: 'bg-sky-100 text-sky-700',
    shipped: 'bg-indigo-100 text-indigo-700',
    delivered: 'bg-emerald-100 text-emerald-700',
    cancelled: 'bg-red-100 text-red-700',
    refunded: 'bg-gray-200 text-gray-700',
  };

  return classes[status] || 'bg-gray-100 text-gray-700';
}

function formatDate(value) {
  if (!value) return '—';

  return new Intl.DateTimeFormat('es-ES', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  }).format(new Date(value));
}

function formatCurrency(value) {
  return new Intl.NumberFormat('es-ES', {
    style: 'currency',
    currency: 'USD',
  }).format(Number(value || 0));
}
</script>

