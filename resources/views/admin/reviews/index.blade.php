<x-admin-layout title="Product Reviews">
    {{-- Status filter tabs --}}
    <div class="flex flex-wrap items-center gap-2 mb-4">
        @php
            $tabs = ['' => 'All ('.$counts['all'].')', 'pending' => 'Pending ('.$counts['pending'].')', 'approved' => 'Approved ('.$counts['approved'].')', 'rejected' => 'Rejected ('.$counts['rejected'].')'];
        @endphp
        @foreach($tabs as $key => $label)
            <a href="{{ route('admin.reviews.index', $key ? ['status' => $key] : []) }}"
               class="h-9 px-4 rounded-lg text-sm font-semibold inline-flex items-center transition {{ request('status') === ($key ?: null) ? 'bg-brand text-white' : 'bg-white border border-line hover:bg-canvas' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border border-line overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-canvas text-muted text-left">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Product</th>
                        <th class="px-4 py-3 font-semibold">Reviewer</th>
                        <th class="px-4 py-3 font-semibold">Rating</th>
                        <th class="px-4 py-3 font-semibold min-w-[220px]">Review</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($reviews as $review)
                        <tr class="hover:bg-canvas align-top">
                            <td class="px-4 py-3">
                                @if($review->product)
                                    <a href="{{ route('product.show', $review->product) }}" target="_blank" class="font-medium text-brand hover:underline">{{ $review->product->name }}</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $review->name }}</div>
                                <div class="text-xs text-muted">{{ $review->email ?: '—' }}</div>
                                <div class="text-xs text-muted">{{ $review->created_at->format('d M Y') }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="flex text-amber-400 text-xs whitespace-nowrap">
                                    @for($s = 1; $s <= 5; $s++)<i class="fa-solid fa-star {{ $s <= $review->rating ? '' : 'text-gray-300' }}"></i>@endfor
                                </span>
                            </td>
                            <td class="px-4 py-3 text-ink/80">{{ $review->comment }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $badge = ['pending' => 'bg-amber-100 text-amber-700', 'approved' => 'bg-brand-light text-brand', 'rejected' => 'bg-red-50 text-sale'][$review->status] ?? 'bg-gray-100 text-gray-600';
                                @endphp
                                <span class="text-xs font-bold px-2 py-1 rounded-full {{ $badge }}">{{ \App\Models\ProductReview::STATUS_LABELS[$review->status] ?? $review->status }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    @if($review->status !== 'approved')
                                        <form method="POST" action="{{ route('admin.reviews.status', $review) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="approved">
                                            <button class="w-8 h-8 grid place-items-center rounded-lg hover:bg-brand-light text-brand" title="Approve"><i class="fa-solid fa-check"></i></button>
                                        </form>
                                    @endif
                                    @if($review->status !== 'rejected')
                                        <form method="POST" action="{{ route('admin.reviews.status', $review) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button class="w-8 h-8 grid place-items-center rounded-lg hover:bg-red-50 text-sale" title="Reject"><i class="fa-solid fa-xmark"></i></button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Delete this review permanently?')">
                                        @csrf @method('DELETE')
                                        <button class="w-8 h-8 grid place-items-center rounded-lg hover:bg-red-50 text-sale" title="Delete"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-muted">No reviews found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $reviews->links() }}</div>
</x-admin-layout>
