<?php

namespace Tests\Feature;

use App\Models\SidebarBanner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SidebarBannerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_sidebar_banner(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.sidebar-banners.store'), [
            'heading' => 'Festive Offer',
            'sub_heading' => 'Flat 40% off on selected styles',
            'image' => UploadedFile::fake()->image('banner.jpg'),
        ]);

        $response->assertRedirect(route('admin.sidebar-banners.index'));

        $this->assertDatabaseHas('sidebar_banners', [
            'heading' => 'Festive Offer',
            'sub_heading' => 'Flat 40% off on selected styles',
        ]);

        $banner = SidebarBanner::query()->first();

        $this->assertNotNull($banner);
        Storage::disk('public')->assertExists($banner->image);
    }
}
