<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\CompanyDetailController;
use App\Http\Controllers\Api\PolicyPageController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReturnRequestController;
use App\Http\Controllers\Api\SidebarBannerController;
use App\Http\Controllers\Api\ShipmentController;
use App\Http\Controllers\Api\UserAddressController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register/otp', [AuthController::class, 'requestRegistrationOtp']);
    Route::post('/register', [AuthController::class, 'completeRegistration']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
    Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);
});

Route::get('/payment/config', [PaymentController::class, 'config']);
Route::get('/company-details', [CompanyDetailController::class, 'show']);
Route::get('/sidebar-banners', [SidebarBannerController::class, 'index']);
Route::get('/policies', [PolicyPageController::class, 'index']);
Route::get('/policies/{type}', [PolicyPageController::class, 'show']);
Route::get('/shipment/shiprocket/config', [ShipmentController::class, 'config']);
Route::post('/shipment/shiprocket/serviceability', [ShipmentController::class, 'serviceability']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

Route::get('/products/random', [ProductController::class, 'random']);
Route::get('/products/search', [ProductController::class, 'search']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/products/{product}/reviews', [ProductController::class, 'reviews']);

Route::middleware('auth:sanctum')->post('/products/{product}/reviews', [ProductController::class, 'storeReview']);
Route::middleware('auth:sanctum')->patch('/products/{product}/reviews/{review}', [ProductController::class, 'updateReview']);

Route::middleware('auth:sanctum')->get('/wishlist', [WishlistController::class, 'index']);
Route::middleware('auth:sanctum')->post('/wishlist', [WishlistController::class, 'store']);
Route::middleware('auth:sanctum')->delete('/wishlist/{product}', [WishlistController::class, 'destroy']);

Route::middleware('auth:sanctum')->get('/cart', [CartController::class, 'index']);
Route::middleware('auth:sanctum')->post('/cart/items', [CartController::class, 'store']);
Route::middleware('auth:sanctum')->patch('/cart/items/{item}', [CartController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/cart/items/{item}', [CartController::class, 'destroy']);
Route::get('/coupons/apply', function () {
    return response()->json([
        'status' => false,
        'message' => 'Use POST /api/coupons/apply with Bearer token and JSON body {"code":"COUPON_CODE"}.',
    ], 405);
});
Route::middleware('auth:sanctum')->post('/coupons/apply', [CouponController::class, 'apply']);

Route::middleware('auth:sanctum')->get('/addresses', [UserAddressController::class, 'index']);
Route::middleware('auth:sanctum')->post('/addresses', [UserAddressController::class, 'store']);
Route::middleware('auth:sanctum')->patch('/addresses/{address}', [UserAddressController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/addresses/{address}', [UserAddressController::class, 'destroy']);

Route::middleware('auth:sanctum')->post('/orders/checkout', [OrderController::class, 'store']);
Route::middleware('auth:sanctum')->get('/orders', [OrderController::class, 'index']);
Route::middleware('auth:sanctum')->get('/orders/{order}/returns', [ReturnRequestController::class, 'orderReturns']);
Route::middleware('auth:sanctum')->get('/orders/{order}', [OrderController::class, 'show']);
Route::middleware('auth:sanctum')->get('/orders/{orderReference}/invoice/download', [OrderController::class, 'downloadInvoice']);
Route::middleware('auth:sanctum')->patch('/orders/{order}/payment', [OrderController::class, 'updatePayment']);
Route::middleware('auth:sanctum')->post('/orders/payment/sync', [OrderController::class, 'syncRazorpayPayment']);
Route::middleware('auth:sanctum')->get('/returns', [ReturnRequestController::class, 'index']);
Route::middleware('auth:sanctum')->get('/returns/{returnRequest}', [ReturnRequestController::class, 'show']);
Route::middleware('auth:sanctum')->post('/returns', [ReturnRequestController::class, 'store']);
