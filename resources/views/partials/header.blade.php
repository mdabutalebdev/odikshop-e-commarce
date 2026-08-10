{{-- ===== Top bar ===== --}}
<div class="hidden lg:block bg-brand-dark text-white/95 text-[13px]">
    <div class="container-x flex items-center justify-between h-9">
        <span class="flex items-center gap-2 font-medium">
            <i class="fa-solid fa-truck-fast"></i>{{ $settings['topbar_text'] ?? 'Free Delivery & Cash on Delivery' }}
        </span>
        <div class="flex items-center gap-4">
            <a href="tel:{{ $settings['phone'] ?? '' }}" class="flex items-center gap-1.5 hover:text-white transition">
                <i class="fa-solid fa-phone"></i>{{ $settings['phone'] ?? '' }}
            </a>
            <span class="w-px h-4 bg-white/25"></span>
            @if(!empty($settings['facebook']))<a href="{{ $settings['facebook'] }}" target="_blank" class="hover:text-white transition"><i class="fa-brands fa-facebook-f"></i></a>@endif
            @if(!empty($settings['instagram']))<a href="{{ $settings['instagram'] }}" target="_blank" class="hover:text-white transition"><i class="fa-brands fa-instagram"></i></a>@endif
            @if(!empty($settings['youtube']))<a href="{{ $settings['youtube'] }}" target="_blank" class="hover:text-white transition"><i class="fa-brands fa-youtube"></i></a>@endif
            @if(!empty($settings['tiktok']))<a href="{{ $settings['tiktok'] }}" target="_blank" class="hover:text-white transition"><i class="fa-brands fa-tiktok"></i></a>@endif
            @if(!empty($settings['telegram']))<a href="{{ $settings['telegram'] }}" target="_blank" class="hover:text-white transition"><i class="fa-brands fa-telegram"></i></a>@endif
        </div>
    </div>
</div>

{{-- ===== Main header (white) ===== --}}
<header class="bg-white text-ink sticky top-0 z-40 shadow-card border-b border-line">
    <div class="container-x">
        <div class="relative flex items-center gap-3 lg:gap-6 py-3">
            {{-- Mobile hamburger --}}
            <button type="button" class="lg:hidden text-2xl -ml-1 text-ink relative z-10" @click="mobileMenu = true" aria-label="Menu">
                <i class="fa-solid fa-bars"></i>
            </button>

            {{-- Logo — centered on mobile, left-aligned on desktop --}}
            <a href="{{ route('home') }}" class="shrink-0 absolute left-1/2 -translate-x-1/2 lg:static lg:translate-x-0">
                <img src="{{ asset('images/logo-header.png') }}" alt="{{ $settings['site_name'] ?? 'ODHIK SHOP' }}" width="400" height="171" class="h-9 sm:h-11 w-auto object-contain">
            </a>

            {{-- Search (desktop) with live suggestions --}}
            <form action="{{ route('shop') }}" method="GET" class="hidden md:flex flex-1 max-w-2xl" data-search>
                <div class="relative w-full">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('messages.search_placeholder') }}" autocomplete="off" data-search-input
                           class="w-full h-12 rounded-lg pl-4 pr-14 text-ink text-sm bg-canvas border border-line placeholder:text-muted focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
                    <button type="submit" class="absolute right-1.5 top-1.5 bottom-1.5 px-4 rounded-md bg-brand text-white grid place-items-center hover:bg-brand-dark">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    <div data-search-results class="hidden absolute top-[calc(100%+6px)] left-0 right-0 bg-white rounded-xl border border-line shadow-2xl overflow-hidden z-50 max-h-[70vh] overflow-y-auto"></div>
                </div>
            </form>

            {{-- Icons (right) --}}
            <div class="flex items-center gap-4 sm:gap-6 ml-auto relative z-10">
                <button type="button" class="md:hidden w-9 grid place-items-center text-xl text-ink" @click="searchOpen = !searchOpen" aria-label="Search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>

                <a href="{{ route('wishlist.index') }}" class="hidden sm:flex flex-col items-center gap-0.5 text-ink hover:text-brand transition" aria-label="{{ __('messages.wishlist') }}">
                    <span class="relative">
                        <i class="fa-regular fa-heart text-xl"></i>
                        <span data-wishlist-count class="absolute -top-1.5 -right-2 min-w-[16px] h-4 px-1 rounded-full bg-accent text-white text-[10px] font-bold grid place-items-center">{{ $wishlistCount ?? 0 }}</span>
                    </span>
                    <span class="text-[11px] font-medium">{{ __('messages.wishlist') }}</span>
                </a>

                <button type="button" class="flex flex-col items-center gap-0.5 text-ink hover:text-brand transition" @click="cartOpen = true" aria-label="{{ __('messages.cart') }}">
                    <span class="relative">
                        <i class="fa-solid fa-cart-shopping text-xl"></i>
                        <span data-cart-count class="absolute -top-1.5 -right-2 min-w-[16px] h-4 px-1 rounded-full bg-accent text-white text-[10px] font-bold grid place-items-center">{{ $cartCount ?? 0 }}</span>
                    </span>
                    <span class="text-[11px] font-medium hidden sm:block">{{ __('messages.cart') }}</span>
                </button>

                <a href="{{ route('account') }}" class="hidden sm:flex flex-col items-center gap-0.5 text-ink hover:text-brand transition" aria-label="{{ __('messages.account') }}">
                    <i class="fa-regular fa-user text-xl"></i>
                    <span class="text-[11px] font-medium">{{ __('messages.account') }}</span>
                </a>
            </div>
        </div>

        {{-- Search (mobile, expandable) --}}
        <div x-show="searchOpen" x-cloak x-transition class="md:hidden pb-3">
            <form action="{{ route('shop') }}" method="GET" class="relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('messages.search_placeholder') }}"
                       class="w-full h-11 rounded-lg pl-4 pr-12 text-ink text-sm bg-canvas border border-line placeholder:text-muted focus:outline-none focus:border-brand">
                <button type="submit" class="absolute right-1 top-1 bottom-1 px-4 rounded-md bg-brand text-white">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- ===== Category nav (teal, desktop) — pill style ===== --}}
    <nav class="hidden lg:block bg-brand">
        <div class="container-x">
            <ul class="flex items-center gap-2 py-2 text-sm font-medium text-white">
                <li>
                    <a href="{{ route('home') }}" class="flex items-center gap-1.5 px-3.5 h-9 rounded-lg transition {{ request()->routeIs('home') ? 'bg-white/20' : 'bg-white/5 hover:bg-white/15' }}">
                        <i class="fa-solid fa-house"></i>{{ __('messages.home') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('shop') }}" class="flex items-center gap-1.5 px-3.5 h-9 rounded-lg transition {{ request()->routeIs('shop') && !request('category') ? 'bg-white/20' : 'bg-white/5 hover:bg-white/15' }}">
                        <i class="fa-solid fa-bag-shopping"></i>{{ __('messages.shop') }}
                    </a>
                </li>
                @foreach($navCategories as $cat)
                    <li>
                        <a href="{{ route('shop', ['category' => $cat->slug]) }}" class="flex items-center gap-1.5 px-3.5 h-9 rounded-lg transition {{ request('category') === $cat->slug ? 'bg-white/20' : 'bg-white/5 hover:bg-white/15' }}">
                            <i class="{{ $cat->icon ?? 'fa-solid fa-tag' }}"></i>{{ $cat->name }}
                        </a>
                    </li>
                @endforeach
                <li>
                    <a href="{{ route('contact') }}" class="flex items-center gap-1.5 px-3.5 h-9 rounded-lg transition {{ request()->routeIs('contact') ? 'bg-white/20' : 'bg-white/5 hover:bg-white/15' }}">
                        <i class="fa-solid fa-headset"></i>{{ __('messages.contact') }}
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</header>

