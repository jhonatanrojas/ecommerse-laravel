<template>
  <section class="bg-gradient-to-r from-blue-50 to-purple-50 py-12">
    <div class="container mx-auto px-4">
      <div class="grid grid-cols-2 gap-4 md:grid-cols-4 md:gap-6">
        <article
          v-for="(item, index) in benefits"
          :key="item.title"
          ref="cards"
          class="rounded-2xl border border-white/70 bg-white/70 p-4 text-center shadow-sm transition duration-500"
          :style="{ transitionDelay: `${index * 100}ms` }"
          :class="visible ? 'translate-y-0 opacity-100' : 'translate-y-6 opacity-0'"
        >
          <p class="text-4xl" aria-hidden="true">{{ item.icon }}</p>
          <h3 class="mt-3 text-sm font-extrabold text-gray-900 md:text-base">{{ item.title }}</h3>
          <p class="mt-1 text-xs text-gray-600 md:text-sm">{{ item.description }}</p>
        </article>
      </div>
    </div>
  </section>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';

const benefits = [
  { icon: '🚚', title: 'Envio Gratis', description: 'En compras desde $49' },
  { icon: '🔒', title: 'Pago Seguro', description: 'Proteccion de compra garantizada' },
  { icon: '↩️', title: 'Devoluciones', description: '30 dias para devolver' },
  { icon: '💬', title: 'Soporte 24/7', description: 'Atencion cuando la necesites' },
];

const cards = ref([]);
const visible = ref(false);
let observer = null;

onMounted(() => {
  observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting && entry.intersectionRatio >= 0.5) {
          visible.value = true;
        }
      });
    },
    { threshold: [0.5] },
  );

  cards.value.forEach((el) => {
    if (el) observer.observe(el);
  });
});

onBeforeUnmount(() => {
  if (!observer) return;
  cards.value.forEach((el) => {
    if (el) observer.unobserve(el);
  });
});
</script>
