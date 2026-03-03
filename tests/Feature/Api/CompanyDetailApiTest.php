<?php

namespace Tests\Feature\Api;

use App\Models\CompanyDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyDetailApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_company_details_payload(): void
    {
        CompanyDetail::query()->create([
            'brand_name' => 'Genz Wearables',
            'company_headline' => 'Smart fashion for every day',
            'address' => '123 Fashion Street',
            'city' => 'Jaipur',
            'district' => 'Jaipur',
            'pincode' => '302001',
            'state' => 'Rajasthan',
            'country' => 'India',
            'gst_number' => '08ABCDE1234F1Z5',
            'phone_number1' => '9999999999',
            'phone_number2' => '8888888888',
            'website_name' => 'genzwearables.com',
            'support_email' => 'support@genzwearables.com',
            'email_primary' => 'hello@genzwearables.com',
            'email_secondary' => 'team@genzwearables.com',
            'additional_info' => 'Open Monday to Saturday',
            'youtube_url' => 'https://youtube.com/@genzwearables',
            'facebook_url' => 'https://facebook.com/genzwearables',
            'pinterest_url' => 'https://pinterest.com/genzwearables',
            'twitter_url' => 'https://twitter.com/genzwearables',
            'linkedin_url' => 'https://linkedin.com/company/genzwearables',
        ]);

        $response = $this->getJson('/api/company-details');

        $response
            ->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.brand_name', 'Genz Wearables')
            ->assertJsonPath('data.company_headline', 'Smart fashion for every day')
            ->assertJsonPath('data.youtube_url', 'https://youtube.com/@genzwearables')
            ->assertJsonPath('data.facebook_url', 'https://facebook.com/genzwearables')
            ->assertJsonPath('data.pinterest_url', 'https://pinterest.com/genzwearables')
            ->assertJsonPath('data.twitter_url', 'https://twitter.com/genzwearables')
            ->assertJsonPath('data.linkedin_url', 'https://linkedin.com/company/genzwearables');
    }
}
