<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns product suggestions filtered by category query', function () {
    $targetCategory = Category::query()->create([
        'name' => 'Smart Watches',
        'slug' => 'smart-watches',
        'status' => 'active',
    ]);

    $otherCategory = Category::query()->create([
        'name' => 'Headphones',
        'slug' => 'headphones',
        'status' => 'active',
    ]);

    foreach (range(1, 11) as $index) {
        Product::query()->create([
            'category_id' => $targetCategory->id,
            'name' => 'Smart Watch '.$index,
            'slug' => 'smart-watch-'.$index.'-'.uniqid(),
            'base_price' => 999,
            'status' => 'active',
        ]);
    }

    $otherProduct = Product::query()->create([
        'category_id' => $otherCategory->id,
        'name' => 'Headphone Pro',
        'slug' => 'headphone-pro-'.uniqid(),
        'base_price' => 799,
        'status' => 'active',
    ]);

    $response = $this->getJson('/api/products/search?q=smart');

    $response
        ->assertSuccessful()
        ->assertJsonPath('status', true)
        ->assertJsonCount(10, 'data')
        ->assertJsonMissing(['id' => $otherProduct->id]);
});

it('validates that category search query is required', function () {
    $response = $this->getJson('/api/products/search?q=');

    $response
        ->assertStatus(422)
        ->assertJsonPath('status', false)
        ->assertJsonPath('message', 'Search query is required.');
});
