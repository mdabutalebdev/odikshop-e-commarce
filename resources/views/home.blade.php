<x-layout>
    {{-- ===== Hero: full-width auto-sliding banner ===== --}}
    <style>
        [data-hero-dot] { background-color: rgba(242, 92, 5, 0.5) !important; }
        [data-hero-dot].bg-accent, [data-hero-dot].bg-white { background-color: rgba(242, 92, 5, 1) !important; opacity: 1 !important; }
    </style>
    <section class="container-x pt-3 sm:pt-4">
        <div data-hero class="relative rounded-2xl overflow-hidden shadow-card bg-brand-light">
            <div data-hero-track class="flex transition-transform duration-500 ease-out">
                @forelse($centerSlides as $slide)
                    <a href="{{ $slide->link ?? '#' }}" class="w-full shrink-0 block">
                        <img src="{{ image_url($slide->image, 'ODHIK SHOP') }}" alt="{{ $slide->title }}"
                             class="w-full aspect-[16/6] sm:aspect-[16/5] object-cover">
                    </a>
                @empty
                    <div class="w-full aspect-[16/6] sm:aspect-[16/5] bg-brand grid place-items-center text-white text-2xl font-bold">ODHIK SHOP</div>
                @endforelse
            </div>

            @if($centerSlides->count() > 1)
                <button type="button" data-hero-prev class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-white/85 hover:bg-white grid place-items-center shadow text-ink">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <button type="button" data-hero-next class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-white/85 hover:bg-white grid place-items-center shadow text-ink">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
                <div class="absolute bottom-3 inset-x-0 flex justify-center gap-1.5">
                    @foreach($centerSlides as $i => $b)
                        <button type="button" data-hero-dot class="w-2.5 h-2.5 rounded-full bg-white/50 {{ $i === 0 ? 'bg-accent' : '' }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ===== Shop by Category (slider — managed from the admin panel) =====
         Uses the same data-row hooks as partials/product-row, so the shared slider
         script in resources/js/app.js drives the arrows and drag-scroll. The card
         styling is inline CSS rather than Tailwind utilities so the section can be
         deployed by editing this file alone, with no CSS rebuild. --}}
    @if($categories->isNotEmpty())
        <style>
            /* Plain CSS rather than Tailwind utilities: this section has to be
               deployable by editing the Blade file alone, and new utility classes
               would not exist in the already-compiled bundle. Colours come from the
               @theme tokens in resources/css/app.css. */
            .catx-headrow { position: relative; margin-bottom: 1.1rem; }
            .catx-title { text-align: center; font-weight: 800; line-height: 1.25; color: var(--color-ink); font-size: clamp(1.15rem, 4vw, 1.75rem); }
            .catx-viewall { position: absolute; right: 0; top: 50%; transform: translateY(-50%); font-size: .8125rem; font-weight: 600; color: var(--color-brand); text-decoration: none; }
            .catx-viewall:hover { text-decoration: underline; }

            .catx-wrap { position: relative; }
            .catx-track { display: flex; gap: 1rem; overflow-x: auto; scroll-snap-type: x proximity; padding-bottom: .25rem; }
            .catx-item { flex: 0 0 auto; width: calc((100% - 2rem) / 3); scroll-snap-align: start; text-decoration: none; color: inherit; }
            @media (min-width: 640px) {
                .catx-track { gap: 1.25rem; }
                .catx-item { width: calc((100% - 5rem) / 5); }
            }
            @media (min-width: 1024px) { .catx-item { width: calc((100% - 7.5rem) / 7); } }

            .catx-card {
                aspect-ratio: 1 / 1;
                background: #fff;
                border: 1px solid var(--color-line);
                border-radius: 20px;
                overflow: hidden;
                display: grid;
                place-items: center;
                box-shadow: 0 2px 8px rgba(16, 24, 40, .06);
            }
            /* Only the artwork reacts to hover; overflow:hidden on the card keeps the
               zoom clipped to the rounded corners. */
            .catx-card img,
            .catx-card i { transition: transform .35s ease; }
            .catx-card img { width: 100%; height: 100%; object-fit: cover; display: block; }
            .catx-card i { font-size: clamp(1.5rem, 4vw, 2.25rem); color: var(--color-brand); }
            .catx-item:hover .catx-card img,
            .catx-item:hover .catx-card i { transform: scale(1.08); }

            /* Fixed height keeps every tile identical, which is what lets the arrows
               stay centred on the cards instead of drifting with longer names. */
            .catx-name {
                height: 2.1rem;
                margin-top: .55rem;
                display: flex; align-items: center; justify-content: center;
                text-align: center;
                font-size: .8125rem; font-weight: 600; line-height: 1.25;
                color: var(--color-ink);
            }
            @media (min-width: 640px) { .catx-name { font-size: .875rem; } }

            .catx-nav {
                position: absolute;
                top: calc((100% - 2.9rem) / 2);   /* centre of the card row, above the name */
                transform: translateY(-50%);
                width: 2rem; height: 2rem;
                font-size: .75rem;
                display: grid; place-items: center;
                border: 0;
                border-radius: 9999px;
                background: #F48721;
                color: #fff;
                box-shadow: none;
                cursor: pointer;
                transition: background .2s ease;
                z-index: 5;
            }
            .catx-nav:hover { background: #dd7712; }
            .catx-prev { left: -.25rem; }
            .catx-next { right: -.25rem; }
            @media (min-width: 640px) {
                .catx-nav { width: 2.5rem; height: 2.5rem; font-size: 1rem; }
                .catx-prev { left: -.5rem; }
                .catx-next { right: -.5rem; }
            }
        </style>

        <section data-row class="container-x mt-8">
            <div class="catx-headrow">
                <h2 class="catx-title">Featured Categories</h2>
                <a href="{{ route('shop') }}" class="catx-viewall">View All →</a>
            </div>

            <div class="catx-wrap">
                <div data-row-scroll class="catx-track no-scrollbar">
                    @foreach($categories as $cat)
                        <a href="{{ route('shop', ['category' => $cat->slug]) }}" class="catx-item">
                            <div class="catx-card">
                                @if($cat->image)
                                    <img src="{{ image_url($cat->image, $cat->name) }}" alt="{{ $cat->name }}" loading="lazy">
                                @else
                                    <i class="{{ $cat->icon ?? 'fa-solid fa-tag' }}"></i>
                                @endif
                            </div>
                            <div class="catx-name">{{ $cat->name }}</div>
                        </a>
                    @endforeach
                </div>

                <button type="button" data-row-prev class="catx-nav catx-prev" aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>
                <button type="button" data-row-next class="catx-nav catx-next" aria-label="Next"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </section>
    @endif

    {{-- ===== Dynamic product sections (each controlled from the admin panel) ===== --}}
    @include('partials.product-row', ['title' => 'Flash Sale',   'icon' => '⚡', 'products' => $flashSale,   'viewAll' => route('shop', ['flash_sale' => 1])])
    @include('partials.product-row', ['title' => 'Featured',     'icon' => '⭐', 'products' => $featured,    'viewAll' => route('shop', ['featured' => 1])])
    @include('partials.product-row', ['title' => 'Best Selling', 'icon' => '🔥', 'products' => $bestSelling, 'viewAll' => route('shop', ['best_seller' => 1])])
    @include('partials.product-row', ['title' => 'New Arrival',  'icon' => '🆕', 'products' => $newArrival,  'viewAll' => route('shop', ['new_arrival' => 1])])

    {{-- ===== All Products grid with Load More ===== --}}
    @if($homeProducts->total() > 0)
        <section class="container-x mt-10"
                 x-data="{
                    page: 1,
                    loading: false,
                    hasMore: {{ $homeProducts->hasMorePages() ? 'true' : 'false' }},
                    async loadMore() {
                        if (this.loading || !this.hasMore) return;
                        this.loading = true;
                        const next = this.page + 1;
                        try {
                            const res = await fetch(`{{ route('home.load-more') }}?page=${next}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                            const data = await res.json();
                            this.$refs.grid.insertAdjacentHTML('beforeend', data.html);
                            this.page = next;
                            this.hasMore = data.hasMore;
                        } catch (e) {
                            console.error('Load more failed', e);
                        } finally {
                            this.loading = false;
                        }
                    }
                 }">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-extrabold flex items-center gap-2">
                    <span class="w-1.5 h-6 rounded-full bg-accent inline-block"></span>All Products
                </h2>
                <a href="{{ route('shop') }}" class="text-brand text-sm font-semibold hover:underline">View All →</a>
            </div>

            <div x-ref="grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @include('partials.home-products', ['products' => $homeProducts])
            </div>

            <div class="text-center mt-6" x-show="hasMore" x-cloak>
                <button type="button" @click="loadMore()" :disabled="loading"
                        class="btn-brand h-11 px-8 inline-flex items-center justify-center gap-2 disabled:opacity-60">
                    <span x-show="!loading">Load More</span>
                    <span x-show="loading" x-cloak class="inline-flex items-center gap-2"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</span>
                </button>
            </div>
        </section>
    @endif

    {{-- ===== Customer reviews (sliding) ===== --}}
    @include('partials.reviews', ['testimonials' => $testimonials])

    {{-- ===== Trust badges (sits right before the footer) ===== --}}
    <section class="container-x mt-12">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach([
                ['fa-truck-fast', 'Free Delivery', 'On selected orders'],
                ['fa-money-bill-wave', 'Cash on Delivery', 'Pay when you receive'],
                ['fa-rotate-left', 'Easy Return', 'Within 3 days'],
                ['fa-headset', '24/7 Support', 'Anytime, anywhere'],
            ] as $badge)
                <div class="bg-white rounded-xl border border-line p-4 flex items-center gap-3">
                    <span class="w-11 h-11 rounded-full bg-brand-light text-brand grid place-items-center text-lg shrink-0"><i class="fa-solid {{ $badge[0] }}"></i></span>
                    <div>
                        <div class="font-bold text-sm">{{ $badge[1] }}</div>
                        <div class="text-xs text-muted">{{ $badge[2] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</x-layout>
