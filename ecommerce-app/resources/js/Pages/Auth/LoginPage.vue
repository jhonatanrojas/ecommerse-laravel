<template>
  <div class="min-h-screen bg-gradient-to-br from-white via-gray-50 to-indigo-50/50 px-4 py-8 sm:px-6 lg:py-12">
    <div class="mx-auto grid w-full max-w-6xl overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-xl lg:grid-cols-2">
      <aside class="relative hidden bg-gradient-to-br from-indigo-600 to-indigo-700 p-10 text-white lg:flex lg:flex-col lg:justify-between">
        <div>
          <a href="/" class="text-2xl font-extrabold tracking-tight">{{ appName }}</a>
          <p class="mt-6 text-sm uppercase tracking-[0.2em] text-indigo-100">Bienvenido de vuelta</p>
          <h1 class="mt-3 text-4xl font-extrabold leading-tight">Tu cuenta, tu carrito y tus pedidos en un solo lugar.</h1>
          <p class="mt-4 max-w-md text-indigo-100">
            Inicia sesión para continuar tu compra con una experiencia rápida y segura.
          </p>
        </div>

        <div class="rounded-2xl border border-white/20 bg-white/10 p-4 text-sm text-indigo-50">
          Checkout optimizado, historial de pedidos y seguimiento en tiempo real.
        </div>
      </aside>

      <main class="p-6 sm:p-10 lg:p-12">
        <div class="mx-auto w-full max-w-md">
          <a href="/" class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700 lg:hidden">
            <span aria-hidden="true">←</span> Volver al inicio
          </a>

          <p class="mt-4 text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">Acceso</p>
          <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900">Iniciar sesión</h2>
          <p class="mt-2 text-sm text-gray-500">Accede a tu cuenta para continuar.</p>

          <FormError
            v-if="authStore.generalError"
            :message="authStore.generalError"
            :dismissible="true"
            @dismiss="authStore.clearErrors()"
            class="mt-6"
          />

          <form @submit.prevent="handleSubmit" class="mt-6 space-y-5">
            <InputText
              v-model="form.email"
              name="email"
              type="email"
              label="Correo electrónico"
              placeholder="tu@email.com"
              :required="true"
              :error="authStore.getFieldError('email')"
              autocomplete="email"
            />

            <InputPassword
              v-model="form.password"
              name="password"
              label="Contraseña"
              placeholder="Tu contraseña"
              :required="true"
              :error="authStore.getFieldError('password')"
              autocomplete="current-password"
            />

            <div class="flex items-center justify-between gap-4">
              <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                <input
                  id="remember"
                  v-model="form.remember"
                  type="checkbox"
                  class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                />
                Recordarme
              </label>

              <a href="/forgot-password" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                ¿Olvidaste tu contraseña?
              </a>
            </div>

            <SubmitButton
              text="Iniciar sesión"
              loading-text="Iniciando sesión..."
              :loading="authStore.loading"
              :disabled="!isFormValid"
            />
          </form>

          <div class="mt-8 border-t border-gray-100 pt-6 text-sm text-gray-600">
            ¿No tienes cuenta?
            <a href="/register" class="font-semibold text-indigo-600 hover:text-indigo-700">Crear cuenta</a>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useAuthStore } from '../../stores/auth';
import { useCartStore } from '../../stores/cart';
import InputText from '../../components/forms/InputText.vue';
import InputPassword from '../../components/forms/InputPassword.vue';
import FormError from '../../components/forms/FormError.vue';
import SubmitButton from '../../components/forms/SubmitButton.vue';

const authStore = useAuthStore();
const cartStore = useCartStore();

const appName = window.AppConfig?.name || 'Tienda';

const form = ref({
  email: '',
  password: '',
  remember: false,
});

const isFormValid = computed(() => {
  return form.value.email.trim() !== '' && form.value.password.length >= 6;
});

const handleSubmit = async () => {
  if (!isFormValid.value) {
    return;
  }

  const result = await authStore.login({
    email: form.value.email,
    password: form.value.password,
    remember: form.value.remember,
  });

  if (result.success) {
    await cartStore.fetchCart();

    const redirectTo = new URLSearchParams(window.location.search).get('redirect') || '/';
    window.location.href = redirectTo;
  }
};
</script>
