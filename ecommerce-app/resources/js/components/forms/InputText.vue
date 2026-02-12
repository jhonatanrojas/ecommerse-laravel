<template>
  <div class="w-full space-y-1.5">
    <label
      v-if="label"
      :for="id"
      class="block text-sm font-medium text-gray-700"
    >
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>

    <input
      :id="id"
      :type="type"
      :name="name"
      :value="modelValue"
      :placeholder="placeholder"
      :required="required"
      :disabled="disabled"
      :autocomplete="autocomplete"
      class="w-full rounded-xl border px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 transition focus:outline-none focus:ring-2"
      :class="inputClasses"
      @input="$emit('update:modelValue', $event.target.value)"
      @blur="$emit('blur')"
    />

    <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
    <p v-else-if="hint" class="text-xs text-gray-500">{{ hint }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: '',
  },
  label: {
    type: String,
    default: '',
  },
  type: {
    type: String,
    default: 'text',
  },
  name: {
    type: String,
    required: true,
  },
  id: {
    type: String,
    default: null,
  },
  placeholder: {
    type: String,
    default: '',
  },
  required: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  error: {
    type: String,
    default: '',
  },
  hint: {
    type: String,
    default: '',
  },
  autocomplete: {
    type: String,
    default: 'off',
  },
});

defineEmits(['update:modelValue', 'blur']);

const id = computed(() => props.id || props.name);

const inputClasses = computed(() => {
  if (props.disabled) {
    return 'cursor-not-allowed border-gray-200 bg-gray-100 text-gray-500';
  }

  if (props.error) {
    return 'border-red-300 bg-white focus:border-red-400 focus:ring-red-100';
  }

  return 'border-gray-200 bg-white focus:border-indigo-300 focus:ring-indigo-100';
});
</script>
