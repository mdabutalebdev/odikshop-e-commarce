<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class TopSellingController extends Controller
{
    public function edit()
    {
        $products = Product::where('is_active', true)
            ->with('category')
            ->orderByDesc('top_selling')
            ->orderBy('name')
            ->get()
            ->map(fn ($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category->name ?? '—',
                'price' => (float) $product->price,
                'image' => image_url($product->main_image, urlencode($product->name)),
                'selected' => (bool) $product->top_selling,
                'order' => 0,
            ])
            ->values();

        return view('admin.top-selling.edit', compact('products'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'products' => ['nullable', 'array'],
            'products.*' => ['integer', 'exists:products,id'],
            'order' => ['nullable', 'array'],
        ]);

        $ids = collect($data['products'] ?? [])->map(fn ($id) => (int) $id)->unique()->values();

        // Reset everything, then flag the chosen products as top selling.
        Product::where('top_selling', true)->whereNotIn('id', $ids)->update([
            'top_selling' => false,
        ]);

        Product::whereIn('id', $ids)->update([
            'top_selling' => true,
        ]);

        return redirect()->route('admin.top-selling.edit')->with('status', 'Top Selling products updated.');
    }
}
