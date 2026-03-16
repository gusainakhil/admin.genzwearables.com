<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreReturnRequest;
use App\Models\OrderItem;
use App\Models\ReturnRequest;
use Illuminate\Http\JsonResponse;

class ReturnRequestController extends Controller
{
    public function store(StoreReturnRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $orderItem = OrderItem::query()
            ->with('order')
            ->findOrFail($validated['order_item_id']);

        if (! $orderItem->order || $orderItem->order->user_id !== $request->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot submit a return request for this order item.',
            ], 403);
        }

        if (
            isset($validated['product_variant_id'])
            && (int) $validated['product_variant_id'] !== (int) $orderItem->product_variant_id
        ) {
            return response()->json([
                'status' => false,
                'message' => 'Selected variant does not match this order item.',
            ], 422);
        }

        $imagePaths = [];

        foreach ($request->file('product_images', []) as $image) {
            $imagePaths[] = $image->store('returns', 'public');
        }

        $returnRequest = ReturnRequest::query()->create([
            'order_item_id' => $orderItem->id,
            'request_type' => $validated['request_type'],
            'requested_by' => null,
            'product_variant_id' => $validated['product_variant_id'] ?? $orderItem->product_variant_id,
            'tracking_id' => null,
            'courier_name' => null,
            'link' => null,
            'product_images' => $imagePaths,
            'reason' => $validated['reason'] ?? null,
            'status' => 'requested',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Return request submitted successfully.',
            'data' => $returnRequest,
        ], 201);
    }
}
