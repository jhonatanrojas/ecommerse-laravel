<template>
  <div class="space-y-4">
    <div v-if="loading" class="space-y-3">
      <div class="skeleton h-24 w-full"></div>
      <div class="skeleton h-24 w-full"></div>
    </div>

    <div v-else-if="!addresses || addresses.length === 0" class="text-center py-8">
      <svg
        class="mx-auto h-12 w-12 text-gray-400"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
        />
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
        />
      </svg>
      <p class="mt-2 text-sm text-gray-500">
        No hay direcciones de {{ type === 'shipping' ? 'envío' : 'facturación' }} registradas.
      </p>
    </div>

    <div v-else class="space-y-3">
      <div
        v-for="address in addresses"
        :key="address.id"
        class="p-4 border rounded-lg transition-all"
        :class="[
          address.is_default
            ? 'border-green-300 bg-green-50'
            : 'border-gray-200 bg-white hover:border-gray-300'
        ]"
      >
        <div class="flex items-start justify-between gap-3">
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
              <p class="font-semibold text-gray-900">
                {{ address.first_name }} {{ address.last_name }}
              </p>
              <span
                v-if="address.is_default"
                class="text-xs font-semibold px-2 py-0.5 rounded-full bg-green-100 text-green-700"
              >
                Por defecto
              </span>
            </div>
            <p class="text-sm text-gray-600">{{ address.address_line_1 }}</p>
            <p v-if="address.address_line_2" class="text-sm text-gray-600">
              {{ address.address_line_2 }}
            </p>
            <p class="text-sm text-gray-600">
              {{ address.city }}, {{ address.state }} {{ address.postal_code }}
            </p>
            <p class="text-sm text-gray-600">{{ address.country }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ address.phone }}</p>
          </div>

          <div class="flex flex-col gap-2">
            <button
              v-if="!address.is_default"
              type="button"
              @click="$emit('set-default', { address, type })"
              :disabled="loading"
              class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-green-200 text-green-700 hover:bg-green-50 transition disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap"
            >
              Marcar por defecto
            </button>
            <button
              type="button"
              @click="$emit('edit', address)"
              :disabled="loading"
              class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-blue-200 text-blue-700 hover:bg-blue-50 transition disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Editar
            </button>
            <button
              type="button"
              @click="$emit('delete', address)"
              :disabled="loading"
              class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50 transition disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Eliminar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  addresses: { type: Array, default: () => [] },
  type: { type: String, required: true },
  loading: { type: Boolean, default: false },
});

defineEmits(['edit', 'delete', 'set-default']);
</script>
