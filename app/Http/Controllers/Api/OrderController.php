<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreOrderRequest;
use App\Http\Requests\Api\UpdateOrderPaymentRequest;
use App\Mail\OrderPaid;
use App\Mail\OrderPlaced;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);

        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate($perPage);

        $orders->getCollection()->transform(function (Order $order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'total' => $order->total,
                'payment_status' => $order->payment_status,
                'order_status' => $order->order_status,
                'created_at' => $order->created_at,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $orders,
        ]);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found',
            ], 404);
        }

        $order->load([
            'address',
            'payment',
            'items.product.images' => function ($q) {
                $q->orderByDesc('is_primary');
            },
            'items.variant.size',
            'items.variant.color',
        ]);

        $items = $order->items->map(function (OrderItem $item) {
            $product = $item->product;
            $variant = $item->variant;
            $images = $product?->images?->map(function ($image) {
                return [
                    'id' => $image->id,
                    'path' => $image->image,
                    'url' => asset('storage/'.$image->image),
                    'is_primary' => (bool) $image->is_primary,
                ];
            }) ?? collect();

            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'product' => $product ? [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'brand' => $product->brand,
                    'images' => $images,
                ] : null,
                'variant' => $variant ? [
                    'id' => $variant->id,
                    'sku' => $variant->sku,
                    'price' => $variant->price,
                    'stock_qty' => $variant->stock_qty,
                    'status' => $variant->status,
                    'size' => $variant->size,
                    'color' => $variant->color,
                ] : null,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'subtotal' => $order->subtotal,
                'shipping' => $order->shipping,
                'discount' => $order->discount,
                'total' => $order->total,
                'payment_status' => $order->payment_status,
                'order_status' => $order->order_status,
                'address' => $order->address,
                'payment' => $order->payment,
                'items' => $items,
                'created_at' => $order->created_at,
            ],
        ]);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $cart = Cart::with(['items.productVariant.product'])
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $cart || $cart->items->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Cart is empty',
            ], 422);
        }

        foreach ($cart->items as $item) {
            $variant = $item->productVariant;

            if (! $variant) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product variant not found',
                ], 422);
            }

            if ($item->quantity > $variant->stock_qty) {
                return response()->json([
                    'status' => false,
                    'message' => 'Insufficient stock',
                    'data' => [
                        'product_variant_id' => $variant->id,
                        'available_qty' => (int) $variant->stock_qty,
                        'requested_qty' => $item->quantity,
                    ],
                ], 422);
            }
        }

        $subtotal = $cart->items->sum(function ($item) {
            return (float) $item->productVariant->price * $item->quantity;
        });
        $shipping = 0;
        $discount = 0;
        $total = $subtotal + $shipping - $discount;

        $order = DB::transaction(function () use ($cart, $validated, $subtotal, $shipping, $discount, $total) {
            $order = Order::create([
                'user_id' => $cart->user_id,
                'address_id' => $validated['address_id'],
                'order_number' => $this->generateOrderNumber(),
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'discount' => $discount,
                'total' => $total,
                'payment_status' => 'pending',
                'order_status' => 'placed',
            ]);

            foreach ($cart->items as $item) {
                $variant = $item->productVariant;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'price' => $variant->price,
                    'quantity' => $item->quantity,
                ]);
            }

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $validated['payment_method'],
                'transaction_id' => null,
                'amount' => $total,
                'status' => 'pending',
                'response' => null,
            ]);

            $cart->items()->delete();

            return $order;
        });

        $order->load('user');
        Mail::to($order->user->email)->send(new OrderPlaced($order));

        return response()->json([
            'status' => true,
            'message' => 'Order created',
            'data' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'total' => $order->total,
                'payment_status' => $order->payment_status,
            ],
        ], 201);
    }

    public function updatePayment(UpdateOrderPaymentRequest $request, Order $order): JsonResponse
    {
        $validated = $request->validated();

        if ($order->payment_status === 'paid') {
            return response()->json([
                'status' => true,
                'message' => 'Order already paid',
                'data' => [
                    'id' => $order->id,
                    'payment_status' => $order->payment_status,
                ],
            ]);
        }

        $order->load(['items.variant', 'payment', 'user']);

        if ($validated['payment_status'] === 'paid') {
            foreach ($order->items as $item) {
                $variant = $item->variant;

                if (! $variant) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Product variant not found',
                    ], 422);
                }

                if ($item->quantity > $variant->stock_qty) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Insufficient stock',
                        'data' => [
                            'product_variant_id' => $variant->id,
                            'available_qty' => (int) $variant->stock_qty,
                            'requested_qty' => $item->quantity,
                        ],
                    ], 422);
                }
            }
        }

        try {
            DB::transaction(function () use ($order, $validated) {
                $paymentStatus = $validated['payment_status'] === 'paid' ? 'success' : 'failed';

                if ($validated['payment_status'] === 'paid') {
                    foreach ($order->items as $item) {
                        $variant = ProductVariant::query()
                            ->whereKey($item->product_variant_id)
                            ->lockForUpdate()
                            ->first();

                        if (! $variant || $item->quantity > $variant->stock_qty) {
                            throw new \RuntimeException('insufficient_stock');
                        }

                        $variant->decrement('stock_qty', $item->quantity);
                    }
                }

                $order->update([
                    'payment_status' => $validated['payment_status'],
                ]);

                Payment::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'payment_method' => $validated['payment_method'] ?? ($order->payment?->payment_method ?? 'razorpay'),
                        'transaction_id' => $validated['transaction_id'] ?? $order->payment?->transaction_id,
                        'amount' => $order->total ?? 0,
                        'status' => $paymentStatus,
                        'response' => $validated['response'] ?? $order->payment?->response,
                    ]
                );
            });
        } catch (\RuntimeException $exception) {
            if ($exception->getMessage() === 'insufficient_stock') {
                return response()->json([
                    'status' => false,
                    'message' => 'Insufficient stock',
                ], 422);
            }

            throw $exception;
        }

        if ($validated['payment_status'] === 'paid') {
            Mail::to($order->user->email)->send(new OrderPaid($order));
        }

        return response()->json([
            'status' => true,
            'message' => 'Payment status updated',
            'data' => [
                'id' => $order->id,
                'payment_status' => $order->payment_status,
            ],
        ]);
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-'.Str::upper(Str::random(10));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
