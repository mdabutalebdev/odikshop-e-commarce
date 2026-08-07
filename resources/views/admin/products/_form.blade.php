@php $editing = $product->exists; @endphp

<form method="POST" action="{{ $editing ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data" class="grid lg:grid-cols-[1fr_320px] gap-6">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="space-y-5">
        <div class="bg-white rounded-xl border border-line p-5 space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">পণ্যের নাম <span class="text-sale">*</span></label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">বিবরণ</label>
                <textarea name="description" rows="5" class="w-full rounded-lg border border-line px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">{{ old('description', $product->description) }}</textarea>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">বিক্রয় মূল্য (BDT) <span class="text-sale">*</span></label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">পুরাতন মূল্য (কাটা দাম)</label>
                    <input type="number" step="0.01" name="old_price" value="{{ old('old_price', $product->old_price) }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">স্টক <span class="text-sale">*</span></label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">রেটিং (০-৫)</label>
                    <input type="number" step="0.1" min="0" max="5" name="rating" value="{{ old('rating', $product->rating ?? 0) }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">রিভিউ সংখ্যা</label>
                    <input type="number" name="reviews_count" value="{{ old('reviews_count', $product->reviews_count ?? 0) }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-5">
        {{-- Image --}}
        <div class="bg-white rounded-xl border border-line p-5">
            <label class="block text-sm font-medium mb-2">পণ্যের ছবি</label>
            @if($product->main_image)
                <img src="{{ image_url($product->main_image, $product->name) }}" class="w-full aspect-square object-cover rounded-lg border border-line mb-3 bg-canvas">
            @endif
            <input type="file" name="image" accept="image/*" class="w-full text-sm mb-2 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-brand-light file:text-brand file:font-semibold">
            <label class="block text-xs text-muted mb-1 mt-2">অথবা ছবির URL</label>
            <input type="text" name="image_url" placeholder="https://..." class="w-full h-10 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
        </div>

        {{-- Category + flags --}}
        <div class="bg-white rounded-xl border border-line p-5 space-y-3">
            <div>
                <label class="block text-sm font-medium mb-1">ক্যাটাগরি</label>
                <select name="category_id" class="w-full h-11 rounded-lg border border-line px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand/30">
                    <option value="">— নির্বাচন করুন —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id) == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_flash_sale" value="1" @checked(old('is_flash_sale', $product->is_flash_sale)) class="accent-brand">ফ্ল্যাশ সেল</label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_popular" value="1" @checked(old('is_popular', $product->is_popular)) class="accent-brand">জনপ্রিয় পণ্য</label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured)) class="accent-brand">ফিচার্ড</label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $editing ? $product->is_active : true)) class="accent-brand">সক্রিয়</label>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="btn-brand h-11 flex-1">{{ $editing ? 'আপডেট করুন' : 'সেভ করুন' }}</button>
            <a href="{{ route('admin.products.index') }}" class="h-11 px-4 rounded-lg border border-line bg-white inline-flex items-center text-sm">বাতিল</a>
        </div>
    </div>
</form>
