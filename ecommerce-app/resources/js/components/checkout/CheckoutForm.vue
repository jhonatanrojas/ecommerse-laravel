<template>
  <div class="space-y-6">
    <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-md sm:p-6">
      <CheckoutSectionTitle
        title="Datos del cliente"
        subtitle="Verifica quién recibirá la orden"
        eyebrow="Paso 1"
      />
      <CustomerDataSection />
    </section>

    <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-md sm:p-6">
      <CheckoutSectionTitle
        title="Dirección de envío"
        subtitle="Completa los datos para la entrega"
        eyebrow="Paso 2"
      />
      <ShippingAddressForm />
    </section>

    <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-md sm:p-6">
      <CheckoutSectionTitle
        title="Dirección de facturación"
        subtitle="Puedes usar la misma dirección del envío"
        eyebrow="Paso 3"
      />
      <BillingAddressForm />
    </section>

    <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-md sm:p-6">
      <CheckoutSectionTitle
        title="Método de envío"
        subtitle="Elige la opción de entrega para tu pedido"
        eyebrow="Paso 4"
      />
      <ShippingMethods />
    </section>

    <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-md sm:p-6">
      <CheckoutSectionTitle
        title="Método de pago"
        subtitle="Selecciona cómo deseas pagar"
        eyebrow="Paso 5"
      />
      <PaymentMethods />
    </section>

    <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-md sm:p-6">
      <CheckoutSectionTitle
        title="Notas del pedido"
        subtitle="Opcional: indicaciones especiales para la entrega"
        eyebrow="Paso 6"
      />
      <CheckoutInput
        id="order-notes"
        v-model="notes"
        type="textarea"
        placeholder="Ejemplo: entregar después de las 5pm, tocar timbre dos veces."
        :rows="4"
      />
    </section>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useCheckoutStore } from '../../stores/checkout';
import CustomerDataSection from './CustomerDataSection.vue';
import ShippingAddressForm from './ShippingAddressForm.vue';
import BillingAddressForm from './BillingAddressForm.vue';
import ShippingMethods from './ShippingMethods.vue';
import PaymentMethods from './PaymentMethods.vue';
import CheckoutInput from './CheckoutInput.vue';
import CheckoutSectionTitle from './CheckoutSectionTitle.vue';

const checkoutStore = useCheckoutStore();

const notes = computed({
  get: () => checkoutStore.notes,
  set: (value) => checkoutStore.setNotes(value),
});
</script>
