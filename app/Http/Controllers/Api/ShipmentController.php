<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShipmentApiKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ShipmentController extends Controller
{
    public function config(): JsonResponse
    {
        $credential = ShipmentApiKey::query()->where('provider', 'shiprocket')->first();

        return response()->json([
            'status' => true,
            'data' => [
                'shiprocket' => [
                    'enabled' => (bool) ($credential?->is_active),
                    'configured' => (bool) ($credential && filled($credential->api_email) && filled($credential->api_password)),
                ],
            ],
        ]);
    }

    public function serviceability(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pickup_postcode' => 'required|string|max:20',
            'delivery_postcode' => 'required|string|max:20',
            'weight' => 'required|numeric|min:0.1',
            'cod' => 'nullable|boolean',
            'declared_value' => 'nullable|numeric|min:0',
        ]);

        $credential = ShipmentApiKey::query()->where('provider', 'shiprocket')->first();

        if (! $credential || ! $credential->is_active) {
            return response()->json([
                'status' => false,
                'message' => 'Shiprocket API is not configured or disabled.',
            ], 422);
        }

        $token = $this->resolveShiprocketToken($credential);

        if (blank($token)) {
            return response()->json([
                'status' => false,
                'message' => 'Unable to authenticate with Shiprocket. Please contact admin.',
            ], 422);
        }

        $requestPayload = [
            'pickup_postcode' => $validated['pickup_postcode'],
            'delivery_postcode' => $validated['delivery_postcode'],
            'weight' => $validated['weight'],
            'cod' => (int) ($validated['cod'] ?? false),
        ];

        if (isset($validated['declared_value'])) {
            $requestPayload['declared_value'] = $validated['declared_value'];
        }

        $response = Http::timeout(20)
            ->acceptJson()
            ->withToken($token)
            ->get('https://apiv2.shiprocket.in/v1/external/courier/serviceability/', $requestPayload);

        if ($response->status() === 401) {
            $token = $this->refreshShiprocketToken($credential);

            if (filled($token)) {
                $response = Http::timeout(20)
                    ->acceptJson()
                    ->withToken($token)
                    ->get('https://apiv2.shiprocket.in/v1/external/courier/serviceability/', $requestPayload);
            }
        }

        if (! $response->successful()) {
            return response()->json([
                'status' => false,
                'message' => $response->json('message') ?? 'Failed to fetch Shiprocket serviceability.',
                'errors' => $response->json(),
            ], $response->status() >= 400 ? $response->status() : 422);
        }

        return response()->json([
            'status' => true,
            'data' => $response->json(),
        ]);
    }

    private function resolveShiprocketToken(ShipmentApiKey $credential): ?string
    {
        if (filled($credential->api_token)) {
            return $credential->api_token;
        }

        return $this->refreshShiprocketToken($credential);
    }

    private function refreshShiprocketToken(ShipmentApiKey $credential): ?string
    {
        if (blank($credential->api_email) || blank($credential->api_password)) {
            return null;
        }

        $response = Http::timeout(20)
            ->acceptJson()
            ->post('https://apiv2.shiprocket.in/v1/external/auth/login', [
                'email' => $credential->api_email,
                'password' => $credential->api_password,
            ]);

        $token = $response->json('token');

        if (! $response->successful() || blank($token)) {
            return null;
        }

        $credential->update([
            'api_token' => $token,
        ]);

        return $token;
    }
}
