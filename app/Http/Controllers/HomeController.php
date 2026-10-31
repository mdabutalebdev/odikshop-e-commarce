<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    // How many products load per "Load More" click on the home page grid.
    private const HOME_GRID_PER_PAGE = 12;

    public function index()
    {
        // For Odik Shop's layout (Left promo, Center slider, Right promo)
        $centerSlides = Banner::active()->ofType('hero')->orderBy('sort_order')->get();
        $promoBanners = Banner::active()->ofType('promo')->orderBy('sort_order')->get();
        $leftBanner = $promoBanners->first();
        $rightBanner = $promoBanners->skip(1)->first();

        // Categories with icons
        $categories = Category::active()->topLevel()->where('show_on_home', true)->withCount('products')->orderBy('sort_order')->get();

        // Home product sections — each driven by its own admin checkbox so the
        // shop owner can decide exactly which products appear in each section.
        $flashSale = Product::with(['category', 'images'])->active()->flashSale()->latest()->take(12)->get();
        $featured = Product::with(['category', 'images'])->active()->featured()->latest()->take(12)->get();
        $bestSelling = Product::with(['category', 'images'])->active()->bestSeller()->latest()->take(12)->get();
        $newArrival = Product::with(['category', 'images'])->active()->newArrival()->latest()->take(12)->get();

        $testimonials = Testimonial::active()->orderBy('sort_order')->get();

        // Paginated "All Products" grid at the bottom of the home page (Load More).
        $homeProducts = Product::with(['category', 'images'])->active()->latest()->paginate(self::HOME_GRID_PER_PAGE);

        return view('home', compact(
            'centerSlides',
            'categories',
            'flashSale',
            'featured',
            'bestSelling',
            'newArrival',
            'testimonials',
            'homeProducts'
        ));
    }

    /**
     * Returns the next page of the home "All Products" grid as rendered
     * product-card HTML — used by the Load More button (AJAX).
     */
    public function loadMore(Request $request)
    {
        $products = Product::with(['category', 'images'])->active()->latest()->paginate(self::HOME_GRID_PER_PAGE);

        return response()->json([
            'html' => view('partials.home-products', ['products' => $products])->render(),
            'hasMore' => $products->hasMorePages(),
        ]);
    }
}
