<?php

use App\Models\User;
use Illuminate\Support\Facades\Cache;

it('returns validation error when phone is already registered', function () {
    $phone = '9876543210';

    User::factory()->create([
        'phone' => $phone,
    ]);

    $response = $this->postJson('/api/auth/register/otp', [
        'name' => 'Duplicate Phone User',
        'phone' => $phone,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonPath('status', false)
        ->assertJsonPath('message', 'Validation failed');

    expect(data_get($response->json(), 'errors.phone.0'))->not->toBeNull();

    $cacheKey = 'register_otp:'.sha1(trim($phone));

    expect(Cache::get($cacheKey))->toBeNull();
});
