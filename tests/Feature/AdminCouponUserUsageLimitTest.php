<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminCouponUserUsageLimitTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_create_coupon_with_user_usage_limit(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post('/admin/coupons', [
            'code' => 'LIMIT3',
            'discount_type' => 'flat',
            'discount_value' => 100,
            'min_order_amount' => 500,
            'user_usage_limit' => 3,
            'expiry_date' => now()->addDays(7)->toDateString(),
            'status' => 'active',
        ]);

        $response->assertRedirect('/admin/coupons');

        $this->assertDatabaseHas('coupons', [
            'code' => 'LIMIT3',
            'user_usage_limit' => 3,
        ]);
    }

    public function test_admin_can_update_coupon_user_usage_limit(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $coupon = Coupon::query()->create([
            'code' => 'LIMITEDIT',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'min_order_amount' => 300,
            'user_usage_limit' => 1,
            'expiry_date' => now()->addDays(10)->toDateString(),
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->put('/admin/coupons/'.$coupon->id, [
            'code' => 'LIMITEDIT',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'min_order_amount' => 300,
            'user_usage_limit' => 5,
            'expiry_date' => now()->addDays(10)->toDateString(),
            'status' => 'active',
        ]);

        $response->assertRedirect('/admin/coupons');

        $this->assertDatabaseHas('coupons', [
            'id' => $coupon->id,
            'user_usage_limit' => 5,
        ]);
    }
}
