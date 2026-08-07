<x-layout>
    {{-- ===== Hero: left promo + center slider + right promo ===== --}}
    <section class="container-x pt-3 sm:pt-4">
        <div class="grid grid-cols-1 lg:grid-cols-6 gap-3 items-stretch">
            {{-- Left promo --}}
            @if($leftBanner)
                <a href="{{ $leftBanner->link ?? '#' }}" class="hidden lg:block lg:col-span-1 rounded-xl overflow-hidden shadow-card">
                    <img src="{{ image_url($leftBanner->image, 'Left') }}" alt="{{ $leftBanner->title }}" class="w-full h-full object-cover">
                </a>
            @endif

            {{-- Center slider (image keeps its natural 1200×375 aspect — no cropping) --}}
            <div data-hero class="relative rounded-xl overflow-hidden shadow-card {{ ($leftBanner && $rightBanner) ? 'lg:col-span-4' : 'lg:col-span-6' }}">
                <div data-hero-track class="flex transition-transform duration-500 ease-out">
                    @forelse($centerSlides as $slide)
                        <a href="{{ $slide->link ?? '#' }}" class="w-full shrink-0 block">
                            <img src="{{ image_url($slide->image, 'Banner') }}" alt="{{ $slide->title }}"
                                 class="w-full aspect-[1200/375] object-cover">
                        </a>
                    @empty
                        <div class="w-full aspect-[1200/375] bg-brand grid place-items-center text-white text-2xl font-bold">ODHIK SHOP</div>
                    @endforelse
                </div>

                @if($centerSlides->count() > 1)
                    <button type="button" data-hero-prev class="absolute left-2 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/80 hover:bg-white grid place-items-center shadow text-ink">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button type="button" data-hero-next class="absolute right-2 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/80 hover:bg-white grid place-items-center shadow text-ink">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                    <div class="absolute bottom-3 inset-x-0 flex justify-center gap-1.5">
                        @foreach($centerSlides as $i => $b)
                            <button type="button" data-hero-dot class="w-2 h-2 rounded-full bg-white/50 {{ $i === 0 ? 'bg-white' : '' }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Right promo --}}
            @if($rightBanner)
                <a href="{{ $rightBanner->link ?? '#' }}" class="hidden lg:block lg:col-span-1 rounded-xl overflow-hidden shadow-card">
                    <img src="{{ image_url($rightBanner->image, 'Right') }}" alt="{{ $rightBanner->title }}" class="w-full h-full object-cover">
                </a>
            @endif
        </div>
    </section>

    {{-- ===== Category strip ===== --}}
    <section class="container-x mt-5">
        <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 gap-3">
            @foreach($categories as $cat)
                <a href="{{ route('shop', ['category' => $cat->slug]) }}"
                   class="bg-white rounded-xl border border-line shadow-soft p-3 flex flex-col items-center gap-2 hover:shadow-card hover:-translate-y-0.5 transition text-center">
                    <span class="w-11 h-11 rounded-full bg-brand-light text-brand grid place-items-center text-lg">
                        <i class="{{ $cat->icon ?? 'fa-solid fa-tag' }}"></i>
                    </span>
                    <span class="text-xs font-medium leading-tight">{{ $cat->name }}</span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ===== Flash sale ===== --}}
    @if($flashSale->isNotEmpty())
        <section data-row class="container-x mt-8">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-extrabold flex items-center gap-2">
                    <span class="text-xl">⚡</span> {{ __('messages.flash_sale') }}
                </h2>
                <div class="flex items-center gap-2">
                    <a href="{{ route('shop', ['flash' => 1]) }}" class="text-brand text-sm font-semibold hover:underline">{{ __('messages.view_all') }}</a>
                    <button type="button" data-row-prev class="hidden sm:grid w-8 h-8 rounded-full border border-line bg-white place-items-center hover:bg-canvas"><i class="fa-solid fa-chevron-left text-xs"></i></button>
                    <button type="button" data-row-next class="hidden sm:grid w-8 h-8 rounded-full border border-line bg-white place-items-center hover:bg-canvas"><i class="fa-solid fa-chevron-right text-xs"></i></button>
                </div>
            </div>
            <div data-row-scroll class="flex gap-3 overflow-x-auto no-scrollbar snap-x pb-1">
                @foreach($flashSale as $product)
                    <div class="w-40 sm:w-48 shrink-0 snap-start">
                        <x-product-card :product="$product" />
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ===== Popular products ===== --}}
    @if($popular->isNotEmpty())
        <section class="container-x mt-8">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-extrabold flex items-center gap-2">
                    <span class="text-xl">🔥</span> {{ __('messages.popular_products') }}
                </h2>
                <a href="{{ route('shop', ['popular' => 1]) }}" class="text-brand text-sm font-semibold hover:underline">{{ __('messages.view_all') }}</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
                @foreach($popular as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- ===== Trust badges ===== --}}
    <section class="container-x mt-10">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach([
                ['fa-truck-fast', __('messages.free_delivery'), __('messages.free_delivery_sub')],
                ['fa-money-bill-wave', __('messages.cash_on_delivery'), __('messages.cash_on_delivery_sub')],
                ['fa-rotate-left', __('messages.easy_return'), __('messages.easy_return_sub')],
                ['fa-headset', __('messages.support_24_7'), __('messages.support_24_7_sub')],
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
