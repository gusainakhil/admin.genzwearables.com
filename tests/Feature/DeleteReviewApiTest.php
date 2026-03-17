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

it('allows a user to delete their own review and images', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $product = createActiveProduct();

    $imagePath = UploadedFile::fake()->image('review-delete.jpg')->store('reviews', 'public');

    $review = Review::query()->create([
        'product_id' => $product->id,
        'user_id' => $user->id,
        'rating' => 4,
        'comment' => 'Will delete this',
        'images' => [$imagePath],
    ]);

    Sanctum::actingAs($user);

    $response = $this->deleteJson("/api/products/{$product->id}/reviews/{$review->id}");

    $response
        ->assertSuccessful()
        ->assertJsonPath('status', true)
        ->assertJsonPath('message', 'Review deleted');

    $this->assertDatabaseMissing('reviews', [
        'id' => $review->id,
    ]);

    Storage::disk('public')->assertMissing($imagePath);
});

it('forbids a user from deleting another users review', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $product = createActiveProduct();

    $review = Review::query()->create([
        'product_id' => $product->id,
        'user_id' => $owner->id,
        'rating' => 5,
        'comment' => 'Owners review',
    ]);

    Sanctum::actingAs($otherUser);

    $response = $this->deleteJson("/api/products/{$product->id}/reviews/{$review->id}");

    $response->assertForbidden();

    $this->assertDatabaseHas('reviews', [
        'id' => $review->id,
    ]);
});

it('requires authentication to delete a review', function () {
    $owner = User::factory()->create();
    $product = createActiveProduct();

    $review = Review::query()->create([
        'product_id' => $product->id,
        'user_id' => $owner->id,
        'rating' => 5,
        'comment' => 'Owners review',
    ]);

    $response = $this->deleteJson("/api/products/{$product->id}/reviews/{$review->id}");

    $response->assertUnauthorized();

    $this->assertDatabaseHas('reviews', [
        'id' => $review->id,
    ]);
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
