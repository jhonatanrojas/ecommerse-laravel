<template>
  <section class="section-padding relative overflow-hidden" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);">
    <!-- Decorative circles -->
    <div class="absolute top-0 right-0 w-64 h-64 rounded-full bg-white/5 -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-48 h-48 rounded-full bg-white/5 translate-y-1/2 -translate-x-1/2"></div>

    <div class="container mx-auto px-4 relative z-10">
      <div class="max-w-2xl mx-auto text-center">
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-white mb-3">
          ¿No te quieres perder nada?
        </h2>
        <p class="text-indigo-200 text-base md:text-lg mb-8">
          Suscríbete y obtén un <strong class="text-white">10% de descuento</strong> en tu primera compra. Ofertas exclusivas cada semana.
        </p>

        <!-- Form -->
        <form @submit.prevent="subscribe" class="flex flex-col sm:flex-row gap-3 max-w-lg mx-auto">
          <div class="flex-1 relative">
            <input
              v-model="email"
              type="email"
              placeholder="tu@email.com"
              required
              class="w-full px-5 py-3.5 rounded-xl bg-white/15 border border-white/25 text-white placeholder-indigo-200 focus:outline-none focus:ring-2 focus:ring-white/50 focus:bg-white/20 transition-all duration-200 text-sm"
              :class="{ 'ring-2 ring-red-400': emailError }"
              aria-label="Email para newsletter"
            />
            <p v-if="emailError" class="absolute -bottom-5 left-0 text-xs text-red-300">{{ emailError }}</p>
          </div>
          <button
            type="submit"
            :disabled="submitting"
            class="px-6 py-3.5 bg-white text-indigo-600 font-bold rounded-xl hover:bg-indigo-50 transition-all duration-200 text-sm whitespace-nowrap disabled:opacity-60"
          >
            <span v-if="!submitting">Suscribirme</span>
            <span v-else class="flex items-center gap-2">
              <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Enviando...
            </span>
          </button>
        </form>

        <!-- Success message -->
        <transition
          enter-active-class="transition ease-out duration-300"
          enter-from-class="opacity-0 translate-y-2"
          enter-to-class="opacity-100 translate-y-0"
        >
          <p v-if="success" class="mt-4 text-sm text-emerald-300 font-medium">
            🎉 ¡Gracias por suscribirte! Revisa tu email para el código de descuento.
          </p>
        </transition>

        <p class="mt-4 text-xs text-indigo-300">
          Sin spam. Puedes cancelar en cualquier momento.
        </p>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'NewsletterSection',
  props: {
    section: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      email: '',
      emailError: '',
      submitting: false,
      success: false,
    };
  },
  methods: {
    async subscribe() {
      this.emailError = '';

      if (!this.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email)) {
        this.emailError = 'Por favor, ingresa un email válido.';
        return;
      }

      this.submitting = true;

      // Simulate API call
      try {
        await new Promise(resolve => setTimeout(resolve, 1200));
        this.success = true;
        this.email = '';
      } catch (err) {
        this.emailError = 'Ocurrió un error. Intenta de nuevo.';
      } finally {
        this.submitting = false;
      }
    },
  },
};
</script>
