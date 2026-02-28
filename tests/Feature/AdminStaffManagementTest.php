<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminStaffManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'auth', 'admin:admin'])
            ->get('/__admin-only-staff-management-test', function () {
                return response('ok', 200);
            });
    }

    public function test_admin_can_access_staff_index(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get('/__admin-only-staff-management-test');

        $response->assertOk();
    }

    public function test_staff_user_cannot_access_staff_management(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'status' => 'active',
        ]);

        $response = $this->actingAs($staff)->get('/__admin-only-staff-management-test');

        $response->assertRedirect('/login');
    }

    public function test_admin_can_create_staff_user(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post('/admin/staff', [
            'name' => 'Staff User',
            'email' => 'newstaff@example.com',
            'phone' => '9999999999',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'status' => 'active',
        ]);

        $response->assertRedirect('/admin/staff');

        $this->assertDatabaseHas('users', [
            'email' => 'newstaff@example.com',
            'role' => 'staff',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_update_staff_password(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $staff = User::factory()->create([
            'role' => 'staff',
            'status' => 'active',
            'password' => 'oldpassword123',
        ]);

        $response = $this->actingAs($admin)->put('/admin/staff/'.$staff->id, [
            'name' => $staff->name,
            'email' => $staff->email,
            'phone' => $staff->phone,
            'status' => 'active',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect('/admin/staff');

        $staff->refresh();

        $this->assertTrue(Hash::check('newpassword123', $staff->password));
    }
}
