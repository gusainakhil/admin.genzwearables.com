<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApplyCouponRequest;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function apply(ApplyCouponRequest $request): JsonResponse
    {
        $code = Str::upper(trim((string) $request->validated('code')));

        $cart = Cart::query()
            ->with('items.productVariant')
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $cart || $cart->items->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Cart is empty',
            ], 422);
        }

        $subtotal = (float) $cart->items->sum(function ($item) {
            return (float) $item->productVariant->price * (int) $item->quantity;
        });

        $coupon = Coupon::query()
            ->where('code', $code)
            ->where('status', 'active')
            ->first();

        if (! $coupon) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid coupon code',
            ], 422);
        }

        if (strtotime((string) $coupon->expiry_date) < strtotime(today()->toDateString())) {
            return response()->json([
                'status' => false,
                'message' => 'Coupon has expired',
            ], 422);
        }

        if ($coupon->min_order_amount !== null && $subtotal < (float) $coupon->min_order_amount) {
            return response()->json([
                'status' => false,
                'message' => 'Minimum order amount not met for this coupon',
            ], 422);
        }

        $usedCount = 0;

        if ($coupon->user_usage_limit !== null) {
            $usedCount = Order::query()
                ->where('user_id', $request->user()->id)
                ->where('coupon_id', $coupon->id)
                ->where('payment_status', 'paid')
                ->count();

            if ($usedCount >= $coupon->user_usage_limit) {
                return response()->json([
                    'status' => false,
                    'message' => 'Coupon usage limit reached',
                ], 422);
            }
        }

        $discountAmount = $coupon->discount_type === 'percent'
            ? round($subtotal * ((float) $coupon->discount_value / 100), 2)
            : (float) $coupon->discount_value;

        $discountAmount = min($discountAmount, $subtotal);
        $finalAmount = max(0, round($subtotal - $discountAmount, 2));

        return response()->json([
            'status' => true,
            'message' => 'Coupon applied successfully',
            'data' => [
                'code' => $coupon->code,
                'discount_type' => $coupon->discount_type,
                'discount_value' => $coupon->discount_value,
                'discount_amount' => $discountAmount,
                'subtotal' => $subtotal,
                'total_after_discount' => $finalAmount,
                'user_usage_limit' => $coupon->user_usage_limit,
                'used_count' => $usedCount,
                'remaining_uses' => $coupon->user_usage_limit !== null
                    ? max(0, (int) $coupon->user_usage_limit - $usedCount)
                    : null,
            ],
        ]);
    }
}
