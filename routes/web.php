<?php

use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyDetailController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Categories
    Route::resource('categories', CategoryController::class);

    // Products
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/images', [ProductController::class, 'addImage'])->name('products.images.store');
    Route::delete('product-images/{image}', [ProductController::class, 'deleteImage'])->name('products.images.destroy');
    Route::post('products/{product}/variants', [ProductController::class, 'addVariant'])->name('products.variants.store');
    Route::patch('product-variants/{variant}/status', [ProductController::class, 'updateVariantStatus'])->name('products.variants.status');
    Route::delete('product-variants/{variant}', [ProductController::class, 'deleteVariant'])->name('products.variants.destroy');

    // Orders
    Route::resource('orders', OrderController::class)->only(['index', 'show']);
    Route::get('orders/{order}/invoice', [OrderController::class, 'printInvoice'])->name('orders.print-invoice');
    Route::get('orders/{order}/parcel-sheet', [OrderController::class, 'printParcelSheet'])->name('orders.print-parcel-sheet');
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::patch('orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('orders.payment-status');
    Route::post('orders/{order}/shipment', [OrderController::class, 'addShipment'])->name('orders.shipment');

    // Customers
    Route::resource('customers', CustomerController::class)->only(['index', 'show']);
    Route::patch('customers/{customer}/status', [CustomerController::class, 'updateStatus'])->name('customers.status');

    // Coupons
    Route::resource('coupons', CouponController::class);

    // Attributes
    Route::get('sizes', [AttributeController::class, 'sizesIndex'])->name('sizes.index');
    Route::post('sizes', [AttributeController::class, 'storeSize'])->name('sizes.store');
    Route::delete('sizes/{size}', [AttributeController::class, 'destroySize'])->name('sizes.destroy');

    Route::get('colors', [AttributeController::class, 'colorsIndex'])->name('colors.index');
    Route::post('colors', [AttributeController::class, 'storeColor'])->name('colors.store');
    Route::delete('colors/{color}', [AttributeController::class, 'destroyColor'])->name('colors.destroy');

    // Reviews
    Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');

    // Company Details
    Route::get('company-details', [CompanyDetailController::class, 'index'])->name('company-details.index');
    Route::post('company-details', [CompanyDetailController::class, 'update'])->name('company-details.update');
});
