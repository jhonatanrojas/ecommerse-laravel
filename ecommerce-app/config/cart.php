<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Guest Cart Expiration Days
    |--------------------------------------------------------------------------
    |
    | This value determines how many days a guest cart will remain active
    | before it expires. Guest carts are associated with session_id and
    | will be automatically cleaned up after this period.
    |
    */

    'guest_cart_expiration_days' => env('CART_GUEST_EXPIRATION_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Maximum Item Quantity
    |--------------------------------------------------------------------------
    |
    | This value sets the maximum quantity allowed for a single item in the
    | cart. This prevents users from adding excessive quantities and helps
    | manage inventory.
    |
    */

    'max_item_quantity' => env('CART_MAX_ITEM_QUANTITY', 99),

    /*
    |--------------------------------------------------------------------------
    | Tax Rate
    |--------------------------------------------------------------------------
    |
    | This value represents the tax rate applied to cart totals. The value
    | should be a decimal (e.g., 0.10 for 10% tax). Tax is calculated on
    | the subtotal after discount.
    |
    */

    'tax_rate' => env('CART_TAX_RATE', 0.10),

    /*
    |--------------------------------------------------------------------------
    | Default Shipping Cost
    |--------------------------------------------------------------------------
    |
    | This value sets the default shipping cost applied to all orders.
    | This can be overridden by shipping method calculations in the future.
    |
    */

    'default_shipping_cost' => env('CART_DEFAULT_SHIPPING_COST', 10.00),

    /*
    |--------------------------------------------------------------------------
    | Email Notifications
    |--------------------------------------------------------------------------
    |
    | These settings control whether email notifications are sent for various
    | cart events. By default, all email notifications are disabled and must
    | be explicitly enabled by the administrator.
    |
    */

    'emails' => [
        /*
        | Send order confirmation email after successful checkout
        */
        'order_confirmation_enabled' => env('CART_EMAIL_ORDER_CONFIRMATION_ENABLED', false),

        /*
        | Send cart abandonment reminder email after specified delay
        */
        'cart_abandonment_enabled' => env('CART_EMAIL_CART_ABANDONMENT_ENABLED', false),

        /*
        | Delay in hours before sending cart abandonment email
        */
        'cart_abandonment_delay_hours' => env('CART_EMAIL_ABANDONMENT_DELAY_HOURS', 24),
    ],

];
