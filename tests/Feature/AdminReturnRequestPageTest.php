<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ReturnRequest;
use App\Models\Size;
use App\Models\UserAddress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReturnRequestPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_return_and_refund_requests_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
        ]);

        $category = Category::query()->create([
            'name' => 'Shoes',
            'slug' => 'shoes',
            'status' => 'active',
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Runner Shoe',
            'slug' => 'runner-shoe',
            'base_price' => 2499,
            'status' => 'active',
        ]);

        $order = Order::query()->create([
            'user_id' => $customer->id,
            'order_number' => 'ORD-RET-1001',
            'subtotal' => 2499,
            'shipping' => 0,
            'discount' => 0,
            'total' => 2499,
            'payment_status' => 'paid',
            'order_status' => 'delivered',
        ]);

        $orderItem = OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'price' => 2499,
            'quantity' => 1,
        ]);

        ReturnRequest::query()->create([
            'order_item_id' => $orderItem->id,
            'reason' => 'Size issue',
            'status' => 'requested',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.return-requests.index'));

        $response
            ->assertOk()
            ->assertSee('Return &amp; Refund Requests')
            ->assertSee('ORD-RET-1001')
            ->assertSee('Runner Shoe')
            ->assertSee('View')
            ->assertDontSee('Size issue');
    }

    public function test_admin_can_view_full_return_request_details_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
            'phone' => '9999999999',
        ]);

        $address = UserAddress::query()->create([
            'user_id' => $customer->id,
            'name' => 'Rahul Sharma',
            'phone' => '9999999999',
            'address' => '12 Green Park',
            'city' => 'Jaipur',
            'state' => 'Rajasthan',
            'pincode' => '302001',
            'country' => 'India',
            'is_default' => true,
        ]);

        $category = Category::query()->create([
            'name' => 'Shoes',
            'slug' => 'shoes-detail',
            'status' => 'active',
        ]);

        $size = Size::query()->create(['name' => 'M']);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Runner Shoe Pro',
            'slug' => 'runner-shoe-pro',
            'base_price' => 2999,
            'status' => 'active',
        ]);

        $variant = ProductVariant::query()->create([
            'product_id' => $product->id,
            'size_id' => $size->id,
            'sku' => 'RUN-PRO-M',
            'price' => 2999,
            'stock_qty' => 10,
            'status' => 'active',
        ]);

        $order = Order::query()->create([
            'user_id' => $customer->id,
            'address_id' => $address->id,
            'order_number' => 'ORD-RET-1002',
            'subtotal' => 2999,
            'shipping' => 0,
            'discount' => 0,
            'total' => 2999,
            'payment_status' => 'paid',
            'order_status' => 'delivered',
        ]);

        $orderItem = OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'price' => 2999,
            'quantity' => 1,
        ]);

        $returnRequest = ReturnRequest::query()->create([
            'order_item_id' => $orderItem->id,
            'request_type' => 'replacement',
            'requested_by' => 'self',
            'product_variant_id' => $variant->id,
            'tracking_id' => 'TRACK-ADMIN-1002',
            'courier_name' => 'Delhivery',
            'link' => 'https://tracking.example.com/TRACK-ADMIN-1002',
            'product_images' => ['returns/test-image-1.jpg', 'returns/test-image-2.jpg'],
            'reason' => 'Damaged sole',
            'status' => 'requested',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.return-requests.show', $returnRequest));

        $response
            ->assertOk()
            ->assertSee('Return Request Details')
            ->assertSee('ORD-RET-1002')
            ->assertSee('Runner Shoe Pro')
            ->assertSee('Rahul Sharma')
            ->assertSee('12 Green Park')
            ->assertSee('Damaged sole')
            ->assertSee('Self')
            ->assertSee('TRACK-ADMIN-1002')
            ->assertSee('Delhivery')
            ->assertSee('https://tracking.example.com/TRACK-ADMIN-1002')
            ->assertSee('returns/test-image-1.jpg')
            ->assertSee('returns/test-image-2.jpg');
    }

    public function test_admin_can_update_return_request_with_self_and_tracking_id(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
        ]);

        $category = Category::query()->create([
            'name' => 'Bags',
            'slug' => 'bags',
            'status' => 'active',
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Travel Bag',
            'slug' => 'travel-bag',
            'base_price' => 1800,
            'status' => 'active',
        ]);

        $order = Order::query()->create([
            'user_id' => $customer->id,
            'order_number' => 'ORD-RET-1003',
            'subtotal' => 1800,
            'shipping' => 0,
            'discount' => 0,
            'total' => 1800,
            'payment_status' => 'paid',
            'order_status' => 'delivered',
        ]);

        $orderItem = OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'price' => 1800,
            'quantity' => 1,
        ]);

        $returnRequest = ReturnRequest::query()->create([
            'order_item_id' => $orderItem->id,
            'request_type' => 'return',
            'requested_by' => null,
            'tracking_id' => null,
            'reason' => 'Zip issue',
            'status' => 'requested',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.return-requests.update', $returnRequest), [
            'status' => 'approved',
            'requested_by' => 'self',
            'tracking_id' => 'SELF-TRACK-1003',
            'courier_name' => 'Blue Dart',
            'link' => 'https://tracking.example.com/SELF-TRACK-1003',
        ]);

        $response->assertRedirect(route('admin.return-requests.show', $returnRequest));

        $this->assertDatabaseHas('returns', [
            'id' => $returnRequest->id,
            'status' => 'approved',
            'requested_by' => 'self',
            'tracking_id' => 'SELF-TRACK-1003',
            'courier_name' => 'Blue Dart',
            'link' => 'https://tracking.example.com/SELF-TRACK-1003',
        ]);
    }
}
