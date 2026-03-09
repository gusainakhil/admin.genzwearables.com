<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

it('sends otp from dedicated route', function () {
    Setting::set('authkey_api_key', 'test_authkey_123');

    Http::fake([
        'https://api.authkey.io/*' => Http::response(['status' => 'ok'], 200),
    ]);

    $response = $this->postJson('/api/auth/register/otp', [
        'name' => 'Split Flow User',
        'phone' => '9988776655',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response
        ->assertSuccessful()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.otp_required', true);
});

it('requires otp on final register route', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'Split Flow User',
        'phone' => '9988776655',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonPath('status', false)
        ->assertJsonPath('message', 'SMS OTP is required for verification.');
});

it('registers successfully from final route after otp verification', function () {
    $phone = '9911223344';
    $otp = '112233';
    $cacheKey = 'register_otp:'.sha1(trim($phone));

    Cache::put($cacheKey, [
        'sms_otp_hash' => bcrypt($otp),
    ], now()->addMinutes(10));

    $response = $this->postJson('/api/auth/register', [
        'name' => 'Final Register User',
        'phone' => $phone,
        'password' => 'password',
        'password_confirmation' => 'password',
        'sms_otp' => $otp,
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('status', true)
        ->assertJsonPath('message', 'Registration successful');

    $this->assertDatabaseHas('users', [
        'phone' => $phone,
        'name' => 'Final Register User',
    ]);

    $registeredUser = \App\Models\User::query()->where('phone', $phone)->first();

    expect($registeredUser)->not->toBeNull();
    expect($registeredUser?->email)->toContain('@genzwearables.local');
});
