@php $editing = $category->exists; @endphp

<form method="POST" action="{{ $editing ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="bg-white rounded-xl border border-line p-5 max-w-lg space-y-4">
    @csrf
    @if($editing) @method('PUT') @endif

    <div>
        <label class="block text-sm font-medium mb-1">Name (English) <span class="text-sale">*</span></label>
        <input type="text" name="name_en" value="{{ old('name_en', $category->name_en) }}" required class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
        @error('name_en')<span class="text-sale text-xs">{{ $message }}</span>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">নাম (Bangla)</label>
        <input type="text" name="name_bn" value="{{ old('name_bn', $category->name_bn) }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
        @error('name_bn')<span class="text-sale text-xs">{{ $message }}</span>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">আইকন (FontAwesome ক্লাস)</label>
        <input type="text" name="icon" value="{{ old('icon', $category->icon) }}" placeholder="fa-solid fa-plug" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
        <p class="text-xs text-muted mt-1">উদাহরণ: fa-solid fa-plug, fa-solid fa-shirt</p>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">অর্ডার (sort)</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
    </div>
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="show_in_nav" value="1" @checked(old('show_in_nav', $editing ? $category->show_in_nav : true)) class="accent-brand">নেভিগেশনে দেখান</label>
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $editing ? $category->is_active : true)) class="accent-brand">সক্রিয়</label>

    <div class="flex gap-2 pt-2">
        <button type="submit" class="btn-brand h-11 px-6">{{ $editing ? 'আপডেট' : 'সেভ' }}</button>
        <a href="{{ route('admin.categories.index') }}" class="h-11 px-4 rounded-lg border border-line inline-flex items-center text-sm">বাতিল</a>
    </div>
</form>
