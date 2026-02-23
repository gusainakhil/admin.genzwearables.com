<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function config(): JsonResponse
    {
        $razorpayEnabled = Setting::get('razorpay_enabled', '1') === '1';
        $razorpayKeyId = Setting::get('razorpay_key_id', '');

        return response()->json([
            'status' => true,
            'data' => [
                'razorpay' => [
                    'enabled' => $razorpayEnabled,
                    'key_id' => $razorpayKeyId,
                ],
            ],
        ]);
    }
}
