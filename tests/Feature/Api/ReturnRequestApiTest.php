<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('allows user to submit return request with multiple images', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $category = Category::query()->create([
        'name' => 'Jackets',
        'slug' => 'jackets',
        'status' => 'active',
    ]);

    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Winter Jacket',
        'slug' => 'winter-jacket-'.uniqid(),
        'base_price' => 1999,
        'status' => 'active',
    ]);

    $size = Size::query()->create([
        'name' => 'M',
    ]);

    $variant = ProductVariant::query()->create([
        'product_id' => $product->id,
        'size_id' => $size->id,
        'sku' => 'JACKET-M-'.uniqid(),
        'price' => 1999,
        'stock_qty' => 5,
        'status' => 'active',
    ]);

    $order = Order::query()->create([
        'user_id' => $user->id,
        'order_number' => 'ORD-RET-API-1001',
        'subtotal' => 1999,
        'shipping' => 0,
        'discount' => 0,
        'total' => 1999,
        'payment_status' => 'paid',
        'order_status' => 'delivered',
    ]);

    $orderItem = OrderItem::query()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_variant_id' => $variant->id,
        'price' => 1999,
        'quantity' => 1,
    ]);

    Sanctum::actingAs($user);

    $response = $this->post('/api/returns', [
        'order_item_id' => $orderItem->id,
        'request_type' => 'return',
        'product_variant_id' => $variant->id,
        'reason' => 'Received damaged product',
        'product_images' => [
            UploadedFile::fake()->image('damage-1.jpg'),
            UploadedFile::fake()->image('damage-2.jpg'),
        ],
    ], [
        'Accept' => 'application/json',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.order_item_id', $orderItem->id)
        ->assertJsonPath('data.request_type', 'return')
        ->assertJsonPath('data.requested_by', null)
        ->assertJsonPath('data.product_variant_id', $variant->id)
        ->assertJsonPath('data.tracking_id', null)
        ->assertJsonPath('data.courier_name', null)
        ->assertJsonPath('data.link', null)
        ->assertJsonPath('data.status', 'requested');

    expect($response->json('data.product_images'))->toHaveCount(2);

    foreach (($response->json('data.product_images') ?? []) as $imagePath) {
        Storage::disk('public')->assertExists($imagePath);
    }
});

it('rejects return submission when order item does not belong to user', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();

    $category = Category::query()->create([
        'name' => 'Shoes',
        'slug' => 'shoes-'.uniqid(),
        'status' => 'active',
    ]);

    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Runner Shoe',
        'slug' => 'runner-shoe-'.uniqid(),
        'base_price' => 1499,
        'status' => 'active',
    ]);

    $size = Size::query()->create(['name' => 'L']);

    $variant = ProductVariant::query()->create([
        'product_id' => $product->id,
        'size_id' => $size->id,
        'sku' => 'RUNNER-L-'.uniqid(),
        'price' => 1499,
        'stock_qty' => 10,
        'status' => 'active',
    ]);

    $order = Order::query()->create([
        'user_id' => $owner->id,
        'order_number' => 'ORD-RET-API-1002',
        'subtotal' => 1499,
        'shipping' => 0,
        'discount' => 0,
        'total' => 1499,
        'payment_status' => 'paid',
        'order_status' => 'delivered',
    ]);

    $orderItem = OrderItem::query()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_variant_id' => $variant->id,
        'price' => 1499,
        'quantity' => 1,
    ]);

    Sanctum::actingAs($otherUser);

    $response = $this->postJson('/api/returns', [
        'order_item_id' => $orderItem->id,
        'request_type' => 'replacement',
        'product_variant_id' => $variant->id,
        'reason' => 'Wrong size sent',
    ]);

    $response
        ->assertForbidden()
        ->assertJsonPath('status', false);
});
