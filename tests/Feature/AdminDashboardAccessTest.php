<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminDashboardAccessTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'auth', 'admin:admin,staff'])
            ->get('/__admin-access-test', function () {
                return response('ok', 200);
            });
    }

    public function test_admin_user_can_access_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get('/__admin-access-test');

        $response->assertOk();
    }

    public function test_staff_user_can_access_dashboard(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'status' => 'active',
        ]);

        $response = $this->actingAs($staff)->get('/__admin-access-test');

        $response->assertOk();
    }

    public function test_customer_user_cannot_access_dashboard(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
        ]);

        $response = $this->actingAs($customer)->get('/__admin-access-test');

        $response->assertRedirect('/login');
    }

    public function test_staff_user_login_redirects_to_dashboard(): void
    {
        User::factory()->create([
            'email' => 'staff@example.com',
            'password' => 'password',
            'role' => 'staff',
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'email' => 'staff@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin');
    }
}
