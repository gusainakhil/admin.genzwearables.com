<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCartItemRequest;
use App\Http\Requests\Api\UpdateCartItemRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $cart = Cart::with([
            'items.productVariant.product.images' => function ($q) {
                $q->orderByDesc('is_primary');
            },
            'items.productVariant.size',
            'items.productVariant.color',
        ])
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $cart) {
            return response()->json([
                'status' => true,
                'data' => [
                    'id' => null,
                    'items' => [],
                ],
            ]);
        }

        $items = $cart->items->map(function (CartItem $item) {
            $variant = $item->productVariant;
            $product = $variant?->product;
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
                'quantity' => $item->quantity,
                'product_variant' => $variant ? [
                    'id' => $variant->id,
                    'product_id' => $variant->product_id,
                    'price' => $variant->price,
                    'stock_qty' => $variant->stock_qty,
                    'status' => $variant->status,
                    'size' => $variant->size,
                    'color' => $variant->color,
                    'product' => $product ? [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'brand' => $product->brand,
                        'base_price' => $product->base_price,
                        'images' => $images,
                    ] : null,
                ] : null,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $cart->id,
                'items' => $items,
            ],
        ]);
    }

    public function store(StoreCartItemRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $quantity = $validated['quantity'] ?? 1;

        $variant = ProductVariant::query()->find($validated['product_variant_id']);

        if (! $variant) {
            return response()->json([
                'status' => false,
                'message' => 'Product variant not found',
            ], 404);
        }

        $cart = Cart::firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_variant_id', $validated['product_variant_id'])
            ->first();

        $wasCreated = false;

        $requestedQuantity = $cartItem ? $cartItem->quantity + $quantity : $quantity;
        $availableQuantity = (int) $variant->stock_qty;

        if ($requestedQuantity > $availableQuantity) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient stock',
                'data' => [
                    'available_qty' => $availableQuantity,
                    'requested_qty' => $requestedQuantity,
                ],
            ], 422);
        }

        if ($cartItem) {
            $cartItem->quantity = $requestedQuantity;
            $cartItem->save();
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_variant_id' => $validated['product_variant_id'],
                'quantity' => $requestedQuantity,
            ]);
            $wasCreated = true;
        }

        $cartItem->load(['productVariant.product', 'productVariant.size', 'productVariant.color']);

        return response()->json([
            'status' => true,
            'message' => $wasCreated ? 'Added to cart' : 'Cart updated',
            'data' => [
                'id' => $cartItem->id,
                'quantity' => $cartItem->quantity,
                'product_variant' => $cartItem->productVariant ? [
                    'id' => $cartItem->productVariant->id,
                    'product_id' => $cartItem->productVariant->product_id,
                    'price' => $cartItem->productVariant->price,
                    'stock_qty' => $cartItem->productVariant->stock_qty,
                    'status' => $cartItem->productVariant->status,
                    'size' => $cartItem->productVariant->size,
                    'color' => $cartItem->productVariant->color,
                    'product' => $cartItem->productVariant->product,
                ] : null,
            ],
        ], $wasCreated ? 201 : 200);
    }

    public function update(UpdateCartItemRequest $request, CartItem $item): JsonResponse
    {
        $validated = $request->validated();

        $item->loadMissing('productVariant');
        $availableQuantity = (int) ($item->productVariant?->stock_qty ?? 0);

        if ($validated['quantity'] > $availableQuantity) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient stock',
                'data' => [
                    'available_qty' => $availableQuantity,
                    'requested_qty' => $validated['quantity'],
                ],
            ], 422);
        }

        $item->quantity = $validated['quantity'];
        $item->save();

        $item->load(['productVariant.product', 'productVariant.size', 'productVariant.color']);

        return response()->json([
            'status' => true,
            'message' => 'Cart item updated',
            'data' => [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'product_variant' => $item->productVariant ? [
                    'id' => $item->productVariant->id,
                    'product_id' => $item->productVariant->product_id,
                    'price' => $item->productVariant->price,
                    'stock_qty' => $item->productVariant->stock_qty,
                    'status' => $item->productVariant->status,
                    'size' => $item->productVariant->size,
                    'color' => $item->productVariant->color,
                    'product' => $item->productVariant->product,
                ] : null,
            ],
        ]);
    }

    public function destroy(Request $request, CartItem $item): JsonResponse
    {
        $item->loadMissing('cart');

        if ($item->cart->user_id !== $request->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Not authorized',
            ], 403);
        }

        $item->delete();

        return response()->json([
            'status' => true,
            'message' => 'Cart item removed',
        ]);
    }
}
