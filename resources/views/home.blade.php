<x-layout>
    {{-- ===== Hero: full-width auto-sliding banner ===== --}}
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
                        <button type="button" data-hero-dot class="w-2.5 h-2.5 rounded-full bg-white/50 {{ $i === 0 ? 'bg-white' : '' }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ===== Shop by Category (slider — managed from the admin panel) ===== --}}
    @if($categories->isNotEmpty())
        <section data-row class="container-x mt-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-extrabold flex items-center gap-2">
                    <span class="w-1.5 h-6 rounded-full bg-accent inline-block"></span>Featured Categories
                </h2>
                <div class="flex items-center gap-2">
                    <a href="{{ route('shop') }}" class="text-brand text-sm font-semibold hover:underline">View All →</a>
                    <button type="button" data-row-prev class="hidden sm:grid w-8 h-8 rounded-full border border-line bg-white place-items-center hover:bg-brand hover:text-white hover:border-brand transition"><i class="fa-solid fa-chevron-left text-xs"></i></button>
                    <button type="button" data-row-next class="hidden sm:grid w-8 h-8 rounded-full border border-line bg-white place-items-center hover:bg-brand hover:text-white hover:border-brand transition"><i class="fa-solid fa-chevron-right text-xs"></i></button>
                </div>
            </div>
            <div data-row-scroll class="flex gap-4 overflow-x-auto no-scrollbar snap-x pb-2">
                @foreach($categories as $cat)
                    <a href="{{ route('shop', ['category' => $cat->slug]) }}"
                       class="group w-28 sm:w-36 shrink-0 snap-start text-center">
                        <div class="bg-white rounded-2xl border border-line shadow-card aspect-square grid place-items-center overflow-hidden group-hover:shadow-lg group-hover:-translate-y-1 group-hover:border-brand/40 transition">
                            @if($cat->image)
                                <img src="{{ image_url($cat->image, $cat->name) }}" alt="{{ $cat->name }}" loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <i class="{{ $cat->icon ?? 'fa-solid fa-tag' }} text-4xl sm:text-5xl text-brand group-hover:scale-110 transition"></i>
                            @endif
                        </div>
                        <div class="mt-2.5 text-sm font-semibold text-ink leading-tight">{{ $cat->name }}</div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ===== Dynamic product sections (each controlled from the admin panel) ===== --}}
    @include('partials.product-row', ['title' => 'Flash Sale',   'icon' => '⚡', 'products' => $flashSale,   'viewAll' => route('shop', ['flash_sale' => 1])])
    @include('partials.product-row', ['title' => 'Featured',     'icon' => '⭐', 'products' => $featured,    'viewAll' => route('shop', ['featured' => 1])])
    @include('partials.product-row', ['title' => 'Best Selling', 'icon' => '🔥', 'products' => $bestSelling, 'viewAll' => route('shop', ['best_seller' => 1])])
    @include('partials.product-row', ['title' => 'New Arrival',  'icon' => '🆕', 'products' => $newArrival,  'viewAll' => route('shop', ['new_arrival' => 1])])

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
