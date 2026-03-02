<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAdminPasswordRequest;
use App\Http\Requests\Admin\UpdateShipmentApiCredentialRequest;
use App\Models\Setting;
use App\Models\ShipmentApiKey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'razorpay_key_id' => Setting::get('razorpay_key_id', ''),
            'razorpay_key_secret' => Setting::get('razorpay_key_secret', ''),
            'razorpay_enabled' => Setting::get('razorpay_enabled', '1'),
        ];

        $shipmentApiCredential = ShipmentApiKey::query()->where('provider', 'shiprocket')->first();

        return view('admin.settings.index', compact('settings', 'shipmentApiCredential'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'razorpay_key_id' => [
                'nullable',
                'string',
                'max:100',
                'required_if:razorpay_enabled,1',
                'required_with:razorpay_key_secret',
                'regex:/^rzp_(test|live)_[A-Za-z0-9]+$/',
            ],
            'razorpay_key_secret' => [
                'nullable',
                'string',
                'max:100',
                'required_if:razorpay_enabled,1',
                'required_with:razorpay_key_id',
                'regex:/^[A-Za-z0-9]{16,}$/',
            ],
            'razorpay_enabled' => 'nullable|boolean',
        ], [
            'razorpay_key_id.required_if' => 'Razorpay Key ID is required when Razorpay is enabled.',
            'razorpay_key_id.required_with' => 'Razorpay Key ID is required when Key Secret is provided.',
            'razorpay_key_id.regex' => 'Razorpay Key ID must start with rzp_test_ or rzp_live_.',
            'razorpay_key_secret.required_if' => 'Razorpay Key Secret is required when Razorpay is enabled.',
            'razorpay_key_secret.required_with' => 'Razorpay Key Secret is required when Key ID is provided.',
            'razorpay_key_secret.regex' => 'Razorpay Key Secret format looks invalid.',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value ?? '');
        }

        return back()->with('success', 'Settings updated successfully');
    }

    public function updatePassword(UpdateAdminPasswordRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully');
    }

    public function updateShipmentApiCredentials(UpdateShipmentApiCredentialRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $shipmentApiCredential = ShipmentApiKey::query()->where('provider', 'shiprocket')->first();

        if (! $shipmentApiCredential && blank($validated['api_password'] ?? null)) {
            return back()->withErrors([
                'api_password' => 'Shiprocket API password is required for first-time setup.',
            ])->withInput();
        }

        if ($shipmentApiCredential && blank($validated['api_password'] ?? null)) {
            unset($validated['api_password']);
        }

        $validated['provider'] = 'shiprocket';
        $validated['is_active'] = $request->boolean('is_active');

        ShipmentApiKey::query()->updateOrCreate(
            ['provider' => 'shiprocket'],
            $validated
        );

        return back()->with('success', 'Shiprocket API credentials updated successfully');
    }

    public function generateShipmentApiToken(): RedirectResponse
    {
        $shipmentApiCredential = ShipmentApiKey::query()->where('provider', 'shiprocket')->first();

        if (! $shipmentApiCredential || blank($shipmentApiCredential->api_email) || blank($shipmentApiCredential->api_password)) {
            return back()->withErrors([
                'api_email' => 'Please save Shiprocket email and password first.',
            ]);
        }

        try {
            $response = Http::timeout(20)
                ->acceptJson()
                ->post('https://apiv2.shiprocket.in/v1/external/auth/login', [
                    'email' => $shipmentApiCredential->api_email,
                    'password' => $shipmentApiCredential->api_password,
                ]);
        } catch (\Throwable $exception) {
            return back()->withErrors([
                'api_email' => 'Unable to connect to Shiprocket API. Please try again.',
            ]);
        }

        $token = $response->json('token');

        if (! $response->successful() || blank($token)) {
            return back()->withErrors([
                'api_email' => 'Shiprocket token generation failed. Please verify your credentials.',
            ]);
        }

        $shipmentApiCredential->update([
            'api_token' => $token,
            'is_active' => true,
        ]);

        return back()->with('success', 'Shiprocket API token generated successfully');
    }
}
