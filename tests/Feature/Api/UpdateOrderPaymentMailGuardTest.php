<?php

namespace Tests\Feature\Api;

use App\Mail\OrderPaid;
use App\Models\Order;
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
        $user->forceFill([
            'email' => '',
        ])->save();

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

        $response = $this->patchJson('/api/orders/'.$order->id.'/payment', [
            'payment_status' => 'paid',
            'payment_method' => 'razorpay',
            'transaction_id' => 'txn_mail_guard_123',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.payment_status', 'paid');

        $this->assertSame('paid', $order->fresh()->payment_status);
        Mail::assertNotSent(OrderPaid::class);
    }
}
