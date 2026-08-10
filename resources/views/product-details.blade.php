<x-layout :title="$product->name">
    @php
        $gallery = collect([$product->main_image])->merge($product->images->pluck('path'))->filter()->unique()->values();
        $galleryUrls = $gallery->map(fn ($img) => image_url($img, $product->name));
        $reviews = $product->approvedReviews;
        $avg = $reviews->count() ? round($reviews->avg('rating'), 1) : $product->rating;
        $variants = collect($product->attributes ?? [])
            ->filter(fn ($g) => !empty($g['name']) && !empty(array_filter($g['options'] ?? [])))
            ->values();
    @endphp

    @php $variantNames = $variants->pluck('name')->values(); @endphp
    <div class="container-x py-4" x-data="{ qty: 1, options: {}, required: {{ $variantNames->toJson() }} }">
        {{-- Breadcrumb --}}
        <nav class="text-xs text-muted mb-3">
            <a href="{{ route('home') }}" class="hover:text-brand">Home</a>
            <span class="mx-1">/</span>
            <a href="{{ route('shop') }}" class="hover:text-brand">Shop</a>
            @if($product->category)
                <span class="mx-1">/</span>
                <a href="{{ route('shop', ['category' => $product->category->slug]) }}" class="hover:text-brand">{{ $product->category->name }}</a>
            @endif
            <span class="mx-1">/</span>
            <span class="text-ink">{{ $product->name }}</span>
        </nav>

        <div class="bg-white rounded-xl border border-line p-3 sm:p-5">
            <div class="grid md:grid-cols-2 gap-5 lg:gap-8">
                {{-- Gallery --}}
                <div x-data="{ idx: 0, imgs: {{ $galleryUrls->toJson() }} }" class="flex gap-3">
                    @if($gallery->count() > 1)
                        <div class="flex sm:flex-col gap-2 order-2 sm:order-1 overflow-x-auto no-scrollbar sm:max-h-[420px] sm:overflow-y-auto shrink-0">
                            <template x-for="(u, i) in imgs" :key="i">
                                <button type="button" @click="idx = i"
                                        class="w-14 h-14 rounded-lg border-2 overflow-hidden shrink-0 transition"
                                        :class="idx === i ? 'border-brand' : 'border-line hover:border-brand/40'">
                                    <img :src="u" class="w-full h-full object-cover">
                                </button>
                            </template>
                        </div>
                    @endif

                    <div class="relative flex-1 order-1 sm:order-2 rounded-xl border border-line overflow-hidden aspect-square bg-canvas">
                        @if($product->discount_percent > 0)
                            <span class="absolute top-3 left-3 z-10 bg-accent text-white text-[11px] font-bold px-2 py-1 rounded">-{{ $product->discount_percent }}%</span>
                        @endif
                        <img :src="imgs[idx]" alt="{{ $product->name }}" class="w-full h-full object-contain">

                        <template x-if="imgs.length > 1">
                            <div>
                                <button type="button" @click="idx = (idx - 1 + imgs.length) % imgs.length"
                                        class="absolute left-2 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/85 hover:bg-white grid place-items-center shadow text-ink">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>
                                <button type="button" @click="idx = (idx + 1) % imgs.length"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/85 hover:bg-white grid place-items-center shadow text-ink">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Details --}}
                <div>
                    <h1 class="text-lg sm:text-xl font-bold leading-snug">{{ $product->name }}</h1>

                    <div class="flex items-center gap-2 mt-2">
                        @php $r = (int) round($avg); @endphp
                        <span class="flex text-[11px]">
                            @for($s = 1; $s <= 5; $s++)
                                <i class="fa-solid fa-star {{ $s <= $r ? 'text-amber-400' : 'text-gray-300' }}"></i>
                            @endfor
                        </span>
                        <a href="#reviews" class="text-muted text-xs hover:text-brand">({{ $reviews->count() }} reviews)</a>
                        @if($product->stock > 0)
                            <span class="ml-1 text-[10px] font-semibold text-brand bg-brand-light px-2 py-0.5 rounded-full">In Stock</span>
                        @else
                            <span class="ml-1 text-[10px] font-semibold text-sale bg-red-50 px-2 py-0.5 rounded-full">Out of Stock</span>
                        @endif
                    </div>

                    {{-- Price box --}}
                    <div class="bg-brand-light rounded-xl p-3 mt-3 flex items-center gap-3 flex-wrap">
                        <span class="text-brand font-bold text-xl">{{ bdt($product->price) }}</span>
                        @if($product->old_price && $product->old_price > $product->price)
                            <span class="text-muted text-sm line-through">{{ bdt($product->old_price) }}</span>
                            <span class="bg-accent text-white text-[11px] font-bold px-2 py-1 rounded ml-auto">-{{ $product->discount_percent }}% OFF</span>
                        @endif
                    </div>

                    {{-- Variants --}}
                    @if($variants->isNotEmpty())
                        <div class="mt-4 space-y-3">
                            @foreach($variants as $group)
                                @php $gName = $group['name']; $opts = array_values(array_filter($group['options'] ?? [])); @endphp
                                <div>
                                    <div class="text-xs font-semibold mb-1.5">{{ $gName }}: <span class="text-muted font-normal" x-text="options['{{ $gName }}'] || 'নির্বাচন করুন'"></span></div>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($opts as $opt)
                                            <button type="button"
                                                    @click="options['{{ $gName }}'] = '{{ $opt }}'"
                                                    :class="options['{{ $gName }}'] === '{{ $opt }}' ? 'border-brand bg-brand text-white' : 'border-line hover:border-brand text-ink'"
                                                    class="px-3 h-9 rounded-lg border text-xs font-semibold transition bg-white">
                                                {{ $opt }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Quantity --}}
                    <div class="flex items-center gap-4 mt-4">
                        <span class="text-xs font-semibold">Quantity:</span>
                        <div class="flex items-center border border-line rounded-lg overflow-hidden">
                            <button type="button" @click="qty = Math.max(1, qty - 1)" class="w-9 h-9 grid place-items-center hover:bg-canvas">−</button>
                            <span class="w-10 h-9 grid place-items-center font-bold border-x border-line text-sm" x-text="qty"></span>
                            <button type="button" @click="qty++" class="w-9 h-9 grid place-items-center hover:bg-canvas">+</button>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <form method="POST" action="{{ route('cart.add', $product) }}" class="flex gap-2 mt-4"
                          @submit="
                              const missing = required.filter(n => !options[n]);
                              if (missing.length) { $event.preventDefault(); alert('অনুগ্রহ করে নির্বাচন করুন: ' + missing.join(', ')); }
                          ">
                        @csrf
                        <input type="hidden" name="quantity" :value="qty">
                        @foreach($variants as $g)
                            <input type="hidden" :name="'options[{{ $g['name'] }}]'" :value="options['{{ $g['name'] }}'] || ''">
                        @endforeach

                        <button type="submit" name="redirect" value=""
                                class="flex-1 h-11 rounded-lg border-2 border-brand text-brand text-sm font-bold hover:bg-brand-light transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-cart-plus"></i>Add to Cart
                        </button>
                        <button type="submit" name="redirect" value="checkout"
                                class="flex-1 h-11 btn-accent text-sm">
                            <i class="fa-solid fa-bolt"></i>Order Now
                        </button>
                        <button type="button" data-wishlist="{{ $product->id }}"
                                class="w-11 h-11 rounded-lg border border-line grid place-items-center hover:border-sale transition shrink-0" aria-label="Add to wishlist">
                            <i class="fa-regular fa-heart text-base text-sale"></i>
                        </button>
                    </form>

                    {{-- Meta --}}
                    <div class="mt-5 text-xs text-muted space-y-1.5 border-t border-line pt-3">
                        <div><span class="font-semibold text-ink">SKU:</span> {{ $product->sku ?? '—' }}</div>
                        @if($product->category)
                            <div><span class="font-semibold text-ink">Category:</span> {{ $product->category->name }}</div>
                        @endif
                        <div class="flex items-center gap-2 pt-1"><i class="fa-solid fa-clock text-brand"></i>Delivery Time 1-2 Days</div>
                        <div class="flex items-center gap-2"><i class="fa-solid fa-truck-fast text-brand"></i>Cash on Delivery all over Bangladesh</div>
                        <div class="flex items-center gap-2"><i class="fa-solid fa-rotate-left text-brand"></i>Easy 3-day return</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Description --}}
        <div class="bg-white rounded-xl border border-line p-5 mt-5">
            <h2 class="font-bold text-base mb-3 flex items-center gap-2"><i class="fa-solid fa-circle-info text-brand"></i>Product Description</h2>
            <div class="text-sm leading-relaxed text-ink/85 space-y-3
                        [&_p]:mb-2 [&_ul]:list-disc [&_ul]:pl-5 [&_ul]:space-y-1
                        [&_ol]:list-decimal [&_ol]:pl-5 [&_ol]:space-y-1
                        [&_strong]:text-ink [&_strong]:font-semibold
                        [&_h1]:text-lg [&_h1]:font-bold [&_h2]:text-base [&_h2]:font-bold
                        [&_h3]:text-sm [&_h3]:font-bold
                        [&_a]:text-brand [&_a:hover]:underline
                        [&_img]:rounded-lg [&_img]:my-3 [&_img]:max-w-full
                        [&_iframe]:w-full [&_iframe]:aspect-video [&_iframe]:rounded-lg [&_iframe]:my-3 [&_iframe]:h-auto">
                {!! $product->description !!}
            </div>
        </div>

        {{-- ===== Reviews ===== --}}
        <div id="reviews" class="bg-white rounded-xl border border-line p-5 mt-5">
            <div class="grid md:grid-cols-[260px_1fr] gap-6">
                {{-- Summary + form --}}
                <div>
                    <h2 class="font-bold text-base mb-2">Customer Reviews</h2>
                    <div class="flex items-center gap-3 mb-3">
                        <span class="text-3xl font-extrabold text-brand">{{ $avg }}</span>
                        <div>
                            <div class="flex text-amber-400 text-xs">
                                @for($s = 1; $s <= 5; $s++)<i class="fa-solid fa-star {{ $s <= round($avg) ? '' : 'text-gray-300' }}"></i>@endfor
                            </div>
                            <div class="text-[11px] text-muted mt-0.5">{{ $reviews->count() }} review(s)</div>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="bg-brand-light border border-brand/20 text-brand rounded-lg p-3 mb-3 text-sm"><i class="fa-solid fa-circle-check mr-1"></i>{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 text-sale rounded-lg p-3 mb-3 text-sm">
                            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('reviews.store') }}" class="space-y-3" x-data="{ rating: 5 }">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="rating" x-model="rating">
                        <div>
                            <label class="block text-xs font-medium mb-1">Your Rating</label>
                            <div class="flex gap-1 text-xl text-amber-400">
                                <template x-for="s in 5" :key="s">
                                    <button type="button" @click="rating = s"><i class="fa-solid fa-star" :class="s <= rating ? '' : 'text-gray-300'"></i></button>
                                </template>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1">Name <span class="text-sale">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="w-full h-9 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1">Email <span class="text-muted font-normal">(optional)</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" class="w-full h-9 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1">Review <span class="text-sale">*</span></label>
                            <textarea name="comment" rows="3" required class="w-full rounded-lg border border-line px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">{{ old('comment') }}</textarea>
                        </div>
                        <button type="submit" class="btn-brand h-10 px-6 w-full text-sm">Submit Review</button>
                    </form>
                </div>

                {{-- Review list --}}
                <div>
                    @forelse($reviews as $review)
                        <div class="border-b border-line pb-3 mb-3 last:border-0">
                            <div class="flex items-center gap-3 mb-1.5">
                                <span class="w-8 h-8 rounded-full bg-brand text-white grid place-items-center font-bold text-xs shrink-0">{{ mb_substr($review->name, 0, 1) }}</span>
                                <div>
                                    <div class="font-semibold text-sm">{{ $review->name }}</div>
                                    <div class="flex text-amber-400 text-[11px]">
                                        @for($s = 1; $s <= 5; $s++)<i class="fa-solid fa-star {{ $s <= $review->rating ? '' : 'text-gray-300' }}"></i>@endfor
                                    </div>
                                </div>
                                <span class="ml-auto text-[11px] text-muted">{{ $review->created_at->format('d M Y') }}</span>
                            </div>
                            <p class="text-xs text-ink/80 leading-relaxed">{{ $review->comment }}</p>
                        </div>
                    @empty
                        <div class="text-center text-muted py-8">
                            <i class="fa-regular fa-comment-dots text-3xl mb-2 opacity-30"></i>
                            <p class="text-sm">No reviews yet. Be the first to review this product!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Related --}}
        @if($related->isNotEmpty())
            <div class="mt-6">
                <h2 class="text-base font-extrabold mb-3 flex items-center gap-2"><span class="w-1.5 h-6 rounded-full bg-accent inline-block"></span>Related Products</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach($related as $rel)
                        <x-product-card :product="$rel" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layout>
