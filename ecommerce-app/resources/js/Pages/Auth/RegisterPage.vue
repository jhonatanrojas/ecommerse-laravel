<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="text-center text-3xl font-extrabold text-gray-900">
        Crear cuenta
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Únete a nuestra comunidad
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
            v-model="form.name"
            name="name"
            label="Nombre completo"
            placeholder="Juan Pérez"
            :required="true"
            :error="authStore.getFieldError('name')"
            autocomplete="name"
          />

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

          <InputText
            v-model="form.phone"
            name="phone"
            type="tel"
            label="Teléfono"
            placeholder="+34 600 000 000"
            :error="authStore.getFieldError('phone')"
            autocomplete="tel"
            hint="Opcional"
          />

          <InputPassword
            v-model="form.password"
            name="password"
            label="Contraseña"
            placeholder="Mínimo 8 caracteres"
            :required="true"
            :error="authStore.getFieldError('password')"
            autocomplete="new-password"
            hint="Mínimo 8 caracteres"
          />

          <InputPassword
            v-model="form.password_confirmation"
            name="password_confirmation"
            label="Confirmar contraseña"
            placeholder="Repite tu contraseña"
            :required="true"
            :error="authStore.getFieldError('password_confirmation')"
            autocomplete="new-password"
          />

          <div class="flex items-center">
            <input
              id="terms"
              v-model="form.acceptTerms"
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              required
            />
            <label for="terms" class="ml-2 block text-sm text-gray-900">
              Acepto los 
              <a href="/terms" class="text-blue-600 hover:text-blue-500">términos y condiciones</a>
              y la 
              <a href="/privacy" class="text-blue-600 hover:text-blue-500">política de privacidad</a>
            </label>
          </div>

          <SubmitButton
            text="Crear cuenta"
            loading-text="Creando cuenta..."
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
                ¿Ya tienes cuenta?
              </span>
            </div>
          </div>

          <div class="mt-6">
            <a
              href="/login"
              class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all"
            >
              Iniciar sesión
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
  name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
  acceptTerms: false,
});

const isFormValid = computed(() => {
  return (
    form.value.name.trim() !== '' &&
    form.value.email.trim() !== '' &&
    form.value.password.length >= 8 &&
    form.value.password === form.value.password_confirmation &&
    form.value.acceptTerms
  );
});

const handleSubmit = async () => {
  if (!isFormValid.value) {
    return;
  }

  const result = await authStore.register({
    name: form.value.name,
    email: form.value.email,
    phone: form.value.phone || null,
    password: form.value.password,
    password_confirmation: form.value.password_confirmation,
  });

  if (result.success) {
    // Sincronizar carrito del invitado con el usuario autenticado
    await cartStore.fetchCart();
    
    // Redirigir al home o a la página anterior
    const redirectTo = new URLSearchParams(window.location.search).get('redirect') || '/';
    window.location.href = redirectTo;
  }
};
</script>
