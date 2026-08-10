<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/search/suggest', [ShopController::class, 'suggest'])->name('search.suggest');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'bn'])) {
        session()->put('locale', $locale);
    }
    return back();
})->name('lang.switch');

Route::get('/track', [OrderTrackingController::class, 'index'])->name('track');
Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
Route::get('/page/{slug}', [PageController::class, 'show'])->name('page');

/*
|--------------------------------------------------------------------------
| Cart
|--------------------------------------------------------------------------
*/
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::get('/partial', [CartController::class, 'partial'])->name('partial');
    Route::post('/add/{product:id}', [CartController::class, 'add'])->name('add');
    Route::patch('/update/{key}', [CartController::class, 'update'])->name('update');
    Route::post('/remove', [CartController::class, 'remove'])->name('remove');
});

/*
|--------------------------------------------------------------------------
| Wishlist
|--------------------------------------------------------------------------
*/
Route::prefix('wishlist')->name('wishlist.')->group(function () {
    Route::get('/', [WishlistController::class, 'index'])->name('index');
    Route::post('/toggle', [WishlistController::class, 'toggle'])->name('toggle');
    Route::post('/remove', [WishlistController::class, 'remove'])->name('remove');
});

/*
|--------------------------------------------------------------------------
| Checkout
|--------------------------------------------------------------------------
*/
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
    Route::post('/webhook', [CheckoutController::class, 'webhook'])->name('webhook');
    Route::get('/{order}/confirmation', [CheckoutController::class, 'confirmation'])->name('confirmation');
    Route::get('/{order}/pay', [CheckoutController::class, 'pay'])->name('pay');
    Route::get('/{order}/callback', [CheckoutController::class, 'callback'])->name('callback');
    Route::get('/{order}/cancel', [CheckoutController::class, 'cancel'])->name('cancel');
});

/*
|--------------------------------------------------------------------------
| Auth & Account
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->middleware('guest')->name('register.form');
Route::get('/account', [AccountController::class, 'index'])->name('account');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest')->name('login.attempt');
Route::post('/register', [RegisterController::class, 'register'])->middleware('guest')->name('register');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/account/update', [AccountController::class, 'update'])->middleware('auth')->name('account.update');
