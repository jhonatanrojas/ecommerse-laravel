<template>
  <div class="container mx-auto px-4 py-5">
    <section class="grid grid-cols-1 gap-4 lg:grid-cols-[320px_1fr]">
      <aside class="rounded-md border border-gray-200 bg-white p-4">
        <h2 class="text-sm font-extrabold uppercase tracking-wide text-gray-600">Conversaciones</h2>
        <div class="mt-3 rounded border border-amber-200 bg-amber-50 p-3">
          <p class="text-sm font-bold text-gray-800">Orden {{ route.params.orderUuid }}</p>
          <p class="text-xs text-gray-600">Compra directa en proceso</p>
        </div>
      </aside>

      <div class="rounded-md border border-gray-200 bg-white p-4">
        <h1 class="text-xl font-black">Mensajería interna</h1>
        <p class="mt-2 text-sm text-gray-600">
          Esta vista funciona como destino de redirección tras “Comprar ahora”.
          Puedes conectar aquí tu módulo de chat real entre comprador y vendedor.
        </p>

        <div class="mt-6 h-[420px] rounded border border-dashed border-gray-300 bg-gray-50 p-4 text-sm text-gray-500">
          Chat para la orden <strong>{{ route.params.orderUuid }}</strong>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router';

const route = useRoute();
</script>
