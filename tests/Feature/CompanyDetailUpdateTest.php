<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CompanyDetailUpdateTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_save_company_headline_and_social_links(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post('/admin/company-details', [
            'brand_name' => 'Genz Wearables',
            'company_headline' => 'Smart fashion for every day',
            'youtube_url' => 'https://youtube.com/@genzwearables',
            'facebook_url' => 'https://facebook.com/genzwearables',
            'pinterest_url' => 'https://pinterest.com/genzwearables',
            'twitter_url' => 'https://twitter.com/genzwearables',
            'linkedin_url' => 'https://linkedin.com/company/genzwearables',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('company_details', [
            'brand_name' => 'Genz Wearables',
            'company_headline' => 'Smart fashion for every day',
            'youtube_url' => 'https://youtube.com/@genzwearables',
            'facebook_url' => 'https://facebook.com/genzwearables',
            'pinterest_url' => 'https://pinterest.com/genzwearables',
            'twitter_url' => 'https://twitter.com/genzwearables',
            'linkedin_url' => 'https://linkedin.com/company/genzwearables',
        ]);
    }
}
