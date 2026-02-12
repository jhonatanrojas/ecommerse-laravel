<template>
  <form class="space-y-4" @submit.prevent="submit">
    <InputPassword
      name="current_password"
      label="Contraseña actual"
      v-model="form.current_password"
      :error="fieldError('current_password') || localErrors.current_password"
      autocomplete="current-password"
      required
    />

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <InputPassword
        name="password"
        label="Nueva contraseña"
        v-model="form.password"
        :error="fieldError('password') || localErrors.password"
        autocomplete="new-password"
        required
        hint="Mínimo 8 caracteres."
      />

      <InputPassword
        name="password_confirmation"
        label="Confirmar contraseña"
        v-model="form.password_confirmation"
        :error="fieldError('password_confirmation') || localErrors.password_confirmation"
        autocomplete="new-password"
        required
      />
    </div>

    <SubmitButton
      text="Actualizar contraseña"
      loading-text="Actualizando..."
      :loading="loading"
      :disabled="loading"
    />
  </form>
</template>

<script setup>
import { computed, reactive } from 'vue';
import InputPassword from '../forms/InputPassword.vue';
import SubmitButton from '../forms/SubmitButton.vue';

const props = defineProps({
  loading: { type: Boolean, default: false },
  fieldErrors: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['submit']);

const form = reactive({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const localErrors = reactive({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const fieldError = (field) => props.fieldErrors?.[field]?.[0] || '';

const validate = () => {
  localErrors.current_password = '';
  localErrors.password = '';
  localErrors.password_confirmation = '';

  if (!form.current_password) localErrors.current_password = 'Ingresa tu contraseña actual.';
  if (!form.password) localErrors.password = 'Ingresa una nueva contraseña.';
  if (form.password && form.password.length < 8) localErrors.password = 'La contraseña debe tener al menos 8 caracteres.';
  if (!form.password_confirmation) localErrors.password_confirmation = 'Confirma tu nueva contraseña.';
  if (form.password && form.password_confirmation && form.password !== form.password_confirmation) {
    localErrors.password_confirmation = 'La confirmación no coincide.';
  }

  return !localErrors.current_password && !localErrors.password && !localErrors.password_confirmation;
};

const submit = () => {
  if (!validate()) return;

  emit('submit', {
    current_password: form.current_password,
    password: form.password,
    password_confirmation: form.password_confirmation,
  });
};
</script>

