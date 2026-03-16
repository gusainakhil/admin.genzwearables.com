<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ReturnRequest;
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
            ->assertSee('Size issue');
    }
}
