<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('type')->orderBy('sort_order')->get();

        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create', ['banner' => new Banner]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data = $this->handleImage($request, $data);
        $data['is_active'] = $request->boolean('is_active');

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'ব্যানার যোগ হয়েছে।');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $data = $this->validated($request);
        $data = $this->handleImage($request, $data, $banner);
        $data['is_active'] = $request->boolean('is_active');

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'ব্যানার আপডেট হয়েছে।');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();

        return back()->with('success', 'ব্যানার মুছে ফেলা হয়েছে।');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:1000',
            'type' => 'required|in:hero,left,right',
            'sort_order' => 'nullable|integer',
            'image' => 'nullable|image|max:4096',
            'image_url' => 'nullable|string|max:1000',
        ]);
    }

    private function handleImage(Request $request, array $data, ?Banner $banner = null): array
    {
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('banners', 'public');
        } elseif ($request->filled('image_url')) {
            $data['image'] = $request->input('image_url');
        } else {
            $data['image'] = $banner?->image ?? 'https://placehold.co/1200x420/008060/ffffff?text=Banner';
        }

        unset($data['image_url']);

        return $data;
    }
}
