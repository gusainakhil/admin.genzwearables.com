<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreOrderRequest;
use App\Http\Requests\Api\SyncRazorpayOrderPaymentRequest;
use App\Http\Requests\Api\UpdateOrderPaymentRequest;
use App\Mail\OrderPaid;
use App\Mail\OrderPlaced;
use App\Models\Cart;
use App\Models\CompanyDetail;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\UserAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\Error as RazorpayError;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);

        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->with([
                'items.product.images' => function ($query) {
                    $query->orderByDesc('is_primary');
                },
                'items.variant.size',
                'items.variant.color',
            ])
            ->latest()
            ->paginate($perPage);

        $orders->getCollection()->transform(function (Order $order) {
            $items = $order->items->map(function (OrderItem $item) {
                $product = $item->product;
                $variant = $item->variant;
                $primaryImage = $product?->images?->first();

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
                        'image' => $primaryImage ? [
                            'id' => $primaryImage->id,
                            'path' => $primaryImage->image,
                            'url' => asset('storage/'.$primaryImage->image),
                            'is_primary' => (bool) $primaryImage->is_primary,
                        ] : null,
                    ] : null,
                    'variant' => $variant ? [
                        'id' => $variant->id,
                        'sku' => $variant->sku,
                        'price' => $variant->price,
                        'size' => $variant->size,
                        'color' => $variant->color,
                    ] : null,
                ];
            });

            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'subtotal' => $order->subtotal,
                'shipping' => $order->shipping,
                'discount' => $order->discount,
                'total' => $order->total,
                'payment_status' => $order->payment_status,
                'order_status' => $order->order_status,
                'items' => $items,
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

        $this->autoSyncPendingRazorpayOrderOnShow($order);

        $order->load([
            'address',
            'payment',
            'items.product.images' => function ($q) {
                $q->orderByDesc('is_primary');
            },
            'items.variant.size',
            'items.variant.color',
        ]);

        $address = $order->address_snapshot;

        if (! $address && $order->address) {
            $address = [
                'name' => $order->address->name,
                'phone' => $order->address->phone,
                'address' => $order->address->address,
                'city' => $order->address->city,
                'state' => $order->address->state,
                'pincode' => $order->address->pincode,
                'country' => $order->address->country,
            ];
        }

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
                'address' => $address,
                'payment' => $order->payment,
                'items' => $items,
                'created_at' => $order->created_at,
            ],
        ]);
    }

    public function downloadInvoice(Request $request, string $orderReference): JsonResponse|StreamedResponse
    {
        $order = Order::query()
            ->where('user_id', $request->user()->id)
            ->where(function ($query) use ($orderReference): void {
                if (ctype_digit($orderReference)) {
                    $query->whereKey((int) $orderReference)
                        ->orWhere('order_number', $orderReference);

                    return;
                }

                $query->where('order_number', $orderReference);
            })
            ->first();

        if (! $order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found',
            ], 404);
        }

        $order->load('user', 'address', 'items.product', 'items.variant.size', 'items.variant.color', 'payment', 'shipment');
        $companyDetail = CompanyDetail::query()->first();

        $fileName = 'invoice-'.$order->order_number.'.html';
        $html = view('admin.orders.print-invoice', compact('order', 'companyDetail'))->render();

        return response()->streamDownload(function () use ($html): void {
            echo $html;
        }, $fileName, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

    private function autoSyncPendingRazorpayOrderOnShow(Order $order): void
    {
        if ($order->payment_status !== 'pending') {
            return;
        }

        if ($order->created_at && $order->created_at->lte(now()->subMinutes(30))) {
            DB::transaction(function () use ($order) {
                $freshOrder = Order::query()
                    ->with('payment')
                    ->lockForUpdate()
                    ->find($order->id);

                if (! $freshOrder || $freshOrder->payment_status !== 'pending') {
                    return;
                }

                $freshOrder->update([
                    'payment_status' => 'failed',
                    'order_status' => 'cancelled',
                ]);

                if ($freshOrder->payment) {
                    $freshOrder->payment->update([
                        'status' => 'failed',
                    ]);
                }
            });

            $order->refresh();

            return;
        }

        $payment = $order->payment()->first();

        if (! $payment) {
            return;
        }

        if (Str::lower((string) $payment->payment_method) !== 'razorpay' || ! $payment->transaction_id) {
            return;
        }

        try {
            $razorpayPayload = $this->fetchRazorpayOrderPayments($payment->transaction_id);
        } catch (\RuntimeException) {
            return;
        }

        $items = collect($razorpayPayload['items'] ?? []);

        $hasCaptured = $items->contains(function ($item) {
            if (! is_array($item)) {
                return false;
            }

            return ($item['status'] ?? null) === 'captured' || ($item['captured'] ?? false) === true;
        });

        $hasPending = $items->contains(function ($item) {
            if (! is_array($item)) {
                return false;
            }

            return in_array(($item['status'] ?? ''), ['created', 'authorized'], true);
        });

        $hasFailed = $items->contains(function ($item) {
            return is_array($item) && ($item['status'] ?? null) === 'failed';
        });

        if ($hasCaptured) {
            $mailRequired = false;

            try {
                DB::transaction(function () use ($order, $payment, $razorpayPayload, &$mailRequired) {
                    $freshOrder = Order::query()
                        ->with(['items', 'payment'])
                        ->lockForUpdate()
                        ->find($order->id);

                    if (! $freshOrder || $freshOrder->payment_status !== 'pending') {
                        return;
                    }

                    foreach ($freshOrder->items as $item) {
                        $variant = ProductVariant::query()
                            ->whereKey($item->product_variant_id)
                            ->lockForUpdate()
                            ->first();

                        if (! $variant || $item->quantity > $variant->stock_qty) {
                            throw new \RuntimeException('insufficient_stock');
                        }

                        $variant->decrement('stock_qty', $item->quantity);
                    }

                    $freshOrder->update([
                        'payment_status' => 'paid',
                    ]);

                    $mailRequired = true;

                    Cart::query()
                        ->where('user_id', $freshOrder->user_id)
                        ->get()
                        ->each(fn ($cart) => $cart->items()->delete());

                    Payment::updateOrCreate(
                        ['order_id' => $freshOrder->id],
                        [
                            'payment_method' => $payment->payment_method ?: 'razorpay',
                            'transaction_id' => $payment->transaction_id,
                            'amount' => $freshOrder->total ?? 0,
                            'status' => 'success',
                            'response' => json_encode($razorpayPayload),
                        ]
                    );
                });
            } catch (\RuntimeException) {
                return;
            }

            $order->refresh()->load('user');

            if ($mailRequired && $this->shouldSendOrderMail($order)) {
                Mail::to($order->user->email)->send(new OrderPaid($order));
            }

            return;
        }

        if ($hasFailed && ! $hasPending) {
            DB::transaction(function () use ($order, $payment, $razorpayPayload) {
                $freshOrder = Order::query()
                    ->lockForUpdate()
                    ->find($order->id);

                if ($freshOrder && $freshOrder->payment_status === 'pending') {
                    $freshOrder->update([
                        'payment_status' => 'failed',
                    ]);
                }

                Payment::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'payment_method' => $payment->payment_method ?: 'razorpay',
                        'transaction_id' => $payment->transaction_id,
                        'amount' => $order->total ?? 0,
                        'status' => 'failed',
                        'response' => json_encode($razorpayPayload),
                    ]
                );
            });

            $order->refresh();

            return;
        }

        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'payment_method' => $payment->payment_method ?: 'razorpay',
                'transaction_id' => $payment->transaction_id,
                'amount' => $order->total ?? 0,
                'status' => 'pending',
                'response' => json_encode($razorpayPayload),
            ]
        );
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $selectedAddress = UserAddress::query()
            ->where('user_id', $request->user()->id)
            ->whereKey($validated['address_id'])
            ->first();

        if (! $selectedAddress) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found',
            ], 422);
        }

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
        $couponId = null;
        $couponCode = null;

        if (! empty($validated['coupon_code'])) {
            $couponCode = Str::upper(trim((string) $validated['coupon_code']));

            $coupon = Coupon::query()
                ->where('code', $couponCode)
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

            $discount = $coupon->discount_type === 'percent'
                ? round($subtotal * ((float) $coupon->discount_value / 100), 2)
                : (float) $coupon->discount_value;

            $discount = min($discount, $subtotal + $shipping);
            $couponId = $coupon->id;
        }

        $total = max(0, $subtotal + $shipping - $discount);
        $orderNumber = $this->generateOrderNumber();

        $gatewayTransactionId = null;
        $gatewayResponse = null;

        if (Str::lower($validated['payment_method']) === 'razorpay') {
            try {
                $gatewayResponse = $this->createRazorpayOrder($total, $orderNumber, (int) $request->user()->id);
                $gatewayTransactionId = $gatewayResponse['id'];
            } catch (\RuntimeException $exception) {
                $knownValidationErrors = [
                    'razorpay_disabled',
                    'razorpay_credentials_missing',
                    'razorpay_invalid_amount',
                ];

                return response()->json([
                    'status' => false,
                    'message' => 'Unable to create Razorpay order',
                    'data' => [
                        'reason' => $exception->getMessage(),
                    ],
                ], in_array($exception->getMessage(), $knownValidationErrors, true) ? 422 : 502);
            }
        }

        $order = DB::transaction(function () use ($cart, $validated, $subtotal, $shipping, $discount, $total, $couponId, $couponCode, $selectedAddress, $orderNumber, $gatewayTransactionId, $gatewayResponse) {
            $order = Order::create([
                'user_id' => $cart->user_id,
                'address_snapshot' => [
                    'name' => $selectedAddress->name,
                    'phone' => $selectedAddress->phone,
                    'address' => $selectedAddress->address,
                    'city' => $selectedAddress->city,
                    'state' => $selectedAddress->state,
                    'pincode' => $selectedAddress->pincode,
                    'country' => $selectedAddress->country,
                ],
                'order_number' => $orderNumber,
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'discount' => $discount,
                'coupon_id' => $couponId,
                'coupon_code' => $couponCode,
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
                'transaction_id' => $gatewayTransactionId,
                'amount' => $total,
                'status' => 'pending',
                'response' => $gatewayResponse ? json_encode($gatewayResponse) : null,
            ]);

            return $order;
        });

        $order->load('user');

        if ($this->shouldSendOrderMail($order)) {
            Mail::to($order->user->email)->send(new OrderPlaced($order));
        }

        return response()->json([
            'status' => true,
            'message' => 'Order created',
            'data' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'discount' => $order->discount,
                'coupon_code' => $order->coupon_code,
                'total' => $order->total,
                'payment_status' => $order->payment_status,
                'transaction_id' => $gatewayTransactionId,
            ],
        ], 201);
    }

    public function updatePayment(UpdateOrderPaymentRequest $request, Order $order): JsonResponse
    {
        $validated = $request->validated();

        if ($order->payment_status === 'paid') {
            $order->load(['items.variant']);
            $itemStocks = $order->items->map(function (OrderItem $item) {
                return [
                    'product_variant_id' => $item->product_variant_id,
                    'stock_qty' => $item->variant?->stock_qty,
                ];
            });

            return response()->json([
                'status' => true,
                'message' => 'Order already paid',
                'data' => [
                    'id' => $order->id,
                    'payment_status' => $order->payment_status,
                    'items' => $itemStocks,
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

                    Cart::query()
                        ->where('user_id', $order->user_id)
                        ->get()
                        ->each(fn ($cart) => $cart->items()->delete());
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

        if ($validated['payment_status'] === 'paid' && $this->shouldSendOrderMail($order)) {
            Mail::to($order->user->email)->send(new OrderPaid($order));
        }

        $order->load(['items.variant']);

        $itemStocks = $order->items->map(function (OrderItem $item) {
            return [
                'product_variant_id' => $item->product_variant_id,
                'stock_qty' => $item->variant?->stock_qty,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Payment status updated',
            'data' => [
                'id' => $order->id,
                'payment_status' => $order->payment_status,
                'items' => $itemStocks,
            ],
        ]);
    }

    public function syncRazorpayPayment(SyncRazorpayOrderPaymentRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $payment = Payment::query()
            ->with(['order.items.variant', 'order.user'])
            ->where('transaction_id', $validated['order_id'])
            ->whereHas('order', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->latest('id')
            ->first();

        if (! $payment || ! $payment->order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found',
            ], 404);
        }

        $order = $payment->order;

        if ($order->payment_status === 'paid') {
            return response()->json([
                'status' => true,
                'message' => 'Order already paid',
                'data' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'payment_status' => $order->payment_status,
                    'order_id' => $validated['order_id'],
                ],
            ]);
        }

        try {
            $razorpayPayload = $this->fetchRazorpayOrderPayments($validated['order_id']);
        } catch (\RuntimeException $exception) {
            return response()->json([
                'status' => false,
                'message' => 'Unable to fetch payment status from Razorpay',
                'data' => [
                    'reason' => $exception->getMessage(),
                ],
            ], 502);
        }

        $items = collect($razorpayPayload['items'] ?? []);

        $hasCaptured = $items->contains(function ($item) {
            if (! is_array($item)) {
                return false;
            }

            return ($item['status'] ?? null) === 'captured' || ($item['captured'] ?? false) === true;
        });

        $hasPending = $items->contains(function ($item) {
            if (! is_array($item)) {
                return false;
            }

            return in_array(($item['status'] ?? ''), ['created', 'authorized'], true);
        });

        $hasFailed = $items->contains(function ($item) {
            return is_array($item) && ($item['status'] ?? null) === 'failed';
        });

        if ($hasCaptured) {
            $mailRequired = false;

            try {
                DB::transaction(function () use ($order, $payment, $validated, $razorpayPayload, &$mailRequired) {
                    $freshOrder = Order::query()
                        ->with(['items', 'payment'])
                        ->lockForUpdate()
                        ->find($order->id);

                    if (! $freshOrder) {
                        throw new \RuntimeException('order_not_found');
                    }

                    if ($freshOrder->payment_status !== 'paid') {
                        foreach ($freshOrder->items as $item) {
                            $variant = ProductVariant::query()
                                ->whereKey($item->product_variant_id)
                                ->lockForUpdate()
                                ->first();

                            if (! $variant || $item->quantity > $variant->stock_qty) {
                                throw new \RuntimeException('insufficient_stock');
                            }

                            $variant->decrement('stock_qty', $item->quantity);
                        }

                        $freshOrder->update([
                            'payment_status' => 'paid',
                        ]);

                        $mailRequired = true;

                        Cart::query()
                            ->where('user_id', $freshOrder->user_id)
                            ->get()
                            ->each(fn ($cart) => $cart->items()->delete());
                    }

                    Payment::updateOrCreate(
                        ['order_id' => $freshOrder->id],
                        [
                            'payment_method' => $payment->payment_method ?: 'razorpay',
                            'transaction_id' => $validated['order_id'],
                            'amount' => $freshOrder->total ?? 0,
                            'status' => 'success',
                            'response' => json_encode($razorpayPayload),
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

                return response()->json([
                    'status' => false,
                    'message' => 'Unable to sync payment status',
                    'data' => [
                        'reason' => $exception->getMessage(),
                    ],
                ], 422);
            }

            $order->refresh()->load('user');

            if ($mailRequired && $this->shouldSendOrderMail($order)) {
                Mail::to($order->user->email)->send(new OrderPaid($order));
            }

            return response()->json([
                'status' => true,
                'message' => 'Payment synchronized',
                'data' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'order_id' => $validated['order_id'],
                    'payment_status' => $order->payment_status,
                ],
            ]);
        }

        if ($hasFailed && ! $hasPending) {
            DB::transaction(function () use ($order, $payment, $validated, $razorpayPayload) {
                $freshOrder = Order::query()
                    ->lockForUpdate()
                    ->find($order->id);

                if ($freshOrder && $freshOrder->payment_status !== 'paid') {
                    $freshOrder->update([
                        'payment_status' => 'failed',
                    ]);
                }

                Payment::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'payment_method' => $payment->payment_method ?: 'razorpay',
                        'transaction_id' => $validated['order_id'],
                        'amount' => $order->total ?? 0,
                        'status' => 'failed',
                        'response' => json_encode($razorpayPayload),
                    ]
                );
            });

            $order->refresh();

            return response()->json([
                'status' => true,
                'message' => 'Payment synchronized',
                'data' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'order_id' => $validated['order_id'],
                    'payment_status' => $order->payment_status,
                ],
            ]);
        }

        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'payment_method' => $payment->payment_method ?: 'razorpay',
                'transaction_id' => $validated['order_id'],
                'amount' => $order->total ?? 0,
                'status' => 'pending',
                'response' => json_encode($razorpayPayload),
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Payment still pending',
            'data' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'order_id' => $validated['order_id'],
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

    private function createRazorpayOrder(float $total, string $receipt, int $userId): array
    {
        if (Setting::get('razorpay_enabled', '1') !== '1') {
            throw new \RuntimeException('razorpay_disabled');
        }

        $keyId = (string) Setting::get('razorpay_key_id', '');
        $keySecret = (string) Setting::get('razorpay_key_secret', '');

        if ($keyId === '' || $keySecret === '') {
            throw new \RuntimeException('razorpay_credentials_missing');
        }

        $amountInPaise = (int) round($total * 100);

        if ($amountInPaise <= 0) {
            throw new \RuntimeException('razorpay_invalid_amount');
        }

        if (class_exists(Api::class)) {
            try {
                $api = new Api($keyId, $keySecret);

                $order = $api->order->create([
                    'amount' => $amountInPaise,
                    'currency' => 'INR',
                    'receipt' => $receipt,
                    'notes' => [
                        'user_id' => (string) $userId,
                    ],
                ]);
            } catch (RazorpayError $exception) {
                throw new \RuntimeException('razorpay_order_failed: '.$exception->getMessage(), previous: $exception);
            }

            $payload = is_array($order->toArray()) ? $order->toArray() : [];
        } else {
            $response = Http::withBasicAuth($keyId, $keySecret)
                ->acceptJson()
                ->asJson()
                ->post('https://api.razorpay.com/v1/orders', [
                    'amount' => $amountInPaise,
                    'currency' => 'INR',
                    'receipt' => $receipt,
                    'notes' => [
                        'user_id' => (string) $userId,
                    ],
                ]);

            if ($response->failed()) {
                throw new \RuntimeException('razorpay_order_failed: '.$response->body());
            }

            $payload = $response->json();
        }

        if (! is_array($payload) || ! isset($payload['id']) || ! is_string($payload['id'])) {
            throw new \RuntimeException('razorpay_invalid_response');
        }

        return $payload;
    }

    private function fetchRazorpayOrderPayments(string $razorpayOrderId): array
    {
        if (Setting::get('razorpay_enabled', '1') !== '1') {
            throw new \RuntimeException('razorpay_disabled');
        }

        $keyId = (string) Setting::get('razorpay_key_id', '');
        $keySecret = (string) Setting::get('razorpay_key_secret', '');

        if ($keyId === '' || $keySecret === '') {
            throw new \RuntimeException('razorpay_credentials_missing');
        }

        $response = Http::withBasicAuth($keyId, $keySecret)
            ->acceptJson()
            ->get("https://api.razorpay.com/v1/orders/{$razorpayOrderId}/payments");

        if ($response->failed()) {
            throw new \RuntimeException('razorpay_payment_fetch_failed: '.$response->body());
        }

        $payload = $response->json();

        if (! is_array($payload) || ! array_key_exists('items', $payload) || ! is_array($payload['items'])) {
            throw new \RuntimeException('razorpay_invalid_response');
        }

        return $payload;
    }

    private function shouldSendOrderMail(Order $order): bool
    {
        $email = $order->user?->email;

        if (! is_string($email) || trim($email) === '') {
            return false;
        }

        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
