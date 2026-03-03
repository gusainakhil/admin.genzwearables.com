<?php

namespace Tests\Feature\Api;

use App\Models\SidebarBanner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarBannerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_sidebar_banners_payload(): void
    {
        SidebarBanner::query()->create([
            'heading' => 'Mega Sale',
            'sub_heading' => 'New arrivals at best price',
            'image' => 'sidebar-banners/sample.jpg',
        ]);

        $response = $this->getJson('/api/sidebar-banners');

        $response
            ->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.0.heading', 'Mega Sale')
            ->assertJsonPath('data.0.sub_heading', 'New arrivals at best price')
            ->assertJsonPath('data.0.image', 'sidebar-banners/sample.jpg')
            ->assertJsonPath('data.0.image_url', asset('storage/sidebar-banners/sample.jpg'));
    }
}
