<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserAddressController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
    Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);
});

Route::get('/payment/config', [PaymentController::class, 'config']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

Route::get('/products/random', [ProductController::class, 'random']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/products/{product}/reviews', [ProductController::class, 'reviews']);

Route::middleware('auth:sanctum')->post('/products/{product}/reviews', [ProductController::class, 'storeReview']);

Route::middleware('auth:sanctum')->get('/wishlist', [WishlistController::class, 'index']);
Route::middleware('auth:sanctum')->post('/wishlist', [WishlistController::class, 'store']);
Route::middleware('auth:sanctum')->delete('/wishlist/{product}', [WishlistController::class, 'destroy']);

Route::middleware('auth:sanctum')->get('/cart', [CartController::class, 'index']);
Route::middleware('auth:sanctum')->post('/cart/items', [CartController::class, 'store']);
Route::middleware('auth:sanctum')->patch('/cart/items/{item}', [CartController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/cart/items/{item}', [CartController::class, 'destroy']);

Route::middleware('auth:sanctum')->get('/addresses', [UserAddressController::class, 'index']);
Route::middleware('auth:sanctum')->post('/addresses', [UserAddressController::class, 'store']);
Route::middleware('auth:sanctum')->patch('/addresses/{address}', [UserAddressController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/addresses/{address}', [UserAddressController::class, 'destroy']);

Route::middleware('auth:sanctum')->post('/orders/checkout', [OrderController::class, 'store']);
Route::middleware('auth:sanctum')->get('/orders', [OrderController::class, 'index']);
Route::middleware('auth:sanctum')->get('/orders/{order}', [OrderController::class, 'show']);
Route::middleware('auth:sanctum')->patch('/orders/{order}/payment', [OrderController::class, 'updatePayment']);
