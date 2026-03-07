<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminSettingsAuthkeyTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_view_authkey_setup_guide_on_settings_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get('/admin/settings');

        $response->assertOk();
        $response->assertSee('Authkey API Key');
        $response->assertSee('How to set up Authkey API Key');
    }

    public function test_admin_can_save_authkey_api_key(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post('/admin/settings', [
            'authkey_api_key' => 'authkey_test_api_key_1234567890',
            'razorpay_enabled' => '0',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Settings updated successfully');

        $this->assertDatabaseHas('settings', [
            'setting_key' => 'authkey_api_key',
            'setting_value' => 'authkey_test_api_key_1234567890',
        ]);

        $this->assertSame('authkey_test_api_key_1234567890', Setting::get('authkey_api_key'));
    }
}
