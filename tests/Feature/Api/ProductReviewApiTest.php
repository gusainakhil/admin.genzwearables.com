<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('allows a user to submit a review with multiple images', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $product = createActiveProduct();

    Sanctum::actingAs($user);

    $response = $this->post("/api/products/{$product->id}/reviews", [
        'rating' => 5,
        'comment' => 'Excellent quality',
        'images' => [
            UploadedFile::fake()->image('review-1.jpg'),
            UploadedFile::fake()->image('review-2.jpg'),
        ],
    ], [
        'Accept' => 'application/json',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('status', true)
        ->assertJsonPath('message', 'Review submitted')
        ->assertJsonPath('data.rating', 5)
        ->assertJsonPath('data.comment', 'Excellent quality')
        ->assertJsonPath('data.user.id', $user->id)
        ->assertJsonCount(2, 'data.images');

    $review = Review::query()->firstOrFail();

    expect($review->images)->toHaveCount(2);

    foreach (($response->json('data.images') ?? []) as $image) {
        Storage::disk('public')->assertExists($image['path']);
    }
});

it('returns review images in the product reviews response', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'name' => 'Jane Reviewer',
    ]);
    $product = createActiveProduct();

    $imagePaths = [
        UploadedFile::fake()->image('stored-review-1.jpg')->store('reviews', 'public'),
        UploadedFile::fake()->image('stored-review-2.jpg')->store('reviews', 'public'),
    ];

    Review::query()->create([
        'product_id' => $product->id,
        'user_id' => $user->id,
        'rating' => 4,
        'comment' => 'Looks great',
        'images' => $imagePaths,
    ]);

    $response = $this->getJson("/api/products/{$product->id}/reviews");

    $response
        ->assertSuccessful()
        ->assertJsonPath('status', true)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.user.id', $user->id)
        ->assertJsonPath('data.0.user.name', 'Jane Reviewer')
        ->assertJsonPath('data.0.images.0.path', $imagePaths[0])
        ->assertJsonPath('data.0.images.1.path', $imagePaths[1]);
});

function createActiveProduct(): Product
{
    $category = Category::query()->create([
        'name' => 'T-Shirts',
        'slug' => 't-shirts-'.uniqid(),
        'status' => 'active',
    ]);

    return Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Test Tee',
        'slug' => 'test-tee-'.uniqid(),
        'base_price' => 1000,
        'status' => 'active',
    ]);
}
