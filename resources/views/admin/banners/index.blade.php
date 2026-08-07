<x-admin-layout title="ব্যানার">
    <div class="flex items-center justify-between mb-4">
        <p class="text-muted text-sm">{{ $banners->count() }} টি ব্যানার</p>
        <a href="{{ route('admin.banners.create') }}" class="btn-brand h-10 px-4"><i class="fa-solid fa-plus"></i>নতুন ব্যানার</a>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
        @forelse($banners as $banner)
            <div class="bg-white rounded-xl border border-line overflow-hidden">
                <img src="{{ image_url($banner->image, 'Banner') }}" class="w-full h-40 object-cover bg-canvas">
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold">{{ $banner->title ?? 'শিরোনামহীন' }}</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $banner->type === 'hero' ? 'bg-brand-light text-brand' : 'bg-amber-100 text-amber-700' }}">{{ $banner->type }}</span>
                    </div>
                    <div class="text-xs text-muted mt-1 truncate">{{ $banner->link ?? '—' }}</div>
                    <div class="flex items-center gap-2 mt-3">
                        <a href="{{ route('admin.banners.edit', $banner) }}" class="h-8 px-3 rounded-lg bg-brand-light text-brand text-sm font-semibold inline-flex items-center gap-1"><i class="fa-solid fa-pen"></i>এডিট</a>
                        <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" onsubmit="return confirm('মুছে ফেলতে চান?')">
                            @csrf @method('DELETE')
                            <button class="h-8 px-3 rounded-lg bg-red-50 text-sale text-sm font-semibold inline-flex items-center gap-1"><i class="fa-solid fa-trash-can"></i>মুছুন</button>
                        </form>
                        @unless($banner->is_active)<span class="text-xs text-muted ml-auto">নিষ্ক্রিয়</span>@endunless
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-2 bg-white rounded-xl border border-line p-10 text-center text-muted">কোনো ব্যানার নেই।</div>
        @endforelse
    </div>
</x-admin-layout>
