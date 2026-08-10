<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load('images', 'category', 'approvedReviews');

        $related = Product::active()
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->take(6)
            ->get();

        if ($related->count() < 6) {
            $related = $related->merge(
                Product::active()->where('id', '!=', $product->id)
                    ->whereNotIn('id', $related->pluck('id'))
                    ->take(6 - $related->count())
                    ->get()
            );
        }

        return view('product-details', compact('product', 'related'));
    }
}
