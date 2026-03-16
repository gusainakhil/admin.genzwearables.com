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

it('returns only authenticated users return requests', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $category = Category::query()->create([
        'name' => 'Tees',
        'slug' => 'tees-'.uniqid(),
        'status' => 'active',
    ]);

    $size = Size::query()->create(['name' => 'XL']);

    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Basic Tee',
        'slug' => 'basic-tee-'.uniqid(),
        'base_price' => 899,
        'status' => 'active',
    ]);

    $variant = ProductVariant::query()->create([
        'product_id' => $product->id,
        'size_id' => $size->id,
        'sku' => 'TEE-XL-'.uniqid(),
        'price' => 899,
        'stock_qty' => 20,
        'status' => 'active',
    ]);

    $userOrder = Order::query()->create([
        'user_id' => $user->id,
        'order_number' => 'ORD-RET-LIST-1001',
        'subtotal' => 899,
        'shipping' => 0,
        'discount' => 0,
        'total' => 899,
        'payment_status' => 'paid',
        'order_status' => 'delivered',
    ]);

    $otherOrder = Order::query()->create([
        'user_id' => $otherUser->id,
        'order_number' => 'ORD-RET-LIST-1002',
        'subtotal' => 899,
        'shipping' => 0,
        'discount' => 0,
        'total' => 899,
        'payment_status' => 'paid',
        'order_status' => 'delivered',
    ]);

    $userOrderItem = OrderItem::query()->create([
        'order_id' => $userOrder->id,
        'product_id' => $product->id,
        'product_variant_id' => $variant->id,
        'price' => 899,
        'quantity' => 1,
    ]);

    $otherOrderItem = OrderItem::query()->create([
        'order_id' => $otherOrder->id,
        'product_id' => $product->id,
        'product_variant_id' => $variant->id,
        'price' => 899,
        'quantity' => 1,
    ]);

    $ownRequest = \App\Models\ReturnRequest::query()->create([
        'order_item_id' => $userOrderItem->id,
        'request_type' => 'return',
        'product_variant_id' => $variant->id,
        'reason' => 'Thread issue',
        'status' => 'requested',
    ]);

    \App\Models\ReturnRequest::query()->create([
        'order_item_id' => $otherOrderItem->id,
        'request_type' => 'replacement',
        'product_variant_id' => $variant->id,
        'reason' => 'Wrong size',
        'status' => 'requested',
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/returns?per_page=10');

    $response
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonCount(1, 'data.data')
        ->assertJsonPath('data.data.0.id', $ownRequest->id)
        ->assertJsonPath('data.data.0.order_number', 'ORD-RET-LIST-1001')
        ->assertJsonPath('data.data.0.product.name', 'Basic Tee')
        ->assertJsonPath('data.data.0.variant.sku', $variant->sku);
});

it('returns return request details for authenticated owner', function () {
    $user = User::factory()->create();

    $category = Category::query()->create([
        'name' => 'Shirts',
        'slug' => 'shirts-'.uniqid(),
        'status' => 'active',
    ]);

    $size = Size::query()->create(['name' => 'M']);

    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Office Shirt',
        'slug' => 'office-shirt-'.uniqid(),
        'base_price' => 1299,
        'status' => 'active',
    ]);

    $variant = ProductVariant::query()->create([
        'product_id' => $product->id,
        'size_id' => $size->id,
        'sku' => 'SHIRT-M-'.uniqid(),
        'price' => 1299,
        'stock_qty' => 15,
        'status' => 'active',
    ]);

    $order = Order::query()->create([
        'user_id' => $user->id,
        'order_number' => 'ORD-RET-DETAIL-1001',
        'subtotal' => 1299,
        'shipping' => 0,
        'discount' => 0,
        'total' => 1299,
        'payment_status' => 'paid',
        'order_status' => 'delivered',
    ]);

    $orderItem = OrderItem::query()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_variant_id' => $variant->id,
        'price' => 1299,
        'quantity' => 1,
    ]);

    $returnRequest = \App\Models\ReturnRequest::query()->create([
        'order_item_id' => $orderItem->id,
        'request_type' => 'replacement',
        'product_variant_id' => $variant->id,
        'tracking_id' => 'RET-DETAIL-1001',
        'courier_name' => 'Blue Dart',
        'link' => 'https://tracking.example.com/RET-DETAIL-1001',
        'reason' => 'Button issue',
        'status' => 'approved',
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/returns/'.$returnRequest->id);

    $response
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.id', $returnRequest->id)
        ->assertJsonPath('data.order_number', 'ORD-RET-DETAIL-1001')
        ->assertJsonPath('data.product.name', 'Office Shirt')
        ->assertJsonPath('data.variant.sku', $variant->sku)
        ->assertJsonPath('data.courier_name', 'Blue Dart')
        ->assertJsonPath('data.link', 'https://tracking.example.com/RET-DETAIL-1001');
});

