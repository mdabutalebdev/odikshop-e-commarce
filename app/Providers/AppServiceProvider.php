<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\SiteSetting;
use App\Services\Cart;
use App\Services\Wishlist;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Shared chrome data (nav, counts, settings) for the layout + partials.
        View::composer(
            ['components.layout', 'partials.header', 'partials.mobile-nav', 'partials.cart-drawer'],
            function ($view) {
                $view->with([
                    'navCategories' => Category::active()->inNav()->orderBy('sort_order')->get(),
                    'cartCount' => app(Cart::class)->count(),
                    'wishlistCount' => app(Wishlist::class)->count(),
                    'settings' => SiteSetting::getAll(),
                ]);
            }
        );

        // Product cards need to know which items are wishlisted.
        View::composer('components.product-card', function ($view) {
            $view->with('wishlistIds', app(Wishlist::class)->raw());
        });

        // Footer settings.
        View::composer('partials.footer', function ($view) {
            $view->with('settings', SiteSetting::getAll());
        });
    }
}
