<?php

namespace Tests\Unit;

use App\Enums\CouponType;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Services\Cart\CartCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private CartCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new CartCalculator();
    }

    /** @test */
    public function it_calculates_subtotal_correctly()
    {
        $cart = Cart::factory()->guest()->create();
        
        // Add items with known prices and quantities
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'price' => 100.00,
            'quantity' => 2,
        ]);
        
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'price' => 50.00,
            'quantity' => 3,
        ]);

        $cart->load('items');
        
        // Expected: (100 * 2) + (50 * 3) = 200 + 150 = 350
        $subtotal = $this->calculator->calculateSubtotal($cart);
        
        $this->assertEquals(350.00, $subtotal);
    }

    /** @test */
    public function it_calculates_subtotal_for_empty_cart()
    {
        $cart = Cart::factory()->guest()->create();
        $cart->load('items');
        
        $subtotal = $this->calculator->calculateSubtotal($cart);
        
        $this->assertEquals(0.00, $subtotal);
    }

    /** @test */
    public function it_calculates_fixed_discount_correctly()
    {
        $cart = Cart::factory()->guest()->create();
        
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'price' => 100.00,
            'quantity' => 2,
        ]);

        $cart->load('items');
        
        $coupon = Coupon::factory()->create([
            'type' => CouponType::Fixed,
            'value' => 50.00,
        ]);
        
        $discount = $this->calculator->calculateDiscount($cart, $coupon);
        
        $this->assertEquals(50.00, $discount);
    }

    /** @test */
    public function it_caps_fixed_discount_at_subtotal()
    {
        $cart = Cart::factory()->guest()->create();
        
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'price' => 30.00,
            'quantity' => 1,
        ]);

        $cart->load('items');
        
        // Coupon value exceeds subtotal
        $coupon = Coupon::factory()->create([
            'type' => CouponType::Fixed,
            'value' => 100.00,
        ]);
        
        $discount = $this->calculator->calculateDiscount($cart, $coupon);
        
        // Discount should be capped at subtotal (30)
        $this->assertEquals(30.00, $discount);
    }

    /** @test */
    public function it_calculates_percentage_discount_correctly()
    {
        $cart = Cart::factory()->guest()->create();
        
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'price' => 100.00,
            'quantity' => 2,
        ]);

        $cart->load('items');
        
        // 20% discount
        $coupon = Coupon::factory()->create([
            'type' => CouponType::Percentage,
            'value' => 20.00,
            'max_discount_amount' => null,
        ]);
        
        $discount = $this->calculator->calculateDiscount($cart, $coupon);
        
        // Expected: 200 * 0.20 = 40
        $this->assertEquals(40.00, $discount);
    }

    /** @test */
    public function it_applies_max_discount_amount_for_percentage_coupons()
    {
        $cart = Cart::factory()->guest()->create();
        
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'price' => 100.00,
            'quantity' => 5,
        ]);

        $cart->load('items');
        
        // 50% discount with max cap
        $coupon = Coupon::factory()->create([
            'type' => CouponType::Percentage,
            'value' => 50.00,
            'max_discount_amount' => 100.00,
        ]);
        
        $discount = $this->calculator->calculateDiscount($cart, $coupon);
        
        // Expected: 500 * 0.50 = 250, but capped at 100
        $this->assertEquals(100.00, $discount);
    }

    /** @test */
    public function it_calculates_tax_correctly()
    {
        config(['cart.tax_rate' => 0.10]); // 10% tax
        
        $subtotalAfterDiscount = 100.00;
        
        $tax = $this->calculator->calculateTax($subtotalAfterDiscount);
        
        // Expected: 100 * 0.10 = 10
        $this->assertEquals(10.00, $tax);
    }

    /** @test */
    public function it_calculates_shipping_from_config()
    {
        config(['cart.default_shipping_cost' => 15.00]);
        
        $cart = Cart::factory()->guest()->create();
        
        $shipping = $this->calculator->calculateShipping($cart);
        
        $this->assertEquals(15.00, $shipping);
    }

    /** @test */
    public function it_calculates_total_correctly()
    {
        config(['cart.tax_rate' => 0.10]);
        config(['cart.default_shipping_cost' => 10.00]);
        
        $cart = Cart::factory()->guest()->create([
            'discount_amount' => 20.00,
        ]);
        
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'price' => 100.00,
            'quantity' => 2,
        ]);

        $cart->load('items');
        
        $total = $this->calculator->calculateTotal($cart);
        
        // Subtotal: 200
        // Discount: 20
        // Subtotal after discount: 180
        // Tax (10%): 18
        // Shipping: 10
        // Total: 180 + 18 + 10 = 208
        $this->assertEquals(208.00, $total);
    }

    /** @test */
    public function it_gets_tax_rate_from_config()
    {
        config(['cart.tax_rate' => 0.16]);
        
        $taxRate = $this->calculator->getTaxRate();
        
        $this->assertEquals(0.16, $taxRate);
    }

    /** @test */
    public function it_handles_zero_discount_when_no_coupon_applied()
    {
        config(['cart.tax_rate' => 0.10]);
        config(['cart.default_shipping_cost' => 5.00]);
        
        $cart = Cart::factory()->guest()->create([
            'discount_amount' => 0,
        ]);
        
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'price' => 50.00,
            'quantity' => 2,
        ]);

        $cart->load('items');
        
        $total = $this->calculator->calculateTotal($cart);
        
        // Subtotal: 100
        // Discount: 0
        // Tax (10%): 10
        // Shipping: 5
        // Total: 100 + 10 + 5 = 115
        $this->assertEquals(115.00, $total);
    }
}
