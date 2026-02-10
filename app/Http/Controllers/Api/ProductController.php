<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 20);

        $query = Product::query()
            ->where('status', 'active')
            ->with([
                'category',
                'images' => function ($q) {
                    $q->orderByDesc('is_primary');
                },
            ]);

        if ($request->filled('category_id')) {
            $categoryId = (int) $request->query('category_id');
            $categoryIds = [$categoryId];

            $category = Category::with('children')->find($categoryId);
            if ($category) {
                $categoryIds = array_merge(
                    $categoryIds,
                    $category->children->pluck('id')->all()
                );
            }

            $query->whereIn('category_id', $categoryIds);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->query('gender'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate($perPage);

        $products->getCollection()->transform(function (Product $product) {
            return $this->transformProduct($product);
        });

        return response()->json([
            'status' => true,
            'data' => $products,
        ]);
    }

    public function show(Product $product)
    {
        if ($product->status !== 'active') {
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $product->load([
            'category',
            'images' => function ($q) {
                $q->orderByDesc('is_primary');
            },
            'variants.size',
            'variants.color',
            'reviews.user',
        ]);

        return response()->json([
            'status' => true,
            'data' => $this->transformProduct($product, true, true),
        ]);
    }

    public function reviews(Product $product)
    {
        if ($product->status !== 'active') {
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $reviews = Review::with('user')
            ->where('product_id', $product->id)
            ->latest()
            ->get()
            ->map(function (Review $review) {
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
            });

        return response()->json([
            'status' => true,
            'data' => $reviews,
        ]);
    }

    public function storeReview(Request $request, Product $product)
    {
        if ($product->status !== 'active') {
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        $review = Review::create([
            'product_id' => $product->id,
            'user_id' => $request->user()->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Review submitted',
            'data' => $review,
        ], 201);
    }

    private function transformProduct(Product $product, bool $includeVariants = false, bool $includeReviews = false): array
    {
        $images = $product->images->map(function ($image) {
            return [
                'id' => $image->id,
                'path' => $image->image,
                'url' => asset('storage/'.$image->image),
                'is_primary' => (bool) $image->is_primary,
            ];
        });

        $data = [
            'id' => $product->id,
            'category_id' => $product->category_id,
            'name' => $product->name,
            'slug' => $product->slug,
            'short_description' => $product->short_description,
            'description' => $product->description,
            'brand' => $product->brand,
            'base_price' => $product->base_price,
            'gender' => $product->gender,
            'is_custom' => (bool) $product->is_custom,
            'status' => $product->status,
            'category' => $product->category,
            'images' => $images,
        ];

        if ($includeVariants) {
            $data['variants'] = $product->variants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'sku' => $variant->sku,
                    'price' => $variant->price,
                    'stock_qty' => $variant->stock_qty,
                    'status' => $variant->status,
                    'size' => $variant->size,
                    'color' => $variant->color,
                ];
            });
        }

        if ($includeReviews) {
            $data['reviews'] = $product->reviews->map(function ($review) {
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
            });
        }

        return $data;
    }
}
