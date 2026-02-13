<template>
  <div>
    <a
      href="#main-content"
      class="sr-only rounded-md bg-blue-600 px-4 py-3 font-semibold text-white focus:not-sr-only focus:absolute focus:left-3 focus:top-3 focus:z-[999]"
    >
      Saltar al contenido principal
    </a>

    <slot name="header"></slot>

    <main id="main-content" tabindex="-1">
      <slot></slot>
    </main>

    <slot name="footer"></slot>
  </div>
</template>

<script setup>
</script>
