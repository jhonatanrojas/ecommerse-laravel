# Ejemplos de Uso del Carrito

## 1. Botón Simple en Tarjeta de Producto

```vue
<template>
  <div class="product-card">
    <img :src="product.image" :alt="product.name" />
    <h3>{{ product.name }}</h3>
    <p>${{ product.price }}</p>
    
    <AddToCartButton
      :product-id="product.id"
      button-text="Añadir al carrito"
    />
  </div>
</template>

<script>
export default {
  props: ['product']
}
</script>
```

## 2. Con Variantes y Selector de Cantidad

```vue
<template>
  <div class="product-detail">
    <h1>{{ product.name }}</h1>
    
    <!-- Selector de variante -->
    <select v-model="selectedVariant">
      <option v-for="variant in product.variants" :key="variant.id" :value="variant">
        {{ variant.name }} - ${{ variant.price }}
      </option>
    </select>
    
    <!-- Selector de cantidad -->
    <div class="quantity-selector">
      <button @click="quantity--" :disabled="quantity <= 1">-</button>
      <input v-model.number="quantity" type="number" min="1" />
      <button @click="quantity++">+</button>
    </div>
    
    <!-- Botón añadir al carrito -->
    <AddToCartButton
      :product-id="product.id"
      :variant-id="selectedVariant?.id"
      :quantity="quantity"
      :stock="selectedVariant?.stock || product.stock"
      button-text="Añadir al carrito"
      button-class="btn-primary btn-lg w-full"
      @added="handleAdded"
      @error="handleError"
    />
  </div>
</template>

<script>
export default {
  data() {
    return {
      selectedVariant: null,
      quantity: 1
    }
  },
  methods: {
    handleAdded() {
      // Mostrar mensaje de éxito
      this.$emit('show-toast', {
        type: 'success',
        message: 'Producto añadido al carrito'
      });
    },
    handleError(error) {
      // Mostrar mensaje de error
      this.$emit('show-toast', {
        type: 'error',
        message: error
      });
    }
  }
}
</script>
```

## 3. Botón Comprar Ahora (Añade y va al checkout)

```vue
<template>
  <div class="product-actions">
    <AddToCartButton
      :product-id="product.id"
      button-text="Añadir al carrito"
      button-class="btn-secondary"
    />
    
    <button
      @click="buyNow"
      :disabled="buying"
      class="btn-primary"
    >
      {{ buying ? 'Procesando...' : 'Comprar ahora' }}
    </button>
  </div>
</template>

<script>
import { useCartStore } from '@/stores/cart';

export default {
  props: ['product'],
  data() {
    return {
      buying: false
    }
  },
  setup() {
    const cartStore = useCartStore();
    return { cartStore };
  },
  methods: {
    async buyNow() {
      this.buying = true;
      
      const result = await this.cartStore.addItem(this.product.id, null, 1);
      
      if (result.success) {
        // Redirigir al checkout
        window.location.href = '/checkout';
      } else {
        alert(result.error);
        this.buying = false;
      }
    }
  }
}
</script>
```

## 4. Lista de Productos con Quick Add

```vue
<template>
  <div class="products-grid">
    <div v-for="product in products" :key="product.id" class="product-card">
      <a :href="`/products/${product.slug}`">
        <img :src="product.image" :alt="product.name" />
        <h3>{{ product.name }}</h3>
        <p>${{ product.price }}</p>
      </a>
      
      <!-- Quick add button -->
      <AddToCartButton
        :product-id="product.id"
        :stock="product.stock"
        button-text="+"
        button-class="quick-add-btn"
        @added="showQuickAddFeedback(product)"
      />
    </div>
  </div>
</template>

<script>
export default {
  props: ['products'],
  methods: {
    showQuickAddFeedback(product) {
      // Mostrar mini toast o animación
      console.log(`${product.name} añadido al carrito`);
    }
  }
}
</script>

<style scoped>
.quick-add-btn {
  position: absolute;
  bottom: 10px;
  right: 10px;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  font-size: 20px;
}
</style>
```

