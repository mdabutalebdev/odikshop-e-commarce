{{-- Slide-out cart drawer --}}
<div x-show="cartOpen" x-cloak class="fixed inset-0 z-50" @keydown.escape.window="cartOpen = false">
    <div x-show="cartOpen" x-transition.opacity class="absolute inset-0 bg-black/50" @click="cartOpen = false"></div>

    <div x-show="cartOpen"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
         class="absolute inset-y-0 right-0 w-96 max-w-[90%] bg-white flex flex-col shadow-2xl">

        <div class="bg-brand text-white p-4 flex items-center justify-between">
            <span class="font-bold flex items-center gap-2"><i class="fa-solid fa-cart-shopping"></i>Your Shopping Cart</span>
            <button @click="cartOpen = false" class="text-2xl"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div id="cart-drawer-body" class="flex-1 overflow-y-auto p-4">
            @include('partials.cart-items', ['items' => app(\App\Services\Cart::class)->items()])
        </div>

        <div class="border-t border-line p-4 space-y-3">
            <div class="flex items-center justify-between font-bold">
                <span class="text-muted font-medium">Total:</span>
                <span class="text-brand text-lg">{{ bdt(app(\App\Services\Cart::class)->subtotal()) }}</span>
            </div>
            <a href="{{ route('checkout.index') }}" class="btn-accent w-full h-11">Checkout <i class="fa-solid fa-arrow-right ml-1"></i></a>
        </div>
    </div>
</div>
