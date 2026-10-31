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
        <nav class="flex flex-wrap items-center gap-y-1 text-xs text-muted mb-3">
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

        <div class="bg-white rounded-xl border border-line p-3 sm:p-5 w-full min-w-0">
            <div class="grid md:grid-cols-2 gap-5 lg:gap-8 min-w-0">
                {{-- Gallery --}}
                <div x-data="{ idx: 0, fullscreen: false, imgs: {{ $galleryUrls->toJson() }} }" class="flex flex-col gap-3 min-w-0">
                    {{-- Main Image --}}
                    <div class="relative w-full rounded-xl border border-line overflow-hidden aspect-square bg-canvas cursor-pointer group min-w-0" @click="fullscreen = true">
                        @if($product->discount_percent > 0)
                            <span class="absolute top-3 left-3 z-10 bg-accent text-white text-[11px] font-bold px-2 py-1 rounded">-{{ $product->discount_percent }}%</span>
                        @endif
                        <img :src="imgs[idx]" alt="{{ $product->name }}" class="w-full h-full object-contain">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition flex items-center justify-center">
                            <i class="fa-solid fa-magnifying-glass-plus text-3xl text-white opacity-0 group-hover:opacity-100 transition drop-shadow-md"></i>
                        </div>

                        <template x-if="imgs.length > 1">
                            <div>
                                <button type="button" @click.stop="idx = (idx - 1 + imgs.length) % imgs.length"
                                        class="absolute left-2 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/85 hover:bg-white grid place-items-center shadow text-ink">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>
                                <button type="button" @click.stop="idx = (idx + 1) % imgs.length"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/85 hover:bg-white grid place-items-center shadow text-ink">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>
                            </div>
                        </template>
                    </div>

                    {{-- Fullscreen Modal --}}
                    <template x-teleport="body">
                        <div x-show="fullscreen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4 sm:p-8">
                            <button type="button" @click="fullscreen = false" class="absolute top-4 right-4 sm:top-8 sm:right-8 w-10 h-10 bg-white/10 hover:bg-white/30 text-white rounded-full flex items-center justify-center text-xl transition"><i class="fa-solid fa-xmark"></i></button>
                            
                            <img :src="imgs[idx]" class="max-w-full max-h-full object-contain" @click.away="fullscreen = false">
                            
                            <template x-if="imgs.length > 1">
                                <div>
                                    <button type="button" @click="idx = (idx - 1 + imgs.length) % imgs.length"
                                            class="absolute left-4 sm:left-8 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 hover:bg-white/30 text-white grid place-items-center text-xl transition">
                                        <i class="fa-solid fa-chevron-left"></i>
                                    </button>
                                    <button type="button" @click="idx = (idx + 1) % imgs.length"
                                            class="absolute right-4 sm:right-8 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 hover:bg-white/30 text-white grid place-items-center text-xl transition">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- Thumbnails --}}
                    @if($gallery->count() > 1)
                        <div class="relative group/thumb mt-2" x-data="{
                                scrollLeft() { $refs.thumbList.scrollBy({ left: -150, behavior: 'smooth' }); },
                                scrollRight() { $refs.thumbList.scrollBy({ left: 150, behavior: 'smooth' }); }
                            }">
                            {{-- Left Arrow --}}
                            <button type="button" @click="scrollLeft()" class="absolute left-0 top-1/2 -translate-y-1/2 z-10 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/90 shadow grid place-items-center opacity-0 group-hover/thumb:opacity-100 transition disabled:opacity-0" >
                                <i class="fa-solid fa-chevron-left text-xs sm:text-sm text-ink"></i>
                            </button>
                            
                            <div x-ref="thumbList" class="flex gap-2 overflow-x-auto snap-x snap-mandatory pb-1" style="scrollbar-width: none; -ms-overflow-style: none;">
                                <style>.group\\/thumb .overflow-x-auto::-webkit-scrollbar { display: none; }</style>
                                <template x-for="(u, i) in imgs" :key="i">
                                    <button type="button" @click="idx = i"
                                            class="shrink-0 w-16 sm:w-20 aspect-square rounded-lg border-2 overflow-hidden transition snap-center"
                                            :class="idx === i ? 'border-brand' : 'border-line hover:border-brand/40'">
                                        <img :src="u" class="w-full h-full object-cover">
                                    </button>
                                </template>
                            </div>

                            {{-- Right Arrow --}}
                            <button type="button" @click="scrollRight()" class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/90 shadow grid place-items-center opacity-0 group-hover/thumb:opacity-100 transition disabled:opacity-0" >
                                <i class="fa-solid fa-chevron-right text-xs sm:text-sm text-ink"></i>
                            </button>
                        </div>
                    @endif
                </div>

                {{-- Details --}}
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl font-bold leading-snug break-words">{{ $product->name }}</h1>

                    @if($product->short_description)
                        <p class="text-muted text-sm sm:text-base mt-1.5 leading-relaxed break-words">{{ $product->short_description }}</p>
                    @endif

                    <div class="flex items-center gap-2 mt-2">
                        @php $r = (int) round($avg); @endphp
                        <span class="flex text-[11px]">
                            @for($s = 1; $s <= 5; $s++)
                                <i class="fa-solid fa-star {{ $s <= $r ? 'text-amber-400' : 'text-gray-300' }}"></i>
                            @endfor
                        </span>
                        <a href="#reviews" class="text-muted text-xs hover:text-brand">({{ $reviews->count() }} reviews)</a>
                        @if($product->stock > 0)
                            <span class="ml-1 text-xs font-semibold text-brand bg-brand-light px-2.5 py-1 rounded-full">In Stock</span>
                        @else
                            <span class="ml-1 text-xs font-semibold text-sale bg-red-50 px-2.5 py-1 rounded-full">Out of Stock</span>
                        @endif
                    </div>

                    {{-- Price box --}}
                    <div class="bg-brand-light rounded-xl p-3 mt-3 flex items-center gap-3 flex-wrap">
                        <span class="text-brand font-bold text-2xl">{{ bdt($product->price) }}</span>
                        @if($product->old_price && $product->old_price > $product->price)
                            <span class="text-muted text-base line-through">{{ bdt($product->old_price) }}</span>
                            <span class="bg-accent text-white text-[11px] font-bold px-2 py-1 rounded ml-auto">-{{ $product->discount_percent }}% OFF</span>
                        @endif
                    </div>

                    {{-- Variants --}}
                    @if($variants->isNotEmpty())
                        @php
                            // Common colour names → CSS colour, so a "Color" variant renders as swatches.
                            $colorMap = [
                                'black' => '#111111', 'white' => '#ffffff', 'red' => '#ef4444', 'blue' => '#2563eb',
                                'green' => '#16a34a', 'yellow' => '#eab308', 'orange' => '#f97316', 'purple' => '#9333ea',
                                'pink' => '#ec4899', 'gray' => '#6b7280', 'grey' => '#6b7280', 'brown' => '#92400e',
                                'gold' => '#d4af37', 'silver' => '#cbd5e1', 'navy' => '#1e3a8a', 'maroon' => '#7f1d1d',
                                'teal' => '#14b8a6', 'cyan' => '#06b6d4', 'sky blue' => '#38bdf8', 'sky' => '#38bdf8',
                                'light blue' => '#93c5fd', 'dark blue' => '#1e40af', 'beige' => '#e8dcc4',
                                'কালো' => '#111111', 'সাদা' => '#ffffff', 'লাল' => '#ef4444', 'নীল' => '#2563eb',
                                'সবুজ' => '#16a34a', 'হলুদ' => '#eab308', 'কমলা' => '#f97316', 'গোলাপি' => '#ec4899',
                                'ধূসর' => '#6b7280', 'বাদামি' => '#92400e',
                            ];
                            $lightColors = ['white', 'সাদা', 'yellow', 'হলুদ', 'beige', 'silver', 'gold'];
                            $colorGroups = ['color', 'colour', 'কালার', 'কালার ফ্যামিলি', 'রং', 'রঙ'];
                        @endphp
                        <div class="mt-4 space-y-3">
                            @foreach($variants as $group)
                                @php
                                    $gName = $group['name'];
                                    $opts = array_values(array_filter($group['options'] ?? []));
                                    $isColor = in_array(mb_strtolower(trim($gName)), $colorGroups, true);
                                @endphp
                                <div>
                                    <div class="text-sm font-semibold mb-1.5">নির্বাচন করুন: <span class="text-brand" x-text="options['{{ $gName }}']"></span></div>

                                    @if($isColor)
                                        {{-- Colour swatches (turn colourful, selected gets a ring + check) --}}
                                        <div class="flex flex-wrap gap-2.5">
                                            @foreach($opts as $opt)
                                                @php
                                                    $key = mb_strtolower(trim($opt));
                                                    $hex = $colorMap[$key] ?? null;
                                                    $isLight = in_array($key, $lightColors, true);
                                                @endphp
                                                <button type="button"
                                                        @click="options['{{ $gName }}'] = '{{ $opt }}'"
                                                        :class="options['{{ $gName }}'] === '{{ $opt }}' ? 'ring-2 ring-accent ring-offset-2' : 'ring-1 ring-line hover:ring-accent'"
                                                        class="relative w-9 h-9 rounded-full transition shrink-0"
                                                        style="background-color: {{ $hex ?? '#e5e7eb' }};"
                                                        title="{{ $opt }}" aria-label="{{ $opt }}">
                                                    @unless($hex)
                                                        <span class="absolute inset-0 grid place-items-center text-[9px] font-bold text-ink">{{ mb_substr($opt, 0, 3) }}</span>
                                                    @endunless
                                                    <span x-show="options['{{ $gName }}'] === '{{ $opt }}'" x-cloak class="absolute inset-0 grid place-items-center">
                                                        <i class="fa-solid fa-check text-xs {{ $isLight ? 'text-ink' : 'text-white' }}"></i>
                                                    </span>
                                                </button>
                                            @endforeach
                                        </div>
                                    @else
                                        {{-- Text options (Size etc.) --}}
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($opts as $opt)
                                                <button type="button"
                                                        @click="options['{{ $gName }}'] = '{{ $opt }}'"
                                                        :class="options['{{ $gName }}'] === '{{ $opt }}' ? 'border-accent bg-accent text-white' : 'border-line hover:border-accent text-ink'"
                                                        class="px-3 h-9 rounded-lg border text-sm font-semibold transition bg-white">
                                                    {{ $opt }}
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Actions & Quantity --}}
                    <form method="POST" action="{{ route('cart.add', $product) }}" class="grid grid-cols-2 gap-1 sm:gap-2 mt-5"
                          @submit="
                              const missing = required.filter(n => !options[n]);
                              if (missing.length) { $event.preventDefault(); alert('অনুগ্রহ করে নির্বাচন করুন: ' + missing.join(', ')); }
                          ">
                        @csrf
                        <input type="hidden" name="quantity" :value="qty">
                        @foreach($variants as $g)
                            <input type="hidden" :name="'options[{{ $g['name'] }}]'" :value="options['{{ $g['name'] }}'] || ''">
                        @endforeach

                        {{-- 1. Quantity --}}
                        <div class="flex items-center justify-center sm:justify-between border border-line rounded-lg overflow-hidden h-11 bg-white px-1 sm:px-3">
                            <span class="hidden sm:inline-block text-[13px] font-semibold text-muted">Qty.</span>
                            <div class="flex items-center h-full">
                                <button type="button" @click="qty = Math.max(1, qty - 1)" class="w-7 h-full grid place-items-center hover:bg-canvas text-lg">−</button>
                                <span class="w-6 h-full grid place-items-center font-bold text-sm" x-text="qty"></span>
                                <button type="button" @click="qty++" class="w-7 h-full grid place-items-center hover:bg-canvas text-lg">+</button>
                            </div>
                        </div>

                        {{-- 2. Add to Cart --}}
                        <button type="submit" name="redirect" value=""
                                class="h-11 rounded-lg border-2 border-brand text-brand text-xs sm:text-base font-bold hover:bg-brand-light transition flex items-center justify-center gap-1 sm:gap-1.5 px-1 sm:px-0">
                            <i class="fa-solid fa-cart-plus"></i><span class="whitespace-nowrap">Add to Cart</span>
                        </button>

                        {{-- 3. WhatsApp Order --}}
                        @php 
                            $siteSettings = \App\Models\SiteSetting::getAll();
                            $waPhone = !empty($siteSettings['whatsapp']) ? $siteSettings['whatsapp'] : ($siteSettings['phone'] ?? ''); 
                        @endphp
                        <a href="https://api.whatsapp.com/send?phone={{ preg_replace('/[^0-9+]/', '', $waPhone) }}&text={{ urlencode('I want to order: ' . $product->name . ' - ' . route('product.show', $product)) }}" target="_blank"
                           style="background-color: #25D366;"
                           class="h-11 rounded-lg text-white hover:opacity-90 text-xs sm:text-base font-bold transition flex items-center justify-center gap-1 sm:gap-1.5 px-1 sm:px-0">
                            <i class="fa-brands fa-whatsapp text-sm sm:text-lg"></i><span class="whitespace-nowrap">WhatsApp Order</span>
                        </a>

                        {{-- 4. Order Now --}}
                        <button type="submit" name="redirect" value="checkout"
                                class="h-11 btn-accent text-xs sm:text-base flex items-center justify-center gap-1 sm:gap-1.5 px-1 sm:px-0">
                            <i class="fa-solid fa-bolt"></i><span class="whitespace-nowrap">Order Now</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Description --}}
        <div class="bg-white rounded-xl border border-line p-5 mt-5">
            <h2 class="font-bold text-base mb-3 flex items-center gap-2"><i class="fa-solid fa-circle-info text-brand"></i>Product Description</h2>
            <div class="text-base leading-relaxed text-ink/85 space-y-3 overflow-x-auto break-words
                        [&_p]:mb-2 [&_ul]:list-disc [&_ul]:pl-5 [&_ul]:space-y-1
                        [&_ol]:list-decimal [&_ol]:pl-5 [&_ol]:space-y-1
                        [&_strong]:text-ink [&_strong]:font-semibold
                        [&_h1]:text-lg [&_h1]:font-bold [&_h2]:text-base [&_h2]:font-bold
                        [&_h3]:text-sm [&_h3]:font-bold
                        [&_a]:text-brand [&_a:hover]:underline
                        [&_img]:rounded-lg [&_img]:my-3 [&_img]:max-w-full
                        [&_iframe]:w-full [&_iframe]:aspect-video [&_iframe]:rounded-lg [&_iframe]:my-3 [&_iframe]:h-auto
                        [&_table]:w-full [&_table]:block [&_table]:overflow-x-auto">
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
                            <p class="text-xs text-ink/80 leading-relaxed break-words">{{ $review->comment }}</p>
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