## 5. Usar el Store Directamente (Sin Componente)

```vue
<template>
  <div class="custom-add-to-cart">
    <button
      @click="addToCart"
      :disabled="isAdding"
      class="custom-button"
    >
      <span v-if="isAdding">
        <LoadingSpinner />
        Añadiendo...
      </span>
      <span v-else-if="justAdded">
        <CheckIcon />
        ¡Añadido!
      </span>
      <span v-else>
        <CartIcon />
        Añadir al carrito
      </span>
    </button>
  </div>
</template>

<script>
import { ref } from 'vue';
import { useCartStore } from '@/stores/cart';

export default {
  props: ['product'],
  setup(props) {
    const cartStore = useCartStore();
    const isAdding = ref(false);
    const justAdded = ref(false);
    
    const addToCart = async () => {
      isAdding.value = true;
      
      const result = await cartStore.addItem(props.product.id, null, 1);
      
      isAdding.value = false;
      
      if (result.success) {
        justAdded.value = true;
        setTimeout(() => {
          justAdded.value = false;
        }, 2000);
      } else {
        alert(result.error);
      }
    };
    
    return {
      isAdding,
      justAdded,
      addToCart
    };
  }
}
</script>
```

## 6. Abrir Carrito Programáticamente

```vue
<template>
  <div>
    <button @click="viewCart">
      Ver carrito ({{ cartStore.itemCount }})
    </button>
    
    <button @click="addAndViewCart">
      Añadir y ver carrito
    </button>
  </div>
</template>

<script>
import { useCartStore } from '@/stores/cart';

export default {
  setup() {
    const cartStore = useCartStore();
    
    const viewCart = () => {
      cartStore.openDrawer();
    };
    
    const addAndViewCart = async () => {
      await cartStore.addItem(productId, null, 1);
      // El drawer se abre automáticamente al añadir
    };
    
    return {
      cartStore,
      viewCart,
      addAndViewCart
    };
  }
}
</script>
```

## 7. Mostrar Información del Carrito en Cualquier Lugar

```vue
<template>
  <div class="cart-summary">
    <p>Items: {{ cartStore.itemCount }}</p>
    <p>Subtotal: ${{ formatPrice(cartStore.subtotal) }}</p>
    <p v-if="cartStore.discount > 0">
      Descuento: -${{ formatPrice(cartStore.discount) }}
    </p>
    <p class="total">Total: ${{ formatPrice(cartStore.total) }}</p>
    
    <button @click="cartStore.openDrawer()">
      Ver detalles
    </button>
  </div>
</template>

<script>
import { useCartStore } from '@/stores/cart';

export default {
  setup() {
    const cartStore = useCartStore();
    
    const formatPrice = (price) => {
      return parseFloat(price).toFixed(2);
    };
    
    return {
      cartStore,
      formatPrice
    };
  }
}
</script>
```

## 8. Validación Personalizada Antes de Añadir

```vue
<template>
  <div>
    <AddToCartButton
      v-if="canAddToCart"
      :product-id="product.id"
      :stock="product.stock"
    />
    
    <div v-else class="out-of-stock">
      <p>Producto no disponible</p>
      <button @click="notifyWhenAvailable">
        Notificarme cuando esté disponible
      </button>
    </div>
  </div>
</template>

<script>
export default {
  props: ['product'],
  computed: {
    canAddToCart() {
      return this.product.stock > 0 && 
             this.product.is_active && 
             !this.product.is_discontinued;
    }
  },
  methods: {
    notifyWhenAvailable() {
      // Lógica para notificaciones
    }
  }
}
</script>
```

## 9. Integrar con Sistema de Wishlist

