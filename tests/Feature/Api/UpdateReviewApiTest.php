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

it('allows a user to replace their review images', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $product = createActiveProduct();

    $existingImage = UploadedFile::fake()->image('existing-review.jpg')->store('reviews', 'public');

    $review = Review::query()->create([
        'product_id' => $product->id,
        'user_id' => $user->id,
        'rating' => 3,
        'comment' => 'Initial comment',
        'images' => [$existingImage],
    ]);

    Sanctum::actingAs($user);

    $response = $this->patch("/api/products/{$product->id}/reviews/{$review->id}", [
        'rating' => 5,
        'comment' => 'Updated comment',
        'images' => [
            UploadedFile::fake()->image('updated-review-1.jpg'),
            UploadedFile::fake()->image('updated-review-2.jpg'),
        ],
    ], [
        'Accept' => 'application/json',
    ]);

    $response
        ->assertSuccessful()
        ->assertJsonPath('status', true)
        ->assertJsonPath('message', 'Review updated')
        ->assertJsonPath('data.rating', 5)
        ->assertJsonPath('data.comment', 'Updated comment')
        ->assertJsonCount(2, 'data.images');

    Storage::disk('public')->assertMissing($existingImage);

    foreach (($response->json('data.images') ?? []) as $image) {
        Storage::disk('public')->assertExists($image['path']);
    }

    expect($review->fresh()->images)->toHaveCount(2);
});

it('allows a user to update their own review', function () {
    $user = User::factory()->create();
    $product = createActiveProduct();

    $review = Review::query()->create([
        'product_id' => $product->id,
        'user_id' => $user->id,
        'rating' => 3,
        'comment' => 'Initial comment',
    ]);

    Sanctum::actingAs($user);

    $response = $this->patchJson("/api/products/{$product->id}/reviews/{$review->id}", [
        'rating' => 5,
        'comment' => 'Updated comment',
    ]);

    $response
        ->assertSuccessful()
        ->assertJsonPath('status', true)
        ->assertJsonPath('message', 'Review updated')
        ->assertJsonPath('data.rating', 5)
        ->assertJsonPath('data.comment', 'Updated comment');

    $this->assertDatabaseHas('reviews', [
        'id' => $review->id,
        'user_id' => $user->id,
        'rating' => 5,
        'comment' => 'Updated comment',
    ]);
});

it('forbids a user from updating another users review', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $product = createActiveProduct();

    $review = Review::query()->create([
        'product_id' => $product->id,
        'user_id' => $owner->id,
        'rating' => 4,
        'comment' => 'Owner comment',
    ]);

    Sanctum::actingAs($otherUser);

    $response = $this->patchJson("/api/products/{$product->id}/reviews/{$review->id}", [
        'rating' => 2,
        'comment' => 'Hacked comment',
    ]);

    $response->assertForbidden();

    $this->assertDatabaseHas('reviews', [
        'id' => $review->id,
        'rating' => 4,
        'comment' => 'Owner comment',
    ]);
});

it('requires authentication to update a review', function () {
    $owner = User::factory()->create();
    $product = createActiveProduct();

    $review = Review::query()->create([
        'product_id' => $product->id,
        'user_id' => $owner->id,
        'rating' => 4,
        'comment' => 'Owner comment',
    ]);

    $response = $this->patchJson("/api/products/{$product->id}/reviews/{$review->id}", [
        'rating' => 1,
    ]);

    $response->assertUnauthorized();
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
