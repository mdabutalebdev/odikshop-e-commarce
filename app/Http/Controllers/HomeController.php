<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index()
    {
        // For Odik Shop's layout (Left promo, Center slider, Right promo)
        $centerSlides = Banner::active()->ofType('hero')->orderBy('sort_order')->get();
        $promoBanners = Banner::active()->ofType('promo')->orderBy('sort_order')->get();
        $leftBanner = $promoBanners->first();
        $rightBanner = $promoBanners->skip(1)->first();

        // Categories with icons
        $categories = Category::active()->topLevel()->where('show_on_home', true)->withCount('products')->orderBy('sort_order')->get();

        // Odik Shop expects $flashSale and $popular.
        // We'll map Raimart's 'featured' to 'flashSale' and 'bestSeller' to 'popular'.
        $flashSale = Product::with(['category', 'images'])->active()->featured()->latest()->take(10)->get();
        $popular = Product::with(['category', 'images'])->active()->bestSeller()->latest()->take(12)->get();
        
        $brands = Brand::active()->onHome()->orderBy('sort_order')->orderBy('name')->get();
        $testimonials = Testimonial::active()->orderBy('sort_order')->get();

        return view('home', compact(
            'centerSlides',
            'leftBanner',
            'rightBanner',
            'categories',
            'flashSale',
            'popular',
            'brands',
            'testimonials'
        ));
    }
}
