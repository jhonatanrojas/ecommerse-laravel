<template>
  <div class="min-h-screen bg-gradient-to-br from-white via-gray-50 to-indigo-50/50 px-4 py-8 sm:px-6 lg:py-12">
    <div class="mx-auto grid w-full max-w-6xl overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-xl lg:grid-cols-2">
      <aside class="relative hidden bg-gradient-to-br from-indigo-600 to-indigo-700 p-10 text-white lg:flex lg:flex-col lg:justify-between">
        <div>
          <a href="/" class="text-2xl font-extrabold tracking-tight">{{ appName }}</a>
          <p class="mt-6 text-sm uppercase tracking-[0.2em] text-indigo-100">Nueva cuenta</p>
          <h1 class="mt-3 text-4xl font-extrabold leading-tight">Empieza a comprar con una cuenta personalizada.</h1>
          <p class="mt-4 max-w-md text-indigo-100">
            Guarda direcciones, revisa pedidos y acelera cada compra desde tu perfil.
          </p>
        </div>

        <div class="rounded-2xl border border-white/20 bg-white/10 p-4 text-sm text-indigo-50">
          Registro rápido, checkout más ágil y beneficios para clientes registrados.
        </div>
      </aside>

      <main class="p-6 sm:p-10 lg:p-12">
        <div class="mx-auto w-full max-w-md">
          <a href="/" class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700 lg:hidden">
            <span aria-hidden="true">←</span> Volver al inicio
          </a>

          <p class="mt-4 text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">Registro</p>
          <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900">Crear cuenta</h2>
          <p class="mt-2 text-sm text-gray-500">Únete a nuestra comunidad y finaliza tu compra más rápido.</p>

          <FormError
            v-if="authStore.generalError"
            :message="authStore.generalError"
            :dismissible="true"
            @dismiss="authStore.clearErrors()"
            class="mt-6"
          />

          <form @submit.prevent="handleSubmit" class="mt-6 space-y-5">
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

            <label class="inline-flex items-start gap-2 text-sm text-gray-700">
              <input
                id="terms"
                v-model="form.acceptTerms"
                type="checkbox"
                class="mt-0.5 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                required
              />
              <span>
                Acepto los
                <a href="/terms" class="font-semibold text-indigo-600 hover:text-indigo-700">términos y condiciones</a>
                y la
                <a href="/privacy" class="font-semibold text-indigo-600 hover:text-indigo-700">política de privacidad</a>
              </span>
            </label>

            <SubmitButton
              text="Crear cuenta"
              loading-text="Creando cuenta..."
              :loading="authStore.loading"
              :disabled="!isFormValid"
            />
          </form>

          <div class="mt-8 border-t border-gray-100 pt-6 text-sm text-gray-600">
            ¿Ya tienes cuenta?
            <a href="/login" class="font-semibold text-indigo-600 hover:text-indigo-700">Iniciar sesión</a>
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
    await cartStore.fetchCart();

    const redirectTo = new URLSearchParams(window.location.search).get('redirect') || '/';
    window.location.href = redirectTo;
  }
};
</script>