it('does not allow user to view another users return request details', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();

    $category = Category::query()->create([
        'name' => 'Denim',
        'slug' => 'denim-'.uniqid(),
        'status' => 'active',
    ]);

    $size = Size::query()->create(['name' => 'L']);

    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Denim Jacket',
        'slug' => 'denim-jacket-'.uniqid(),
        'base_price' => 1999,
        'status' => 'active',
    ]);

    $variant = ProductVariant::query()->create([
        'product_id' => $product->id,
        'size_id' => $size->id,
        'sku' => 'DENIM-L-'.uniqid(),
        'price' => 1999,
        'stock_qty' => 9,
        'status' => 'active',
    ]);

    $order = Order::query()->create([
        'user_id' => $owner->id,
        'order_number' => 'ORD-RET-DETAIL-1002',
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

    $returnRequest = \App\Models\ReturnRequest::query()->create([
        'order_item_id' => $orderItem->id,
        'request_type' => 'return',
        'product_variant_id' => $variant->id,
        'reason' => 'Color mismatch',
        'status' => 'requested',
    ]);

    Sanctum::actingAs($otherUser);

    $response = $this->getJson('/api/returns/'.$returnRequest->id);

    $response
        ->assertNotFound()
        ->assertJsonPath('status', false)
        ->assertJsonPath('message', 'Return request not found');
});

it('returns return requests for an authenticated users order id', function () {
    $user = User::factory()->create();

    $category = Category::query()->create([
        'name' => 'Hoodies',
        'slug' => 'hoodies-'.uniqid(),
        'status' => 'active',
    ]);

    $size = Size::query()->create(['name' => 'S']);

    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Zip Hoodie',
        'slug' => 'zip-hoodie-'.uniqid(),
        'base_price' => 1599,
        'status' => 'active',
    ]);

    $variant = ProductVariant::query()->create([
        'product_id' => $product->id,
        'size_id' => $size->id,
        'sku' => 'HOODIE-S-'.uniqid(),
        'price' => 1599,
        'stock_qty' => 7,
        'status' => 'active',
    ]);

    $order = Order::query()->create([
        'user_id' => $user->id,
        'order_number' => 'ORD-RET-ORDER-1001',
        'subtotal' => 1599,
        'shipping' => 0,
        'discount' => 0,
        'total' => 1599,
        'payment_status' => 'paid',
        'order_status' => 'delivered',
    ]);

    $orderItem = OrderItem::query()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_variant_id' => $variant->id,
        'price' => 1599,
        'quantity' => 1,
    ]);

    $returnRequest = \App\Models\ReturnRequest::query()->create([
        'order_item_id' => $orderItem->id,
        'request_type' => 'return',
        'product_variant_id' => $variant->id,
        'reason' => 'Fabric issue',
        'status' => 'requested',
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/orders/'.$order->id.'/returns');

    $response
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.order_id', $order->id)
        ->assertJsonPath('data.order_number', 'ORD-RET-ORDER-1001')
        ->assertJsonCount(1, 'data.returns')
        ->assertJsonPath('data.returns.0.id', $returnRequest->id)
        ->assertJsonPath('data.returns.0.product.name', 'Zip Hoodie');
});

it('does not allow user to fetch return requests for another users order id', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();

    $order = Order::query()->create([
        'user_id' => $owner->id,
        'order_number' => 'ORD-RET-ORDER-1002',
        'subtotal' => 1000,
        'shipping' => 0,
        'discount' => 0,
        'total' => 1000,
        'payment_status' => 'paid',
        'order_status' => 'delivered',
    ]);

    Sanctum::actingAs($otherUser);

    $response = $this->getJson('/api/orders/'.$order->id.'/returns');

    $response
        ->assertNotFound()
        ->assertJsonPath('status', false)
        ->assertJsonPath('message', 'Order not found');
});
