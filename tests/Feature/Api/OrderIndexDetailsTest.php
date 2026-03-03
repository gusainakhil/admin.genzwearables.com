<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Color;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\Size;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderIndexDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_show_auto_syncs_pending_razorpay_payment_within_30_minutes(): void
    {
        $this->setRazorpaySettings();

        $user = User::factory()->create();
        $order = Order::query()->create([
            'user_id' => $user->id,
            'order_number' => 'ORD-SHOW-SYNC-1001',
            'subtotal' => 1000,
            'shipping' => 0,
            'discount' => 0,
            'total' => 1000,
            'payment_status' => 'pending',
            'order_status' => 'placed',
        ]);

        Payment::query()->create([
            'order_id' => $order->id,
            'payment_method' => 'razorpay',
            'transaction_id' => 'order_auto_sync_123',
            'amount' => 1000,
            'status' => 'pending',
        ]);

        Http::fake([
            'https://api.razorpay.com/v1/orders/order_auto_sync_123/payments' => Http::response([
                'entity' => 'collection',
                'count' => 1,
                'items' => [
                    [
                        'id' => 'pay_auto_sync_123',
                        'status' => 'captured',
                        'captured' => true,
                    ],
                ],
            ], 200),
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/orders/'.$order->id)
            ->assertOk()
            ->assertJsonPath('data.payment_status', 'paid');

        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame('success', $order->payment()->first()?->status);
    }

    public function test_order_show_cancels_stale_pending_order_after_30_minutes_without_hitting_razorpay(): void
    {
        $this->setRazorpaySettings();

        $user = User::factory()->create();
        $order = Order::query()->create([
            'user_id' => $user->id,
            'order_number' => 'ORD-SHOW-CANCEL-1002',
            'subtotal' => 900,
            'shipping' => 0,
            'discount' => 0,
            'total' => 900,
            'payment_status' => 'pending',
            'order_status' => 'placed',
        ]);

        $order->forceFill([
            'created_at' => now()->subMinutes(31),
            'updated_at' => now()->subMinutes(31),
        ])->save();

        Payment::query()->create([
            'order_id' => $order->id,
            'payment_method' => 'razorpay',
            'transaction_id' => 'order_stale_pending_123',
            'amount' => 900,
            'status' => 'pending',
        ]);

        Http::fake();

        Sanctum::actingAs($user);

        $this->getJson('/api/orders/'.$order->id)
            ->assertOk()
            ->assertJsonPath('data.order_status', 'cancelled')
            ->assertJsonPath('data.payment_status', 'failed');

        Http::assertNothingSent();
        $this->assertSame('cancelled', $order->fresh()->order_status);
        $this->assertSame('failed', $order->fresh()->payment_status);
        $this->assertSame('failed', $order->payment()->first()?->status);
    }

    public function test_order_show_does_not_hit_razorpay_for_already_paid_order(): void
    {
        $this->setRazorpaySettings();

        $user = User::factory()->create();
        $order = Order::query()->create([
            'user_id' => $user->id,
            'order_number' => 'ORD-SHOW-PAID-1003',
            'subtotal' => 1200,
            'shipping' => 0,
            'discount' => 0,
            'total' => 1200,
            'payment_status' => 'paid',
            'order_status' => 'placed',
        ]);

        Payment::query()->create([
            'order_id' => $order->id,
            'payment_method' => 'razorpay',
            'transaction_id' => 'order_paid_skip_sync_123',
            'amount' => 1200,
            'status' => 'success',
        ]);

        Http::fake();

        Sanctum::actingAs($user);

        $this->getJson('/api/orders/'.$order->id)
            ->assertOk()
            ->assertJsonPath('data.payment_status', 'paid');

        Http::assertNothingSent();
    }

    public function test_orders_index_returns_product_price_and_image_details(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $category = Category::query()->create([
            'name' => 'T-Shirts',
            'slug' => 't-shirts',
            'status' => 'active',
        ]);

        $size = Size::query()->create(['name' => 'L']);
        $color = Color::query()->create(['name' => 'Black', 'hex_code' => '#000000']);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Oversized Tee',
            'slug' => 'oversized-tee',
            'brand' => 'Genz',
            'base_price' => 999,
            'status' => 'active',
        ]);

        ProductImage::query()->create([
            'product_id' => $product->id,
            'image' => 'products/oversized-tee-main.jpg',
            'is_primary' => true,
        ]);

        $variant = ProductVariant::query()->create([
            'product_id' => $product->id,
            'size_id' => $size->id,
            'color_id' => $color->id,
            'sku' => 'TEE-L-BLK',
            'price' => 999,
            'stock_qty' => 10,
            'status' => 'active',
        ]);

        $order = Order::query()->create([
            'user_id' => $user->id,
            'order_number' => 'ORD-DETAIL-1001',
            'subtotal' => 999,
            'shipping' => 0,
            'discount' => 0,
            'total' => 999,
            'payment_status' => 'pending',
            'order_status' => 'placed',
        ]);

        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'price' => 999,
            'quantity' => 1,
        ]);

        Order::query()->create([
            'user_id' => $otherUser->id,
            'order_number' => 'ORD-OTHER-1002',
            'subtotal' => 500,
            'shipping' => 0,
            'discount' => 0,
            'total' => 500,
            'payment_status' => 'pending',
            'order_status' => 'placed',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/orders?per_page=10');

        $response
            ->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.order_number', 'ORD-DETAIL-1001')
            ->assertJsonPath('data.data.0.items.0.price', '999.00')
            ->assertJsonPath('data.data.0.items.0.product.name', 'Oversized Tee')
            ->assertJsonPath('data.data.0.items.0.product.image.path', 'products/oversized-tee-main.jpg');

        $imageUrl = $response->json('data.data.0.items.0.product.image.url');
        $this->assertStringContainsString('/storage/products/oversized-tee-main.jpg', (string) $imageUrl);
    }

    private function setRazorpaySettings(): void
    {
        Setting::set('razorpay_enabled', '1');
        Setting::set('razorpay_key_id', 'rzp_test_key');
        Setting::set('razorpay_key_secret', 'rzp_test_secret');
    }
}
