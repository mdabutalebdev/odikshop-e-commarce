<x-layout :title="$title">
    @php
        $collections = [
            'flash_sale' => 'Flash Sale',
            'featured' => 'Featured',
            'best_seller' => 'Best Selling',
            'new_arrival' => 'New Arrival',
        ];
        $hasFilters = $selectedCategories->isNotEmpty() || $selectedFlags->isNotEmpty()
            || request()->hasAny(['min_price', 'max_price', 'in_stock', 'out_of_stock']);
    @endphp

    {{-- Page header --}}
    <section class="bg-white border-b border-line">
        <div class="container-x py-6">
            <nav class="text-xs text-muted mb-1">
                <a href="{{ route('home') }}" class="hover:text-brand">Home</a>
                <span class="mx-1">/</span>
                <span class="text-ink">{{ $title }}</span>
            </nav>
            <h1 class="text-2xl font-extrabold">{{ $title }}</h1>
            <p class="text-muted text-sm mt-1">Explore our collection of genuine, quality products.</p>
        </div>
    </section>

    <div class="container-x py-6 grid lg:grid-cols-[270px_1fr] gap-6" x-data="{ filtersOpen: false }">
        {{-- ===== Sidebar filters ===== --}}
        <aside class="lg:block" :class="filtersOpen ? 'block' : 'hidden'">
            <form method="GET" action="{{ route('shop') }}" class="bg-white rounded-xl border border-line divide-y divide-line overflow-hidden sticky top-24">
                @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif

                <div class="flex items-center justify-between p-4">
                    <h3 class="font-bold flex items-center gap-2"><i class="fa-solid fa-sliders text-brand"></i>Filters</h3>
                    @if($hasFilters)
                        <a href="{{ route('shop') }}" class="text-xs text-sale font-semibold hover:underline">Clear All</a>
                    @endif
                </div>

                {{-- Price Range --}}
                <div class="p-4"
                     x-data="{
                        floor: {{ $priceFloor }}, ceil: {{ max($priceCeil, $priceFloor + 1) }},
                        min: {{ $minPrice }}, max: {{ $maxPrice }},
                        get span(){ return Math.max(1, this.ceil - this.floor) },
                        get lp(){ return (this.min - this.floor) / this.span * 100 },
                        get rp(){ return 100 - (this.max - this.floor) / this.span * 100 },
                        submitForm(){ this.$root.closest('form').submit() }
                     }">
                    <h4 class="text-sm font-bold mb-3">Price Range</h4>
                    <div class="flex justify-between text-sm font-semibold text-ink mb-3">
                        <span>৳<span x-text="min"></span></span>
                        <span>৳<span x-text="max"></span></span>
                    </div>
                    <div class="range-track mx-1.5">
                        <div class="range-fill" :style="`left:${lp}%; right:${rp}%`"></div>
                        <input type="range" :min="floor" :max="ceil" step="1" x-model.number="min"
                               @input="if(min > max) min = max" @change="submitForm()">
                        <input type="range" :min="floor" :max="ceil" step="1" x-model.number="max"
                               @input="if(max < min) max = min" @change="submitForm()">
                    </div>
                    <input type="hidden" name="min_price" :value="min">
                    <input type="hidden" name="max_price" :value="max">
                    <div class="flex justify-between text-[11px] text-muted mt-2">
                        <span>৳{{ number_format($priceFloor) }}</span>
                        <span>৳{{ number_format($priceCeil) }}</span>
                    </div>
                </div>

                {{-- Categories (show 5 + See All) --}}
                <div class="p-4" x-data="{ showAll: {{ $selectedCategories->isNotEmpty() ? 'true' : 'false' }} }">
                    <h4 class="text-sm font-bold mb-3">Categories</h4>
                    <div class="space-y-2">
                        @foreach($categories as $i => $cat)
                            <label class="flex items-center gap-2 text-sm cursor-pointer {{ $i >= 5 ? '' : '' }}"
                                   @if($i >= 5) x-show="showAll" x-cloak @endif>
                                <input type="checkbox" name="categories[]" value="{{ $cat->slug }}"
                                       @checked($selectedCategories->contains($cat->slug))
                                       onchange="this.form.submit()" class="accent-brand">
                                <span class="flex-1">{{ $cat->name }}</span>
                                <span class="text-xs text-muted">{{ $categoryCounts[$cat->slug] ?? 0 }}</span>
                            </label>
                        @endforeach
                    </div>
                    @if($categories->count() > 5)
                        <button type="button" @click="showAll = !showAll" class="text-xs text-brand font-semibold mt-2.5 hover:underline">
                            <span x-show="!showAll">See All ({{ $categories->count() }}) ↓</span>
                            <span x-show="showAll" x-cloak>Show Less ↑</span>
                        </button>
                    @endif
                </div>

                {{-- Availability --}}
                <div class="p-4">
                    <h4 class="text-sm font-bold mb-3">Availability</h4>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="checkbox" name="in_stock" value="1" @checked(request()->boolean('in_stock')) onchange="this.form.submit()" class="accent-brand">
                            <span class="flex-1">In Stock</span>
                            <span class="text-xs text-muted">{{ $availabilityCounts['in_stock'] ?? 0 }}</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="checkbox" name="out_of_stock" value="1" @checked(request()->boolean('out_of_stock')) onchange="this.form.submit()" class="accent-brand">
                            <span class="flex-1">Out of Stock</span>
                            <span class="text-xs text-muted">{{ $availabilityCounts['out_of_stock'] ?? 0 }}</span>
                        </label>
                    </div>
                </div>

                {{-- Collections --}}
                <div class="p-4">
                    <h4 class="text-sm font-bold mb-3">Collections</h4>
                    <div class="space-y-2">
                        @foreach($collections as $key => $label)
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" name="flags[]" value="{{ $key }}"
                                       @checked($selectedFlags->contains($key))
                                       onchange="this.form.submit()" class="accent-brand">
                                <span class="flex-1">{{ $label }}</span>
                                <span class="text-xs text-muted">{{ $flagCounts[$key] ?? 0 }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </form>
        </aside>

        {{-- ===== Products ===== --}}
        <div class="min-w-0">
            {{-- Toolbar --}}
            <div class="flex items-center justify-between mb-4 gap-3">
                <div class="flex items-center gap-2">
                    <button type="button" @click="filtersOpen = !filtersOpen" class="lg:hidden h-10 px-3 rounded-lg border border-line bg-white text-sm font-semibold inline-flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-brand"></i>Filters
                    </button>
                    <p class="text-sm text-muted">{{ $products->total() }} products</p>
                </div>
                <form method="GET" class="flex items-center gap-2">
                    @foreach(request()->except(['sort','page']) as $k => $v)
                        @if(is_array($v))
                            @foreach($v as $vv)<input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">@endforeach
                        @else
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endif
                    @endforeach
                    <label class="text-sm text-muted hidden sm:block">Sort:</label>
                    <select name="sort" onchange="this.form.submit()" class="h-10 rounded-lg border border-line bg-white px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                        <option value="" @selected(request('sort')==='')>Default</option>
                        <option value="newest" @selected(request('sort')==='newest')>Newest</option>
                        <option value="price_low" @selected(request('sort')==='price_low')>Price: Low to High</option>
                        <option value="price_high" @selected(request('sort')==='price_high')>Price: High to Low</option>
                        <option value="popular" @selected(request('sort')==='popular')>Top Rated</option>
                    </select>
                </form>
            </div>

            @if($products->isEmpty())
                <div class="bg-white rounded-xl border border-line p-12 text-center text-muted">
                    <i class="fa-solid fa-box-open text-5xl mb-3 opacity-30"></i>
                    <p>No products found. Try adjusting your filters.</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    @foreach($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layout>
