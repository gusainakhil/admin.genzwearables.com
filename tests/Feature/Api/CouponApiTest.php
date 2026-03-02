<?php

namespace Tests\Feature\Api;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Color;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CouponApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_apply_coupon_and_get_discount_preview(): void
    {
        $user = $this->createUserWithCart(1000);

        Coupon::query()->create([
            'code' => 'SAVE10',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'min_order_amount' => 500,
            'user_usage_limit' => 2,
            'expiry_date' => now()->addDays(7)->toDateString(),
            'status' => 'active',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/coupons/apply', [
            'code' => 'save10',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.code', 'SAVE10')
            ->assertJsonPath('data.discount_amount', 100)
            ->assertJsonPath('data.total_after_discount', 900);
    }

    public function test_coupon_apply_fails_when_user_limit_reached(): void
    {
        $user = $this->createUserWithCart(1000);

        $coupon = Coupon::query()->create([
            'code' => 'LIMIT1',
            'discount_type' => 'flat',
            'discount_value' => 100,
            'min_order_amount' => 200,
            'user_usage_limit' => 1,
            'expiry_date' => now()->addDays(7)->toDateString(),
            'status' => 'active',
        ]);

        Order::query()->create([
            'user_id' => $user->id,
            'order_number' => 'ORD-LIMIT-USED',
            'subtotal' => 1000,
            'shipping' => 0,
            'discount' => 100,
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->code,
            'total' => 900,
            'payment_status' => 'paid',
            'order_status' => 'placed',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/coupons/apply', [
            'code' => 'LIMIT1',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('status', false)
            ->assertJsonPath('message', 'Coupon usage limit reached');
    }

    public function test_checkout_applies_coupon_and_stores_coupon_fields_in_order(): void
    {
        $user = $this->createUserWithCart(1000);

        $coupon = Coupon::query()->create([
            'code' => 'SAVE10',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'min_order_amount' => 500,
            'user_usage_limit' => 3,
            'expiry_date' => now()->addDays(7)->toDateString(),
            'status' => 'active',
        ]);

        $address = UserAddress::query()->create([
            'user_id' => $user->id,
            'name' => 'Test User',
            'phone' => '9999999999',
            'address' => 'Street 1',
            'city' => 'Jaipur',
            'state' => 'Rajasthan',
            'pincode' => '302001',
            'country' => 'India',
            'is_default' => true,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/orders/checkout', [
            'address_id' => $address->id,
            'payment_method' => 'cod',
            'coupon_code' => 'save10',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.coupon_code', 'SAVE10')
            ->assertJsonPath('data.discount', '100.00');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'coupon_id' => $coupon->id,
            'coupon_code' => 'SAVE10',
        ]);
    }

    private function createUserWithCart(float $variantPrice): User
    {
        $user = User::factory()->create();

        $category = Category::query()->create([
            'name' => 'T-Shirts',
            'slug' => 't-shirts-'.uniqid(),
            'status' => 'active',
        ]);

        $size = Size::query()->create(['name' => 'M']);
        $color = Color::query()->create(['name' => 'Black', 'hex_code' => '#000000']);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Test Tee',
            'slug' => 'test-tee-'.uniqid(),
            'base_price' => $variantPrice,
            'status' => 'active',
        ]);

        $variant = ProductVariant::query()->create([
            'product_id' => $product->id,
            'size_id' => $size->id,
            'color_id' => $color->id,
            'sku' => 'SKU-'.uniqid(),
            'price' => $variantPrice,
            'stock_qty' => 20,
            'status' => 'active',
        ]);

        $cart = Cart::query()->create([
            'user_id' => $user->id,
        ]);

        CartItem::query()->create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        return $user;
    }
}
