<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="text-center text-3xl font-extrabold text-gray-900">
        Iniciar sesión
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Accede a tu cuenta
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <FormError
          v-if="authStore.generalError"
          :message="authStore.generalError"
          :dismissible="true"
          @dismiss="authStore.clearErrors()"
          class="mb-6"
        />

        <form @submit.prevent="handleSubmit" class="space-y-6">
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

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input
                id="remember"
                v-model="form.remember"
                type="checkbox"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <label for="remember" class="ml-2 block text-sm text-gray-900">
                Recordarme
              </label>
            </div>

            <div class="text-sm">
              <a href="/forgot-password" class="font-medium text-blue-600 hover:text-blue-500">
                ¿Olvidaste tu contraseña?
              </a>
            </div>
          </div>

          <SubmitButton
            text="Iniciar sesión"
            loading-text="Iniciando sesión..."
            :loading="authStore.loading"
            :disabled="!isFormValid"
          />
        </form>

        <div class="mt-6">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">
                ¿No tienes cuenta?
              </span>
            </div>
          </div>

          <div class="mt-6">
            <a
              href="/register"
              class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all"
            >
              Crear cuenta
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useAuthStore } from '../../stores/auth';
import { useCartStore } from '../../stores/cart';
import InputText from '../../components/forms/InputText.vue';
import InputPassword from '../../components/forms/InputPassword.vue';
import FormError from '../../components/forms/FormError.vue';
import SubmitButton from '../../components/forms/SubmitButton.vue';

const authStore = useAuthStore();
const cartStore = useCartStore();

const form = ref({
  email: '',
  password: '',
  remember: false,
});

const isFormValid = computed(() => {
  return (
    form.value.email.trim() !== '' &&
    form.value.password.length >= 6
  );
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
    // Sincronizar carrito
    await cartStore.fetchCart();
    
    // Redirigir
    const redirectTo = new URLSearchParams(window.location.search).get('redirect') || '/';
    window.location.href = redirectTo;
  }
};
</script>
