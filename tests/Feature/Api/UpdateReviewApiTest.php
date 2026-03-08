<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

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
