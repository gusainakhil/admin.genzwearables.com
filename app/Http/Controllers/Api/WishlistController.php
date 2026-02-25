<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $items = Wishlist::with(['product.images' => function ($q) {
            $q->orderByDesc('is_primary');
        }, 'product.category', 'product.reviews.user'])
            ->where('user_id', $request->user()->id)
            ->get()
            ->map(function (Wishlist $wishlist) {
                $product = $wishlist->product;
                $images = $product?->images?->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'path' => $image->image,
                        'url' => asset('storage/'.$image->image),
                        'is_primary' => (bool) $image->is_primary,
                    ];
                }) ?? collect();

                return [
                    'id' => $wishlist->id,
                    'product' => $product ? [
                        'id' => $product->id,
                        'category_id' => $product->category_id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'short_description' => $product->short_description,
                        'brand' => $product->brand,
                        'base_price' => $product->base_price,
                        'gender' => $product->gender,
                        'status' => $product->status,
                        'category' => $product->category,
                        'images' => $images,
                        'reviews' => $product->reviews->map(function ($review) {
                            return [
                                'id' => $review->id,
                                'rating' => $review->rating,
                                'comment' => $review->comment,
                                'user' => $review->user ? [
                                    'id' => $review->user->id,
                                    'name' => $review->user->name,
                                ] : null,
                                'created_at' => $review->created_at,
                            ];
                        }),
                    ] : null,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $wishlist = Wishlist::firstOrCreate([
            'user_id' => $request->user()->id,
            'product_id' => $validated['product_id'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Added to wishlist',
            'data' => $wishlist,
        ], 201);
    }

    public function destroy(Request $request, Product $product)
    {
        Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->delete();

        return response()->json([
            'status' => true,
            'message' => 'Removed from wishlist',
        ]);
    }
}
