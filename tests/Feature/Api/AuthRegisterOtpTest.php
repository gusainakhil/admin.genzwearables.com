<?php

namespace Tests\Feature\Api;

use App\Mail\RegisterOtpMail;
use App\Models\CompanyDetail;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthRegisterOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_without_otp_sends_otp_to_email_and_mobile(): void
    {
        CompanyDetail::query()->create([
            'brand_name' => 'Genz Wearables',
            'email_secondary' => 'noreply@genzwearables.com',
        ]);

        Setting::set('authkey_api_key', 'test_authkey_123');

        Mail::fake();
        Http::fake([
            'https://api.authkey.io/*' => Http::response(['status' => 'ok'], 200),
        ]);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '9876543210',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.otp_required', true);

        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com',
        ]);

        Mail::assertSent(RegisterOtpMail::class, function (RegisterOtpMail $mail): bool {
            return $mail->hasTo('test@example.com')
                && $mail->brandName === 'Genz Wearables'
                && strlen($mail->otp) === 6;
        });

        Http::assertSent(function (HttpRequest $request): bool {
            return str_starts_with($request->url(), 'https://api.authkey.io/request')
                && $request['authkey'] === 'test_authkey_123'
                && $request['mobile'] === '9876543210';
        });
    }

    public function test_register_with_valid_otp_creates_user_and_token(): void
    {
        $email = 'verify@example.com';
        $phone = '9999999999';
        $otp = '123456';
        $cacheKey = 'register_otp:'.sha1(strtolower(trim($email)).'|'.trim($phone));

        Cache::put($cacheKey, [
            'otp_hash' => Hash::make($otp),
        ], now()->addMinutes(10));

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Verify User',
            'email' => $email,
            'phone' => $phone,
            'password' => 'password',
            'password_confirmation' => 'password',
            'otp' => $otp,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('status', true)
            ->assertJsonPath('message', 'Registration successful');

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'phone' => $phone,
            'role' => 'customer',
        ]);

        $this->assertNull(Cache::get($cacheKey));
    }

    public function test_register_with_invalid_otp_returns_validation_error(): void
    {
        $email = 'wrongotp@example.com';
        $phone = '8888888888';
        $cacheKey = 'register_otp:'.sha1(strtolower(trim($email)).'|'.trim($phone));

        Cache::put($cacheKey, [
            'otp_hash' => Hash::make('123456'),
        ], now()->addMinutes(10));

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Wrong Otp User',
            'email' => $email,
            'phone' => $phone,
            'password' => 'password',
            'password_confirmation' => 'password',
            'otp' => '000000',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('status', false)
            ->assertJsonPath('message', 'Invalid OTP');

        $this->assertDatabaseMissing('users', [
            'email' => $email,
        ]);
    }
}
