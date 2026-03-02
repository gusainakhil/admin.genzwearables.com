<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Color;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderIndexDetailsTest extends TestCase
{
    use RefreshDatabase;

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
}
