{{-- ===== Top bar (light cream) ===== --}}
<div class="hidden lg:block bg-[#f5f2f2] border-b border-line text-ink text-[13px]">
    <div class="container-x flex items-center justify-between h-9">
        <span class="flex items-center gap-2 font-medium text-brand">
            <i class="fa-solid fa-truck-fast"></i>{{ $settings['topbar_text'] ?? __('messages.topbar_text') }}
        </span>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-1.5 hover:text-brand transition">
                <i class="fa-solid fa-user-shield text-brand"></i>{{ __('messages.admin_panel') }}
            </a>
            
            @if(app()->getLocale() === 'en')
                <a href="{{ route('lang.switch', 'bn') }}" class="flex items-center gap-1.5 border border-line rounded px-2 py-1 hover:border-brand hover:text-brand transition">
                    <i class="fa-solid fa-globe"></i>বাংলা
                </a>
            @else
                <a href="{{ route('lang.switch', 'en') }}" class="flex items-center gap-1.5 border border-line rounded px-2 py-1 hover:border-brand hover:text-brand transition">
                    <i class="fa-solid fa-globe"></i>English
                </a>
            @endif
        </div>
    </div>
</div>

{{-- ===== Main header (green) ===== --}}
<header class="bg-brand text-white sticky top-0 z-40 shadow-card">
    <div class="container-x">
        <div class="flex items-center gap-3 lg:gap-6 py-3">
            {{-- Mobile hamburger --}}
            <button type="button" class="lg:hidden text-2xl -ml-1" @click="mobileMenu = true" aria-label="মেনু">
                <i class="fa-solid fa-bars"></i>
            </button>

            {{-- Logo (wordmark) --}}
            <a href="{{ route('home') }}" class="shrink-0">
                <span class="font-extrabold text-xl sm:text-2xl tracking-tight">{{ $settings['site_name'] ?? 'ODHIK SHOP' }}</span>
            </a>

            {{-- Search (desktop) --}}
            <form action="{{ route('shop') }}" method="GET" class="hidden md:flex flex-1 max-w-2xl">
                <div class="relative w-full">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('messages.search_placeholder') }}"
                           class="w-full h-12 rounded-lg pl-4 pr-14 text-ink text-sm bg-white placeholder:text-muted focus:outline-none focus:ring-2 focus:ring-white/50">
                    <button type="submit" class="absolute right-1.5 top-1.5 bottom-1.5 px-4 rounded-md bg-brand-dark text-white grid place-items-center hover:bg-brand-dark/90">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>

            {{-- Icons (right) --}}
            <div class="flex items-center gap-4 sm:gap-6 ml-auto">
                <button type="button" class="md:hidden w-9 grid place-items-center text-xl" @click="searchOpen = !searchOpen" aria-label="খুঁজুন">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>

                <a href="{{ route('wishlist.index') }}" class="hidden sm:flex flex-col items-center gap-0.5 hover:opacity-90" aria-label="{{ __('messages.wishlist') }}">
                    <span class="relative">
                        <i class="fa-regular fa-heart text-xl"></i>
                        <span data-wishlist-count class="absolute -top-1.5 -right-2 min-w-[16px] h-4 px-1 rounded-full bg-sale text-white text-[10px] font-bold grid place-items-center">{{ $wishlistCount ?? 0 }}</span>
                    </span>
                    <span class="text-[11px] font-medium">{{ __('messages.wishlist') }}</span>
                </a>

                <button type="button" class="flex flex-col items-center gap-0.5 hover:opacity-90" @click="cartOpen = true" aria-label="{{ __('messages.cart') }}">
                    <span class="relative">
                        <i class="fa-solid fa-cart-shopping text-xl"></i>
                        <span data-cart-count class="absolute -top-1.5 -right-2 min-w-[16px] h-4 px-1 rounded-full bg-sale text-white text-[10px] font-bold grid place-items-center">{{ $cartCount ?? 0 }}</span>
                    </span>
                    <span class="text-[11px] font-medium hidden sm:block">{{ __('messages.cart') }}</span>
                </button>

                <a href="{{ route('account') }}" class="hidden sm:flex flex-col items-center gap-0.5 hover:opacity-90" aria-label="{{ __('messages.account') }}">
                    <i class="fa-regular fa-user text-xl"></i>
                    <span class="text-[11px] font-medium">{{ __('messages.account') }}</span>
                </a>
            </div>
        </div>

        {{-- Search (mobile, expandable) --}}
        <div x-show="searchOpen" x-cloak x-transition class="md:hidden pb-3">
            <form action="{{ route('shop') }}" method="GET" class="relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="পণ্য খুঁজুন..."
                       class="w-full h-11 rounded-lg pl-4 pr-12 text-ink text-sm bg-white placeholder:text-muted focus:outline-none">
                <button type="submit" class="absolute right-1 top-1 bottom-1 px-4 rounded-md bg-brand-dark text-white">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- ===== Category nav (darker green, desktop) — pill style ===== --}}
    <nav class="hidden lg:block bg-brand-dark">
        <div class="container-x">
            <ul class="flex items-center gap-2 py-2 text-sm font-medium">
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
        <div class="bg-brand text-white p-4 flex items-center justify-between">
            <span class="font-extrabold text-lg">{{ $settings['site_name'] ?? 'ODHIK SHOP' }}</span>
            <button @click="mobileMenu = false" class="text-2xl"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <nav class="flex-1 overflow-y-auto p-2">
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-canvas"><i class="fa-solid fa-house w-5 text-brand"></i>হোম</a>
            <a href="{{ route('shop') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-canvas"><i class="fa-solid fa-bag-shopping w-5 text-brand"></i>শপ</a>
            @foreach($navCategories as $cat)
                <a href="{{ route('shop', ['category' => $cat->slug]) }}" class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-canvas"><i class="{{ $cat->icon ?? 'fa-solid fa-tag' }} w-5 text-brand"></i>{{ $cat->name }}</a>
            @endforeach
            <a href="{{ route('wishlist.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-canvas"><i class="fa-regular fa-heart w-5 text-brand"></i>উইশলিস্ট</a>
            <a href="{{ route('contact') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-canvas"><i class="fa-solid fa-headset w-5 text-brand"></i>যোগাযোগ</a>
            <a href="{{ route('account') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-canvas"><i class="fa-regular fa-user w-5 text-brand"></i>অ্যাকাউন্ট</a>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-canvas"><i class="fa-solid fa-user-shield w-5 text-brand"></i>অ্যাডমিন প্যানেল</a>
        </nav>
    </div>
</div>
