<x-layout :title="$product->name">
    <div class="container-x py-5" x-data="{ qty: 1 }">
        {{-- Breadcrumb --}}
        <nav class="text-xs text-muted mb-4">
            <a href="{{ route('home') }}" class="hover:text-brand">হোম</a>
            <span class="mx-1">/</span>
            <a href="{{ route('shop') }}" class="hover:text-brand">শপ</a>
            @if($product->category)
                <span class="mx-1">/</span>
                <a href="{{ route('shop', ['category' => $product->category->slug]) }}" class="hover:text-brand">{{ $product->category->name }}</a>
            @endif
            <span class="mx-1">/</span>
            <span class="text-ink">{{ $product->name }}</span>
        </nav>

        <div class="bg-white rounded-xl border border-line p-4 sm:p-6">
            <div class="grid md:grid-cols-2 gap-6 lg:gap-10">
                {{-- Gallery + zoom --}}
                <div x-data="{ main: '{{ image_url($product->main_image, $product->name) }}', zoom: 1 }">
                    <div class="relative rounded-xl border border-line overflow-hidden aspect-square bg-canvas">
                        @if($product->discount_percent > 0)
                            <span class="absolute top-3 left-3 z-10 bg-sale text-white text-xs font-bold px-2 py-1 rounded">-{{ $product->discount_percent }}%</span>
                        @endif
                        <img :src="main" :style="`transform: scale(${zoom})`" alt="{{ $product->name }}" class="w-full h-full object-contain transition-transform duration-200">

                        {{-- Zoom controls --}}
                        <div class="absolute bottom-3 right-3 flex flex-col gap-2">
                            <button type="button" @click="zoom = Math.min(3, zoom + 0.25)" class="w-10 h-10 rounded-full bg-brand text-white grid place-items-center shadow hover:bg-brand-dark" aria-label="জুম ইন"><i class="fa-solid fa-plus"></i></button>
                            <button type="button" @click="zoom = Math.max(1, zoom - 0.25)" class="w-10 h-10 rounded-full bg-brand text-white grid place-items-center shadow hover:bg-brand-dark" aria-label="জুম আউট"><i class="fa-solid fa-minus"></i></button>
                            <button type="button" @click="zoom = 1" class="w-10 h-10 rounded-full bg-brand text-white grid place-items-center shadow hover:bg-brand-dark" aria-label="রিসেট"><i class="fa-solid fa-rotate"></i></button>
                        </div>
                    </div>

                    @if($product->images->isNotEmpty())
                        <div class="flex gap-2 mt-3 overflow-x-auto no-scrollbar">
                            <button type="button" @click="main = '{{ image_url($product->main_image, $product->name) }}'; zoom = 1" class="w-16 h-16 rounded-lg border border-line overflow-hidden shrink-0">
                                <img src="{{ image_url($product->main_image, $product->name) }}" class="w-full h-full object-cover">
                            </button>
                            @foreach($product->images as $img)
                                <button type="button" @click="main = '{{ image_url($img->path) }}'; zoom = 1" class="w-16 h-16 rounded-lg border border-line overflow-hidden shrink-0">
                                    <img src="{{ image_url($img->path) }}" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Details --}}
                <div>
                    <h1 class="text-2xl lg:text-3xl font-extrabold leading-snug">{{ $product->name }}</h1>

                    <div class="flex items-center gap-2 mt-2">
                        @php $r = (int) round($product->rating); @endphp
                        <span class="flex text-sm">
                            @for($s = 1; $s <= 5; $s++)
                                <i class="fa-solid fa-star {{ $s <= $r ? 'text-amber-400' : 'text-gray-300' }}"></i>
                            @endfor
                        </span>
                        <span class="text-muted text-sm">({{ $product->reviews_count }} রিভিউ)</span>
                        @if($product->stock > 0)
                            <span class="ml-1 text-xs font-semibold text-brand bg-brand-light px-2 py-0.5 rounded-full">স্টকে আছে</span>
                        @else
                            <span class="ml-1 text-xs font-semibold text-sale bg-red-50 px-2 py-0.5 rounded-full">স্টক নেই</span>
                        @endif
                    </div>

                    {{-- Price box (mint) --}}
                    <div class="bg-brand-light rounded-xl p-4 mt-4 flex items-center gap-3 flex-wrap">
                        <span class="text-brand font-extrabold text-3xl">{{ bdt($product->price) }}</span>
                        @if($product->old_price && $product->old_price > $product->price)
                            <span class="text-muted text-lg line-through">{{ bdt($product->old_price) }}</span>
                            <span class="bg-sale text-white text-xs font-bold px-2 py-1 rounded ml-auto">-{{ $product->discount_percent }}% ছাড়</span>
                        @endif
                    </div>

                    {{-- Quantity --}}
                    <div class="flex items-center gap-4 mt-6">
                        <span class="text-sm font-semibold">পরিমাণ:</span>
                        <div class="flex items-center border border-line rounded-lg overflow-hidden">
                            <button type="button" @click="qty = Math.max(1, qty - 1)" class="w-11 h-11 grid place-items-center hover:bg-canvas text-lg">−</button>
                            <span class="w-12 h-11 grid place-items-center font-bold border-x border-line" x-text="qty"></span>
                            <button type="button" @click="qty++" class="w-11 h-11 grid place-items-center hover:bg-canvas text-lg">+</button>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3 mt-6">
                        <button type="button" data-add-cart="{{ $product->id }}" :data-qty="qty"
                                class="flex-1 h-12 rounded-lg border-2 border-brand text-brand font-bold hover:bg-brand-light transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-cart-plus"></i>কার্টে যুক্ত করুন
                        </button>
                        <a :href="'{{ route('checkout.index') }}?buy={{ $product->id }}&qty=' + qty"
                           class="flex-1 h-12 btn-brand font-bold text-base">
                            <i class="fa-solid fa-bolt"></i>অর্ডার করুন
                        </a>
                        <button type="button" data-wishlist="{{ $product->id }}"
                                class="w-12 h-12 rounded-lg border border-line grid place-items-center hover:border-sale transition shrink-0" aria-label="উইশলিস্ট">
                            <i class="fa-regular fa-heart text-lg text-sale"></i>
                        </button>
                    </div>

                    {{-- Meta --}}
                    <div class="mt-6 text-sm text-muted space-y-2 border-t border-line pt-4">
                        <div><span class="font-semibold text-ink">SKU:</span> {{ $product->sku ?? '—' }}</div>
                        @if($product->category)
                            <div><span class="font-semibold text-ink">ক্যাটাগরি:</span> {{ $product->category->name }}</div>
                        @endif
                        <div class="flex items-center gap-2 pt-1"><i class="fa-solid fa-truck-fast text-brand"></i>সারা দেশে ক্যাশ অন ডেলিভারি</div>
                        <div class="flex items-center gap-2"><i class="fa-solid fa-rotate-left text-brand"></i>৭ দিনের সহজ রিটার্ন</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Description --}}
        <div class="bg-white rounded-xl border border-line p-6 mt-6">
            <h2 class="font-bold text-lg mb-3 flex items-center gap-2"><i class="fa-solid fa-circle-info text-brand"></i>পণ্যটির বিবরণ</h2>
            <p class="text-sm leading-relaxed text-ink/80 whitespace-pre-line">{{ $product->description }}</p>
        </div>

        {{-- Related --}}
        @if($related->isNotEmpty())
            <div class="mt-8">
                <h2 class="text-lg font-extrabold mb-3">সম্পর্কিত পণ্যসমূহ</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach($related as $rel)
                        <x-product-card :product="$rel" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layout>
