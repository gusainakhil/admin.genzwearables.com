<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreReturnRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReturnRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReturnRequestController extends Controller
{
    private function transformReturnRequest(ReturnRequest $returnRequest): array
    {
        $productImages = collect($returnRequest->product_images ?? [])->map(function (string $path) {
            return [
                'path' => $path,
                'url' => asset('storage/'.$path),
            ];
        });

        return [
            'id' => $returnRequest->id,
            'order_item_id' => $returnRequest->order_item_id,
            'order_number' => $returnRequest->orderItem?->order?->order_number,
            'request_type' => $returnRequest->request_type,
            'requested_by' => $returnRequest->requested_by,
            'product_variant_id' => $returnRequest->product_variant_id,
            'tracking_id' => $returnRequest->tracking_id,
            'courier_name' => $returnRequest->courier_name,
            'link' => $returnRequest->link,
            'reason' => $returnRequest->reason,
            'status' => $returnRequest->status,
            'product' => $returnRequest->orderItem?->product ? [
                'id' => $returnRequest->orderItem->product->id,
                'name' => $returnRequest->orderItem->product->name,
                'slug' => $returnRequest->orderItem->product->slug,
            ] : null,
            'variant' => $returnRequest->productVariant ? [
                'id' => $returnRequest->productVariant->id,
                'sku' => $returnRequest->productVariant->sku,
                'size' => $returnRequest->productVariant->size?->name,
                'color' => $returnRequest->productVariant->color?->name,
            ] : null,
            'product_images' => $productImages,
            'created_at' => $returnRequest->created_at,
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);

        $returnRequests = ReturnRequest::query()
            ->whereHas('orderItem.order', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with([
                'orderItem.order',
                'orderItem.product',
                'productVariant.size',
                'productVariant.color',
            ])
            ->latest()
            ->paginate($perPage);

        $returnRequests->getCollection()->transform(function (ReturnRequest $returnRequest) {
            return $this->transformReturnRequest($returnRequest);
        });

        return response()->json([
            'status' => true,
            'data' => $returnRequests,
        ]);
    }

    public function show(Request $request, ReturnRequest $returnRequest): JsonResponse
    {
        $returnRequest->load([
            'orderItem.order',
            'orderItem.product',
            'productVariant.size',
            'productVariant.color',
        ]);

        if (! $returnRequest->orderItem?->order || $returnRequest->orderItem->order->user_id !== $request->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Return request not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $this->transformReturnRequest($returnRequest),
        ]);
    }

    public function orderReturns(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found',
            ], 404);
        }

        $returnRequests = ReturnRequest::query()
            ->whereHas('orderItem', function ($query) use ($order) {
                $query->where('order_id', $order->id);
            })
            ->with([
                'orderItem.order',
                'orderItem.product',
                'productVariant.size',
                'productVariant.color',
            ])
            ->latest()
            ->get()
            ->map(function (ReturnRequest $returnRequest) {
                return $this->transformReturnRequest($returnRequest);
            })
            ->values();

        return response()->json([
            'status' => true,
            'data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'returns' => $returnRequests,
            ],
        ]);
    }

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
