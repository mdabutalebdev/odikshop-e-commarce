<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\SiteSetting;
use App\Services\Cart;
use App\Services\SteadfastService;
use App\Services\Tracking\MetaConversionsApi;
use App\Services\Tracking\Pixel;
use App\Services\Tracking\TrackingSettings;
use App\Services\Wishlist;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Tracking has to be a singleton: a controller records an event and the
        // layout partial renders it later in the same request, so both must be
        // holding the same instance or the event is silently dropped.
        $this->app->singleton(TrackingSettings::class);
        $this->app->singleton(MetaConversionsApi::class);
        $this->app->singleton(Pixel::class);

        // Shared so the settings rows are read once per request.
        $this->app->singleton(SteadfastService::class);
    }

    public function boot(): void
    {
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

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
