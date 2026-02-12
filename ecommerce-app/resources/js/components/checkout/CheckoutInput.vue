<template>
  <div class="space-y-1.5">
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>

    <textarea
      v-if="type === 'textarea'"
      :id="id"
      :name="name"
      :value="modelValue"
      :rows="rows"
      :placeholder="placeholder"
      :disabled="disabled"
      :autocomplete="autocomplete"
      class="w-full rounded-xl border bg-white px-4 py-2.5 text-sm text-gray-800 transition placeholder:text-gray-400 focus:outline-none focus:ring-2"
      :class="inputClasses"
      @input="$emit('update:modelValue', $event.target.value)"
      @blur="$emit('blur')"
    ></textarea>

    <select
      v-else-if="type === 'select'"
      :id="id"
      :name="name"
      :value="modelValue"
      :disabled="disabled"
      class="w-full rounded-xl border bg-white px-4 py-2.5 text-sm text-gray-800 transition focus:outline-none focus:ring-2"
      :class="inputClasses"
      @change="$emit('update:modelValue', $event.target.value)"
      @blur="$emit('blur')"
    >
      <option value="">{{ placeholder || 'Seleccionar' }}</option>
      <option
        v-for="option in options"
        :key="option.value"
        :value="option.value"
      >
        {{ option.label }}
      </option>
    </select>

    <input
      v-else
      :id="id"
      :name="name"
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :disabled="disabled"
      :autocomplete="autocomplete"
      class="w-full rounded-xl border bg-white px-4 py-2.5 text-sm text-gray-800 transition placeholder:text-gray-400 focus:outline-none focus:ring-2"
      :class="inputClasses"
      @input="$emit('update:modelValue', $event.target.value)"
      @blur="$emit('blur')"
    />

    <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
    <p v-else-if="help" class="text-xs text-gray-500">{{ help }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  id: {
    type: String,
    required: true,
  },
  name: {
    type: String,
    default: '',
  },
  label: {
    type: String,
    default: '',
  },
  modelValue: {
    type: [String, Number],
    default: '',
  },
  type: {
    type: String,
    default: 'text',
  },
  placeholder: {
    type: String,
    default: '',
  },
  rows: {
    type: Number,
    default: 4,
  },
  error: {
    type: String,
    default: '',
  },
  help: {
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
  options: {
    type: Array,
    default: () => [],
  },
  autocomplete: {
    type: String,
    default: '',
  },
});

defineEmits(['update:modelValue', 'blur']);

const inputClasses = computed(() => {
  if (props.error) {
    return 'border-red-300 focus:border-red-400 focus:ring-red-100';
  }

  return 'border-gray-200 focus:border-indigo-300 focus:ring-indigo-100';
});
</script>
