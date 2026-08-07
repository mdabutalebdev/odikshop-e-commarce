<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->when($request->search, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categoryGroups = $this->categoryGroups();
        $brands = $this->brands();

        return view('admin.products.create', compact('categoryGroups', 'brands'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(4));

        if ($request->hasFile('main_image')) {
            $data['main_image'] = $request->file('main_image')->store('products', 'public');
        }
        
        if (isset($data['attributes']) && is_string($data['attributes'])) {
            $data['attributes'] = json_decode($data['attributes'], true);
        }

        $product = Product::create($data);

        $this->storeImages($request, $product);

        return redirect()->route('admin.products.index')->with('status', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        return redirect()->route('admin.products.edit', $product);
    }

    public function edit(Product $product)
    {
        $categoryGroups = $this->categoryGroups();
        $brands = $this->brands();
        $product->load('images');

        return view('admin.products.edit', compact('product', 'categoryGroups', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request);

        if ($request->hasFile('main_image')) {
            if ($product->main_image && !str_starts_with($product->main_image, 'http')) {
                Storage::disk('public')->delete($product->main_image);
            }
            $data['main_image'] = $request->file('main_image')->store('products', 'public');
        }
        
        if (isset($data['attributes']) && is_string($data['attributes'])) {
            $data['attributes'] = json_decode($data['attributes'], true);
        }

        $product->update($data);

        $this->storeImages($request, $product);

        return redirect()->route('admin.products.index')->with('status', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->main_image && !str_starts_with($product->main_image, 'http')) {
            Storage::disk('public')->delete($product->main_image);
        }

        foreach ($product->images as $image) {
            if (! str_starts_with($image->image, 'http')) {
                Storage::disk('public')->delete($image->image);
            }
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Product deleted successfully.');
    }

    public function destroyImage(Product $product, ProductImage $image)
    {
        abort_if($image->product_id !== $product->id, 404);

        if (! str_starts_with($image->image, 'http')) {
            Storage::disk('public')->delete($image->image);
        }

        $image->delete();

        return back()->with('status', 'Image removed.');
    }

    private function categoryGroups()
    {
        return Category::topLevel()->with('children')->orderBy('sort_order')->get();
    }

    private function brands()
    {
        return Brand::orderBy('name')->get();
    }

    private function storeImages(Request $request, Product $product): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $startOrder = $product->images()->max('sort_order') + 1;

        foreach ($request->file('images') as $index => $file) {
            $product->images()->create([
                'image' => $file->store('products', 'public'),
                'sort_order' => $startOrder + $index,
            ]);
        }
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'old_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'sku' => ['nullable', 'string', 'max:100'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'reviews_count' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_best_seller' => ['sometimes', 'boolean'],
            'is_new_arrival' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'main_image' => ['nullable', 'image', 'max:2048'],
            'attributes' => ['nullable'],
            'images' => ['nullable', 'array'],
            'images.*' => ['file', 'mimes:jpeg,jpg,png,webp,gif,mp4,mov,webm,ogv,ogg,m4v,avi,mkv,3gp', 'max:20480'],
        ]);

        foreach (['is_featured', 'is_best_seller', 'is_new_arrival', 'is_active'] as $flag) {
            $data[$flag] = $request->boolean($flag);
        }

        unset($data['images']);

        return $data;
    }
}
