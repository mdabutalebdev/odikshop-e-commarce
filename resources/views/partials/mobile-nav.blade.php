{{-- Bottom navigation (mobile only) --}}
<nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white border-t border-line shadow-[0_-2px_10px_rgba(0,0,0,0.05)]">
    <div class="grid grid-cols-5 h-16">
        <a href="{{ route('home') }}" class="flex flex-col items-center justify-center gap-1 text-[11px] {{ request()->routeIs('home') ? 'text-brand' : 'text-muted' }}">
            <i class="fa-solid fa-house text-lg"></i>{{ __('messages.home') }}
        </a>
        <a href="{{ route('shop') }}" class="flex flex-col items-center justify-center gap-1 text-[11px] {{ request()->routeIs('shop') ? 'text-brand' : 'text-muted' }}">
            <i class="fa-solid fa-grip text-lg"></i>{{ __('messages.shop') }}
        </a>
        <a href="{{ route('wishlist.index') }}" class="relative flex flex-col items-center justify-center gap-1 text-[11px] {{ request()->routeIs('wishlist.*') ? 'text-brand' : 'text-muted' }}">
            <i class="fa-regular fa-heart text-lg"></i>{{ __('messages.wishlist') }}
            <span data-wishlist-count class="absolute top-2 right-5 min-w-4 h-4 px-1 rounded-full bg-sale text-white text-[9px] font-bold grid place-items-center {{ ($wishlistCount ?? 0) === 0 ? 'hidden' : '' }}">{{ $wishlistCount ?? 0 }}</span>
        </a>
        <button type="button" @click="cartOpen = true" class="relative flex flex-col items-center justify-center gap-1 text-[11px] text-muted">
            <i class="fa-solid fa-cart-shopping text-lg"></i>{{ __('messages.cart') }}
            <span data-cart-count class="absolute top-2 right-5 min-w-4 h-4 px-1 rounded-full bg-sale text-white text-[9px] font-bold grid place-items-center {{ ($cartCount ?? 0) === 0 ? 'hidden' : '' }}">{{ $cartCount ?? 0 }}</span>
        </button>
        <a href="{{ route('account') }}" class="flex flex-col items-center justify-center gap-1 text-[11px] {{ request()->routeIs('account') ? 'text-brand' : 'text-muted' }}">
            <i class="fa-regular fa-user text-lg"></i>{{ __('messages.account') }}
        </a>
    </div>
</nav>
