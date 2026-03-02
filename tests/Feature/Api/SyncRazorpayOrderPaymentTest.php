<?php

namespace Tests\Feature\Api;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SyncRazorpayOrderPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_marks_order_as_paid_when_razorpay_payment_is_captured(): void
    {
        Mail::fake();

        $razorpayOrderId = 'order_SMM4Wm7v5slKPJ';
        [$user, $order] = $this->createPendingOrderWithPayment($razorpayOrderId);

        $this->setRazorpaySettings();

        Http::fake([
            'https://api.razorpay.com/v1/orders/'.$razorpayOrderId.'/payments' => Http::response([
                'entity' => 'collection',
                'count' => 1,
                'items' => [
                    [
                        'id' => 'pay_123',
                        'status' => 'captured',
                        'captured' => true,
                    ],
                ],
            ], 200),
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/orders/payment/sync', [
            'order_id' => $razorpayOrderId,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.payment_status', 'paid');

        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame('success', $order->payment()->first()?->status);
    }

    public function test_marks_order_as_failed_when_razorpay_payment_is_failed(): void
    {
        $razorpayOrderId = 'order_failed_123';
        [$user, $order] = $this->createPendingOrderWithPayment($razorpayOrderId);

        $this->setRazorpaySettings();

        Http::fake([
            'https://api.razorpay.com/v1/orders/'.$razorpayOrderId.'/payments' => Http::response([
                'entity' => 'collection',
                'count' => 1,
                'items' => [
                    [
                        'id' => 'pay_456',
                        'status' => 'failed',
                        'captured' => false,
                    ],
                ],
            ], 200),
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/orders/payment/sync', [
            'order_id' => $razorpayOrderId,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.payment_status', 'failed');

        $this->assertSame('failed', $order->fresh()->payment_status);
        $this->assertSame('failed', $order->payment()->first()?->status);
    }

    public function test_keeps_order_pending_when_razorpay_payment_is_still_authorized(): void
    {
        $razorpayOrderId = 'order_pending_123';
        [$user, $order] = $this->createPendingOrderWithPayment($razorpayOrderId);

        $this->setRazorpaySettings();

        Http::fake([
            'https://api.razorpay.com/v1/orders/'.$razorpayOrderId.'/payments' => Http::response([
                'entity' => 'collection',
                'count' => 1,
                'items' => [
                    [
                        'id' => 'pay_789',
                        'status' => 'authorized',
                        'captured' => false,
                    ],
                ],
            ], 200),
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/orders/payment/sync', [
            'order_id' => $razorpayOrderId,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.payment_status', 'pending')
            ->assertJsonPath('message', 'Payment still pending');

        $this->assertSame('pending', $order->fresh()->payment_status);
        $this->assertSame('pending', $order->payment()->first()?->status);
    }

    private function createPendingOrderWithPayment(string $razorpayOrderId): array
    {
        $user = User::factory()->create();

        $order = Order::query()->create([
            'user_id' => $user->id,
            'order_number' => 'ORD-TEST-'.strtoupper(fake()->bothify('??##??##')),
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
            'transaction_id' => $razorpayOrderId,
            'amount' => 1000,
            'status' => 'pending',
        ]);

        return [$user, $order];
    }

    private function setRazorpaySettings(): void
    {
        Setting::set('razorpay_enabled', '1');
        Setting::set('razorpay_key_id', 'rzp_test_key');
        Setting::set('razorpay_key_secret', 'rzp_test_secret');
    }
}
