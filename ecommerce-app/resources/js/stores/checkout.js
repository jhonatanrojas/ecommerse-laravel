import { defineStore } from 'pinia';
import checkoutService from '../services/checkoutService';
import { useToast } from '../composables/useToast';

const toast = useToast();

/**
 * Checkout Store
 * Manages the complete checkout process state
 */
export const useCheckoutStore = defineStore('checkout', {
  state: () => ({
    // Cart data
    cart: null,
    summary: null,
    
    // Store configuration
    storeConfig: null,
    
    // Address data
    shippingAddress: {
      fullName: '',
      addressLine1: '',
      addressLine2: '',
      city: '',
      state: '',
      postalCode: '',
      country: '',
    },
    billingAddress: {
      fullName: '',
      addressLine1: '',
      addressLine2: '',
      city: '',
      state: '',
      postalCode: '',
      country: '',
    },
    useSameAddress: true,
    
    // Shipping and payment methods
    shippingMethod: null,
    paymentMethod: null,
    
    // Additional data
    notes: '',
    
    // UI states
    loading: false,
    submitting: false,
    errors: {},
    
    // Order result
    order: null,
    
    // Available methods (can be fetched from API or hardcoded)
    availableShippingMethods: [
      {
        id: 'standard',
        name: 'Envío Estándar',
        description: 'Entrega en 5-7 días hábiles',
        estimatedDays: '5-7 días',
        cost: 5.00,
      },
      {
        id: 'express',
        name: 'Envío Express',
        description: 'Entrega en 2-3 días hábiles',
        estimatedDays: '2-3 días',
        cost: 15.00,
      },
      {
        id: 'priority',
        name: 'Envío Prioritario',
        description: 'Entrega en 1 día hábil',
        estimatedDays: '1 día',
        cost: 25.00,
      },
    ],
    availablePaymentMethods: [
      {
        id: 'credit_card',
        name: 'Tarjeta de Crédito/Débito',
        description: 'Pago seguro con tarjeta',
        icon: 'credit-card',
      },
      {
        id: 'paypal',
        name: 'PayPal',
        description: 'Pago rápido y seguro',
        icon: 'paypal',
      },
      {
        id: 'bank_transfer',
        name: 'Transferencia Bancaria',
        description: 'Transferencia directa',
        icon: 'bank',
      },
      {
        id: 'cash_on_delivery',
        name: 'Pago contra entrega',
        description: 'Paga al recibir tu pedido',
        icon: 'cash',
      },
    ],
  }),

  getters: {
    /**
     * Check if guest checkout is allowed
     */
    allowGuestCheckout: (state) => state.storeConfig?.allow_guest_checkout ?? false,

    /**
     * Get cart items
     */
    items: (state) => state.cart?.items || [],

    /**
     * Check if cart is empty
     */
    isEmpty: (state) => !state.cart?.items || state.cart.items.length === 0,

    /**
     * Get item count
     */
    itemCount: (state) => state.summary?.item_count || 0,

    /**
     * Get subtotal
     */
    subtotal: (state) => state.summary?.subtotal || 0,

    /**
     * Get discount
     */
    discount: (state) => state.summary?.discount || 0,

    /**
     * Get shipping cost
     */
    shippingCost: (state) => state.shippingMethod?.cost || 0,

    /**
     * Calculate total amount including shipping
     */
    totalAmount: (state) => {
      const baseTotal = state.summary?.total || 0;
      const shipping = state.shippingMethod?.cost || 0;
      return baseTotal + shipping;
    },

    /**
     * Check if all required data is valid
     */
    isValid: (state) => {
      // Check shipping address
      const shippingValid = state.shippingAddress.fullName &&
        state.shippingAddress.addressLine1 &&
        state.shippingAddress.city &&
        state.shippingAddress.state &&
        state.shippingAddress.postalCode &&
        state.shippingAddress.country;

      // Check billing address
      const billingValid = state.useSameAddress || (
        state.billingAddress.fullName &&
        state.billingAddress.addressLine1 &&
        state.billingAddress.city &&
        state.billingAddress.state &&
        state.billingAddress.postalCode &&
        state.billingAddress.country
      );

      // Check methods
      const methodsValid = state.shippingMethod && state.paymentMethod;

      return shippingValid && billingValid && methodsValid;
    },

    /**
     * Check if there are validation errors
     */
    hasErrors: (state) => Object.keys(state.errors).length > 0,

    /**
     * Get billing address (use shipping if same address is checked)
     */
    effectiveBillingAddress: (state) => {
      return state.useSameAddress ? state.shippingAddress : state.billingAddress;
    },
  },

  actions: {
    /**
     * Load store configuration
     */
    async loadStoreConfig() {
      try {
        const response = await checkoutService.getStoreConfig();
        const data = response.data || response;
        this.storeConfig = data;
        return { success: true };
      } catch (error) {
        console.error('Error loading store config:', error);
        return { success: false };
      }
    },

    /**
     * Load cart from API
     */
    async loadCart() {
      this.loading = true;
      this.errors = {};
      
      try {
        const response = await checkoutService.getCart();
        const data = response.data || response;
        
        // Handle both response formats
        if (data.cart && data.summary) {
          this.cart = data.cart;
          this.summary = data.summary;
        } else if (data.items !== undefined) {
          // Empty cart response
          this.cart = { items: data.items };
          this.summary = data.summary;
        }
        
        return { success: true };
      } catch (error) {
        const message = error.response?.data?.message || 'Error al cargar el carrito';
        this.errors.general = [message];
        console.error('Error loading cart:', error);
        return { success: false, error: message };
      } finally {
        this.loading = false;
      }
    },

    /**
     * Set shipping address
     * @param {Address} address
     */
    setShippingAddress(address) {
      this.shippingAddress = { ...address };
      
      // If using same address, update billing too
      if (this.useSameAddress) {
        this.billingAddress = { ...address };
      }
      
      // Clear address-related errors
      delete this.errors.shipping_address;
    },

    /**
     * Set billing address
     * @param {Address} address
     */
    setBillingAddress(address) {
      this.billingAddress = { ...address };
      
      // Clear address-related errors
      delete this.errors.billing_address;
    },

    /**
     * Toggle use same address for billing
     * @param {boolean} value
     */
    toggleSameAddress(value) {
      this.useSameAddress = value;
      
      // If enabled, copy shipping to billing
      if (value) {
        this.billingAddress = { ...this.shippingAddress };
      }
    },

    /**
     * Set shipping method
     * @param {ShippingMethod} method
     */
    setShippingMethod(method) {
      this.shippingMethod = method;
      
      // Clear shipping method errors
      delete this.errors.shipping_method;
    },

    /**
     * Set payment method
     * @param {PaymentMethod} method
     */
    setPaymentMethod(method) {
      this.paymentMethod = method;
      
      // Clear payment method errors
      delete this.errors.payment_method;
    },

    /**
     * Set order notes
     * @param {string} notes
     */
    setNotes(notes) {
      this.notes = notes;
    },

    /**
     * Validate checkout data
     * @returns {boolean}
     */
    validateCheckout() {
      this.errors = {};
      let isValid = true;

      // Validate shipping address
      if (!this.shippingAddress.fullName) {
        this.errors.shipping_address = this.errors.shipping_address || [];
        this.errors.shipping_address.push('El nombre completo es requerido');
        isValid = false;
      }
      if (!this.shippingAddress.addressLine1) {
        this.errors.shipping_address = this.errors.shipping_address || [];
        this.errors.shipping_address.push('La dirección es requerida');
        isValid = false;
      }
      if (!this.shippingAddress.city) {
        this.errors.shipping_address = this.errors.shipping_address || [];
        this.errors.shipping_address.push('La ciudad es requerida');
        isValid = false;
      }
      if (!this.shippingAddress.state) {
        this.errors.shipping_address = this.errors.shipping_address || [];
        this.errors.shipping_address.push('El estado/provincia es requerido');
        isValid = false;
      }
      if (!this.shippingAddress.postalCode) {
        this.errors.shipping_address = this.errors.shipping_address || [];
        this.errors.shipping_address.push('El código postal es requerido');
        isValid = false;
      }
      if (!this.shippingAddress.country) {
        this.errors.shipping_address = this.errors.shipping_address || [];
        this.errors.shipping_address.push('El país es requerido');
        isValid = false;
      }

      // Validate billing address if not using same address
      if (!this.useSameAddress) {
        if (!this.billingAddress.fullName) {
          this.errors.billing_address = this.errors.billing_address || [];
          this.errors.billing_address.push('El nombre completo es requerido');
          isValid = false;
        }
        if (!this.billingAddress.addressLine1) {
          this.errors.billing_address = this.errors.billing_address || [];
          this.errors.billing_address.push('La dirección es requerida');
          isValid = false;
        }
        if (!this.billingAddress.city) {
          this.errors.billing_address = this.errors.billing_address || [];
          this.errors.billing_address.push('La ciudad es requerida');
          isValid = false;
        }
        if (!this.billingAddress.state) {
          this.errors.billing_address = this.errors.billing_address || [];
          this.errors.billing_address.push('El estado/provincia es requerido');
          isValid = false;
        }
        if (!this.billingAddress.postalCode) {
          this.errors.billing_address = this.errors.billing_address || [];
          this.errors.billing_address.push('El código postal es requerido');
          isValid = false;
        }
        if (!this.billingAddress.country) {
          this.errors.billing_address = this.errors.billing_address || [];
          this.errors.billing_address.push('El país es requerido');
          isValid = false;
        }
      }

      // Validate shipping method
      if (!this.shippingMethod) {
        this.errors.shipping_method = ['Debe seleccionar un método de envío'];
        isValid = false;
      }

      // Validate payment method
      if (!this.paymentMethod) {
        this.errors.payment_method = ['Debe seleccionar un método de pago'];
        isValid = false;
      }

      return isValid;
    },

    /**
     * Submit checkout
     */
    async submitCheckout() {
      // Validate before submitting
      if (!this.validateCheckout()) {
        toast.error('Validación fallida', 'Por favor complete todos los campos requeridos');
        return { success: false, error: 'Por favor complete todos los campos requeridos' };
      }

      this.submitting = true;
      this.errors = {};

      try {
        // Prepare payload
        const payload = {
          shipping_address: {
            full_name: this.shippingAddress.fullName,
            address_line_1: this.shippingAddress.addressLine1,
            address_line_2: this.shippingAddress.addressLine2 || '',
            city: this.shippingAddress.city,
            state: this.shippingAddress.state,
            postal_code: this.shippingAddress.postalCode,
            country: this.shippingAddress.country,
          },
          billing_address: {
            full_name: this.effectiveBillingAddress.fullName,
            address_line_1: this.effectiveBillingAddress.addressLine1,
            address_line_2: this.effectiveBillingAddress.addressLine2 || '',
            city: this.effectiveBillingAddress.city,
            state: this.effectiveBillingAddress.state,
            postal_code: this.effectiveBillingAddress.postalCode,
            country: this.effectiveBillingAddress.country,
          },
          shipping_method: this.shippingMethod.id,
          payment_method: this.paymentMethod.id,
          notes: this.notes || '',
        };

        // Submit to API
        const response = await checkoutService.submitCheckout(payload);
        const data = response.data || response;
        
        // Store order result
        this.order = data;
        
        // Show success notification
        toast.success('¡Pedido realizado!', 'Tu pedido ha sido procesado exitosamente');
        
        return { success: true, order: data };
      } catch (error) {
        // Handle validation errors
        if (error.validationErrors) {
          this.errors = error.validationErrors;
          toast.error('Error de validación', 'Por favor revisa los campos marcados');
        } else {
          const message = error.response?.data?.message || 'Error al procesar el pedido';
          this.errors.general = [message];
          toast.error('Error al procesar', message);
        }
        
        console.error('Error submitting checkout:', error);
        return { 
          success: false, 
          error: error.response?.data?.message || 'Error al procesar el pedido' 
        };
      } finally {
        this.submitting = false;
      }
    },

    /**
     * Clear all errors
     */
    clearErrors() {
      this.errors = {};
    },

    /**
     * Reset checkout state
     */
    reset() {
      this.cart = null;
      this.summary = null;
      this.shippingAddress = {
        fullName: '',
        addressLine1: '',
        addressLine2: '',
        city: '',
        state: '',
        postalCode: '',
        country: '',
      };
      this.billingAddress = {
        fullName: '',
        addressLine1: '',
        addressLine2: '',
        city: '',
        state: '',
        postalCode: '',
        country: '',
      };
      this.useSameAddress = true;
      this.shippingMethod = null;
      this.paymentMethod = null;
      this.notes = '';
      this.loading = false;
      this.submitting = false;
      this.errors = {};
      this.order = null;
    },
  },
});

export default useCheckoutStore;
