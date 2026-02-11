/**
 * Checkout Type Definitions
 * JSDoc type definitions for checkout system
 */

/**
 * @typedef {Object} Product
 * @property {number} id
 * @property {string} name
 * @property {string} slug
 * @property {string} [image]
 * @property {number} price
 * @property {string} [description]
 */

/**
 * @typedef {Object} CartItem
 * @property {string} uuid
 * @property {Product} product
 * @property {number} quantity
 * @property {number} price
 * @property {number} subtotal
 * @property {Object} [variant]
 */

/**
 * @typedef {Object} Coupon
 * @property {string} code
 * @property {number} discount
 * @property {'percentage'|'fixed'} type
 */

/**
 * @typedef {Object} Cart
 * @property {CartItem[]} items
 * @property {number} subtotal
 * @property {number} discount
 * @property {number} total
 * @property {Coupon} [coupon]
 */

/**
 * @typedef {Object} CartSummary
 * @property {number} item_count
 * @property {number} subtotal
 * @property {number} discount
 * @property {number} tax
 * @property {number} shipping_cost
 * @property {number} total
 */

/**
 * @typedef {Object} Address
 * @property {string} fullName
 * @property {string} addressLine1
 * @property {string} [addressLine2]
 * @property {string} city
 * @property {string} state
 * @property {string} postalCode
 * @property {string} country
 */

/**
 * @typedef {Object} ShippingMethod
 * @property {string} id
 * @property {string} name
 * @property {string} description
 * @property {string} estimatedDays
 * @property {number} cost
 */

/**
 * @typedef {Object} PaymentMethod
 * @property {string} id
 * @property {string} name
 * @property {string} description
 * @property {string} [icon]
 */

/**
 * @typedef {Object} OrderItem
 * @property {number} id
 * @property {Product} product
 * @property {number} quantity
 * @property {number} price
 * @property {number} subtotal
 */

/**
 * @typedef {Object} Order
 * @property {number} id
 * @property {string} orderNumber
 * @property {string} status
 * @property {OrderItem[]} items
 * @property {Address} shippingAddress
 * @property {Address} billingAddress
 * @property {ShippingMethod} shippingMethod
 * @property {PaymentMethod} paymentMethod
 * @property {number} subtotal
 * @property {number} shippingCost
 * @property {number} discount
 * @property {number} total
 * @property {string} [notes]
 * @property {string} createdAt
 */

/**
 * @typedef {Object} CheckoutPayload
 * @property {Object} shipping_address
 * @property {string} shipping_address.full_name
 * @property {string} shipping_address.address_line_1
 * @property {string} [shipping_address.address_line_2]
 * @property {string} shipping_address.city
 * @property {string} shipping_address.state
 * @property {string} shipping_address.postal_code
 * @property {string} shipping_address.country
 * @property {Object} billing_address
 * @property {string} billing_address.full_name
 * @property {string} billing_address.address_line_1
 * @property {string} [billing_address.address_line_2]
 * @property {string} billing_address.city
 * @property {string} billing_address.state
 * @property {string} billing_address.postal_code
 * @property {string} billing_address.country
 * @property {string} shipping_method
 * @property {string} payment_method
 * @property {string} [notes]
 */

export {};
