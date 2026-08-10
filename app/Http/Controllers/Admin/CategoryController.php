<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('children')->withCount('products')->topLevel()->orderBy('sort_order')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $category = new Category();
        $parents = Category::topLevel()->orderBy('name_en')->get();

        return view('admin.categories.create', compact('category', 'parents'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $data['slug'] = $this->uniqueSlug($data['name_en'], $data['name_bn'] ?? null);

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('status', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        $parents = Category::topLevel()->where('id', '!=', $category->id)->orderBy('name_en')->get();

        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            if ($category->image && ! str_starts_with($category->image, 'http')) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        // Keep the existing slug so category URLs stay stable; only mint one
        // if this category somehow has none.
        if (empty($category->slug)) {
            $data['slug'] = $this->uniqueSlug($data['name_en'], $data['name_bn'] ?? null, $category->id);
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('status', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')->with('status', 'Category deleted successfully.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_bn' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'icon' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
            'show_in_nav' => ['sometimes', 'boolean'],
            'show_on_home' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['show_in_nav'] = $request->boolean('show_in_nav');
        $data['show_on_home'] = $request->boolean('show_on_home');
        $data['sort_order'] = $request->integer('sort_order');
        unset($data['image']);

        return $data;
    }

    /**
     * Build a URL-safe, unique slug from the English name, falling back to the
     * Bangla name (transliterated) and finally a random token — because
     * Str::slug() returns an empty string for pure Bangla input.
     */
    private function uniqueSlug(string $nameEn, ?string $nameBn = null, ?int $ignoreId = null): string
    {
        $base = Str::slug($nameEn);

        if ($base === '' && $nameBn) {
            $base = Str::slug(Str::ascii($nameBn));
        }

        if ($base === '') {
            $base = 'category-'.Str::lower(Str::random(6));
        }

        $slug = $base;
        $i = 2;

        while (Category::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