{{-- ===== Mobile menu drawer ===== --}}
<div x-show="mobileMenu" x-cloak class="fixed inset-0 z-50 lg:hidden" @keydown.escape.window="mobileMenu = false">
    <div x-show="mobileMenu" x-transition.opacity class="absolute inset-0 bg-black/50" @click="mobileMenu = false"></div>
    <div x-show="mobileMenu"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
         class="absolute inset-y-0 left-0 w-80 max-w-[85%] bg-white flex flex-col">
        <div class="bg-white border-b border-line p-4 flex items-center justify-between">
            <img src="{{ asset('images/logo-header.png') }}" alt="{{ $settings['site_name'] ?? 'ODHIK SHOP' }}" class="h-8 w-auto object-contain">
            <button @click="mobileMenu = false" class="text-2xl text-ink"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <nav class="flex-1 overflow-y-auto p-2">
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-canvas"><i class="fa-solid fa-house w-5 text-brand"></i>{{ __('messages.home') }}</a>
            <a href="{{ route('shop') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-canvas"><i class="fa-solid fa-bag-shopping w-5 text-brand"></i>{{ __('messages.shop') }}</a>
            @foreach($navCategories as $cat)
                <a href="{{ route('shop', ['category' => $cat->slug]) }}" class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-canvas"><i class="{{ $cat->icon ?? 'fa-solid fa-tag' }} w-5 text-brand"></i>{{ $cat->name }}</a>
            @endforeach
            <a href="{{ route('wishlist.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-canvas"><i class="fa-regular fa-heart w-5 text-brand"></i>{{ __('messages.wishlist') }}</a>
            <a href="{{ route('contact') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-canvas"><i class="fa-solid fa-headset w-5 text-brand"></i>{{ __('messages.contact') }}</a>
            <a href="{{ route('account') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-canvas"><i class="fa-regular fa-user w-5 text-brand"></i>{{ __('messages.account') }}</a>
            <a href="{{ route('track') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-canvas"><i class="fa-solid fa-truck-fast w-5 text-brand"></i>Track Order</a>
        </nav>
    </div>
</div>
