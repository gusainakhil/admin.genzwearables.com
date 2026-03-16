<?php

namespace Tests\Feature\Api;

use App\Mail\OrderPaid;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateOrderPaymentMailGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_skips_order_paid_email_when_user_email_is_empty(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $user->forceFill(['email' => ''])->save();

        $order = Order::query()->create([
            'user_id' => $user->id,
            'order_number' => 'ORD-MAIL-GUARD-'.strtoupper(fake()->bothify('??##??##')),
            'subtotal' => 1000,
            'shipping' => 0,
            'discount' => 0,
            'total' => 1000,
            'payment_status' => 'pending',
            'order_status' => 'placed',
        ]);

        Sanctum::actingAs($user);

        $this->patchJson('/api/orders/'.$order->id.'/payment', [
            'payment_status' => 'paid',
            'payment_method' => 'razorpay',
            'transaction_id' => 'txn_mail_guard_123',
        ])
            ->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.payment_status', 'paid');

        $this->assertSame('paid', $order->fresh()->payment_status);
        Mail::assertNotSent(OrderPaid::class);
    }

    public function test_cart_is_cleared_when_payment_is_confirmed(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $cart = Cart::query()->create(['user_id' => $user->id]);

        CartItem::query()->create([
            'cart_id' => $cart->id,
            'product_variant_id' => ProductVariant::query()->inRandomOrder()->first()?->id ?? 1,
            'quantity' => 1,
        ]);

        $order = Order::query()->create([
            'user_id' => $user->id,
            'order_number' => 'ORD-CART-CLEAR-'.strtoupper(fake()->bothify('??##??##')),
            'subtotal' => 1000,
            'shipping' => 0,
            'discount' => 0,
            'total' => 1000,
            'payment_status' => 'pending',
            'order_status' => 'placed',
        ]);

        Sanctum::actingAs($user);

        $this->patchJson('/api/orders/'.$order->id.'/payment', [
            'payment_status' => 'paid',
            'payment_method' => 'razorpay',
            'transaction_id' => 'txn_cart_clear_456',
        ])
            ->assertOk()
            ->assertJsonPath('data.payment_status', 'paid');

        $this->assertSame(0, $cart->fresh()->items()->count());
    }

    public function test_cart_is_not_cleared_when_payment_fails(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $cart = Cart::query()->create(['user_id' => $user->id]);

        CartItem::query()->create([
            'cart_id' => $cart->id,
            'product_variant_id' => ProductVariant::query()->inRandomOrder()->first()?->id ?? 1,
            'quantity' => 1,
        ]);

        $order = Order::query()->create([
            'user_id' => $user->id,
            'order_number' => 'ORD-CART-FAIL-'.strtoupper(fake()->bothify('??##??##')),
            'subtotal' => 1000,
            'shipping' => 0,
            'discount' => 0,
            'total' => 1000,
            'payment_status' => 'pending',
            'order_status' => 'placed',
        ]);

        Sanctum::actingAs($user);

        $this->patchJson('/api/orders/'.$order->id.'/payment', [
            'payment_status' => 'failed',
            'payment_method' => 'razorpay',
            'transaction_id' => 'txn_cart_fail_789',
        ])
            ->assertOk()
            ->assertJsonPath('data.payment_status', 'failed');

        $this->assertSame(1, $cart->fresh()->items()->count());
    }
}
