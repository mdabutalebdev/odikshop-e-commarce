<x-admin-layout title="পণ্য ব্যবস্থাপনা">
    <div class="flex items-center justify-between mb-4 gap-3 flex-wrap">
        <form method="GET" class="relative flex-1 max-w-xs">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="পণ্য খুঁজুন..." class="w-full h-10 rounded-lg border border-line pl-9 pr-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand/30">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-muted text-sm"></i>
        </form>
        <a href="{{ route('admin.products.create') }}" class="btn-brand h-10 px-4"><i class="fa-solid fa-plus"></i>নতুন পণ্য</a>
    </div>

    <div class="bg-white rounded-xl border border-line overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-canvas text-muted text-left">
                    <tr>
                        <th class="px-4 py-3 font-semibold">পণ্য</th>
                        <th class="px-4 py-3 font-semibold">ক্যাটাগরি</th>
                        <th class="px-4 py-3 font-semibold">দাম</th>
                        <th class="px-4 py-3 font-semibold">স্টক</th>
                        <th class="px-4 py-3 font-semibold">ট্যাগ</th>
                        <th class="px-4 py-3 font-semibold text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($products as $product)
                        <tr class="hover:bg-canvas">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ image_url($product->main_image, $product->name) }}" class="w-10 h-10 rounded-lg object-cover bg-canvas shrink-0">
                                    <span class="font-semibold clamp-2 max-w-[200px]">{{ $product->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-muted">{{ $product->category?->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="font-bold text-brand">{{ bdt($product->price) }}</span>
                                @if($product->old_price)<span class="text-xs text-muted line-through ml-1">{{ bdt($product->old_price) }}</span>@endif
                            </td>
                            <td class="px-4 py-3">{{ $product->stock }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @if($product->is_flash_sale)<span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-sale/10 text-sale">Flash</span>@endif
                                    @if($product->is_featured)<span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-100 text-amber-700">Featured</span>@endif
                                    @if($product->is_best_seller)<span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-accent/10 text-accent">Best</span>@endif
                                    @if($product->is_new_arrival)<span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-brand-light text-brand">New</span>@endif
                                    @if(!$product->is_active)<span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-gray-200 text-gray-600">Inactive</span>@endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="w-8 h-8 grid place-items-center rounded-lg hover:bg-brand-light text-brand"><i class="fa-solid fa-pen"></i></a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('এই পণ্যটি মুছে ফেলতে চান?')">
                                        @csrf @method('DELETE')
                                        <button class="w-8 h-8 grid place-items-center rounded-lg hover:bg-red-50 text-sale"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-muted">কোনো পণ্য নেই।</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
</x-admin-layout>
