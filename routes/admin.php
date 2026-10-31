<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\SteadfastController;
use App\Http\Controllers\Admin\TrackingController;
use Illuminate\Support\Facades\Route;

Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('banners', BannerController::class)->except('show');

    Route::resource('products', ProductController::class);
    Route::delete('products/{product}/images/{image}', [ProductController::class, 'destroyImage'])->name('products.images.destroy');
    Route::post('editor-image', [ProductController::class, 'uploadEditorImage'])->name('editor.image');

    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('orders/bulk-print', [OrderController::class, 'bulkPrint'])->name('orders.bulk_print');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

    Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::patch('reviews/{review}/status', [ReviewController::class, 'updateStatus'])->name('reviews.status');
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::get('contact-messages', [\App\Http\Controllers\Admin\ContactMessageController::class, 'index'])->name('contact_messages.index');
    Route::delete('contact-messages/{contactMessage}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'destroy'])->name('contact_messages.destroy');

    Route::get('tracking', [TrackingController::class, 'index'])->name('tracking.index');
    Route::put('tracking', [TrackingController::class, 'update'])->name('tracking.update');
    Route::post('tracking/test-capi', [TrackingController::class, 'testCapi'])->name('tracking.test_capi');

    Route::get('steadfast', [SteadfastController::class, 'index'])->name('steadfast.index');
    Route::put('steadfast', [SteadfastController::class, 'update'])->name('steadfast.update');
    Route::post('steadfast/ping', [SteadfastController::class, 'ping'])->name('steadfast.ping');
    Route::post('steadfast/balance', [SteadfastController::class, 'balance'])->name('steadfast.balance');
    Route::post('steadfast/track', [SteadfastController::class, 'track'])->name('steadfast.track');
    Route::post('steadfast/token', [SteadfastController::class, 'generateToken'])->name('steadfast.token');
    Route::post('orders/{order}/steadfast/book', [SteadfastController::class, 'bookOrder'])->name('steadfast.book');
    Route::post('orders/{order}/steadfast/refresh', [SteadfastController::class, 'refreshOrder'])->name('steadfast.refresh');

    Route::get('settings', [SiteSettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SiteSettingController::class, 'update'])->name('settings.update');
});
