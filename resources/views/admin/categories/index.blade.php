<x-admin-layout title="ক্যাটাগরি">
    <div class="flex items-center justify-between mb-4">
        <p class="text-muted text-sm">{{ $categories->count() }} টি ক্যাটাগরি</p>
        <a href="{{ route('admin.categories.create') }}" class="btn-brand h-10 px-4"><i class="fa-solid fa-plus"></i>নতুন ক্যাটাগরি</a>
    </div>

    <div class="bg-white rounded-xl border border-line overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-canvas text-muted text-left">
                    <tr>
                        <th class="px-4 py-3 font-semibold">নাম</th>
                        <th class="px-4 py-3 font-semibold">slug</th>
                        <th class="px-4 py-3 font-semibold">পণ্য</th>
                        <th class="px-4 py-3 font-semibold">নেভে দেখান</th>
                        <th class="px-4 py-3 font-semibold text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-canvas">
                            <td class="px-4 py-3"><span class="font-semibold flex items-center gap-2"><i class="{{ $cat->icon ?? 'fa-solid fa-tag' }} text-brand w-5"></i>{{ $cat->name_en }} <span class="text-xs font-normal text-muted">({{ $cat->name_bn }})</span></span></td>
                            <td class="px-4 py-3 text-muted">{{ $cat->slug }}</td>
                            <td class="px-4 py-3">{{ $cat->products_count }}</td>
                            <td class="px-4 py-3">
                                @if($cat->show_in_nav)<span class="text-xs font-bold text-brand"><i class="fa-solid fa-check"></i></span>@else<span class="text-muted">—</span>@endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.categories.edit', $cat) }}" class="w-8 h-8 grid place-items-center rounded-lg hover:bg-brand-light text-brand"><i class="fa-solid fa-pen"></i></a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('মুছে ফেলতে চান?')">
                                        @csrf @method('DELETE')
                                        <button class="w-8 h-8 grid place-items-center rounded-lg hover:bg-red-50 text-sale"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-10 text-center text-muted">কোনো ক্যাটাগরি নেই।</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