```vue
<template>
  <div class="product-actions">
    <button
      @click="toggleWishlist"
      :class="{ 'in-wishlist': isInWishlist }"
      class="wishlist-btn"
    >
      <HeartIcon :filled="isInWishlist" />
    </button>
    
    <AddToCartButton
      :product-id="product.id"
      button-class="btn-primary flex-1"
      @added="removeFromWishlistIfNeeded"
    />
  </div>
</template>

<script>
export default {
  props: ['product'],
  data() {
    return {
      isInWishlist: false
    }
  },
  methods: {
    toggleWishlist() {
      this.isInWishlist = !this.isInWishlist;
      // Guardar en backend
    },
    removeFromWishlistIfNeeded() {
      if (this.isInWishlist) {
        // Opcional: remover de wishlist al añadir al carrito
        this.isInWishlist = false;
      }
    }
  }
}
</script>
```

## 10. Página de Producto Completa

```vue
<template>
  <div class="product-page">
    <div class="product-gallery">
      <!-- Galería de imágenes -->
    </div>
    
    <div class="product-info">
      <h1>{{ product.name }}</h1>
      <div class="price">
        <span class="current-price">${{ product.price }}</span>
        <span v-if="product.sale_price" class="original-price">
          ${{ product.original_price }}
        </span>
      </div>
      
      <!-- Variantes -->
      <div v-if="product.variants.length" class="variants">
        <label>Selecciona una opción:</label>
        <div class="variant-options">
          <button
            v-for="variant in product.variants"
            :key="variant.id"
            @click="selectedVariant = variant"
            :class="{ active: selectedVariant?.id === variant.id }"
            class="variant-btn"
          >
            {{ variant.name }}
          </button>
        </div>
      </div>
      
      <!-- Cantidad -->
      <div class="quantity-section">
        <label>Cantidad:</label>
        <div class="quantity-controls">
          <button @click="decreaseQuantity">-</button>
          <input v-model.number="quantity" type="number" min="1" />
          <button @click="increaseQuantity">+</button>
        </div>
        <span v-if="availableStock" class="stock-info">
          {{ availableStock }} disponibles
        </span>
      </div>
      
      <!-- Botones de acción -->
      <div class="action-buttons">
        <AddToCartButton
          :product-id="product.id"
          :variant-id="selectedVariant?.id"
          :quantity="quantity"
          :stock="availableStock"
          button-text="Añadir al carrito"
          button-class="btn-primary btn-lg"
          @added="handleAdded"
          @error="handleError"
        />
        
        <button @click="buyNow" class="btn-secondary btn-lg">
          Comprar ahora
        </button>
      </div>
      
      <!-- Información adicional -->
      <div class="product-details">
        <p>{{ product.description }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import { useCartStore } from '@/stores/cart';

export default {
  props: ['product'],
  data() {
    return {
      selectedVariant: null,
      quantity: 1
    }
  },
  setup() {
    const cartStore = useCartStore();
    return { cartStore };
  },
  computed: {
    availableStock() {
      if (this.selectedVariant) {
        return this.selectedVariant.stock;
      }
      return this.product.stock;
    }
  },
  methods: {
    increaseQuantity() {
      if (!this.availableStock || this.quantity < this.availableStock) {
        this.quantity++;
      }
    },
    decreaseQuantity() {
      if (this.quantity > 1) {
        this.quantity--;
      }
    },
    handleAdded() {
      // Mostrar toast de éxito
      this.$emit('show-success', 'Producto añadido al carrito');
    },
    handleError(error) {
      // Mostrar toast de error
      this.$emit('show-error', error);
    },
    async buyNow() {
      const result = await this.cartStore.addItem(
        this.product.id,
        this.selectedVariant?.id,
        this.quantity
      );
      
      if (result.success) {
        window.location.href = '/checkout';
      }
    }
  }
}
</script>
```

## Tips de Implementación

1. **Siempre valida el stock** antes de mostrar el botón
2. **Usa eventos** para feedback personalizado
3. **Maneja errores** apropiadamente
4. **Muestra estados de loading** para mejor UX
5. **Considera variantes** si tu producto las tiene
6. **Sincroniza con el backend** automáticamente
7. **Usa el store** para acceder a datos del carrito en cualquier lugar
8. **Personaliza estilos** según tu diseño
9. **Añade animaciones** para mejor feedback visual
10. **Testea en móvil** para asegurar buena UX
