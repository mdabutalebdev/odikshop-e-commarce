@props(['product'])

@php $inWishlist = in_array($product->id, $wishlistIds ?? []); @endphp

<div class="group bg-white rounded-xl border border-line shadow-soft hover:shadow-card transition overflow-hidden flex flex-col">
    <div class="relative">
        @if($product->discount_percent > 0)
            <span class="absolute top-2 left-2 z-10 bg-sale text-white text-[11px] font-bold px-1.5 py-0.5 rounded">-{{ $product->discount_percent }}%</span>
        @endif

        <a href="{{ route('product.show', $product) }}" class="block aspect-square bg-canvas overflow-hidden">
            <img src="{{ image_url($product->main_image, $product->name) }}" alt="{{ $product->name }}" loading="lazy"
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
        </a>
    </div>

    <div class="p-2.5 flex flex-col flex-1">
        <h3 class="text-sm font-semibold leading-snug clamp-2 min-h-[2.5rem]">
            <a href="{{ route('product.show', $product) }}" class="hover:text-brand transition">{{ $product->name }}</a>
        </h3>

        <div class="flex items-center gap-1 mt-1">
            @php $r = (int) round($product->rating); @endphp
            <span class="flex text-[11px]">
                @for($s = 1; $s <= 5; $s++)
                    <i class="fa-solid fa-star {{ $s <= $r ? 'text-amber-400' : 'text-gray-300' }}"></i>
                @endfor
            </span>
            @if($product->reviews_count)
                <span class="text-muted text-xs">({{ $product->reviews_count }})</span>
            @endif
        </div>

        <div class="flex items-center flex-wrap gap-x-2 mt-1.5">
            <span class="text-brand font-extrabold text-[15px]">{{ bdt($product->price) }}</span>
            @if($product->old_price && $product->old_price > $product->price)
                <span class="text-muted text-xs line-through">{{ bdt($product->old_price) }}</span>
            @endif
        </div>

        {{-- Action icon buttons: wishlist · cart · view --}}
        <div class="grid grid-cols-3 gap-1.5 mt-2.5">
            <button type="button" data-wishlist="{{ $product->id }}"
                    class="h-9 rounded-lg bg-canvas hover:bg-brand-light grid place-items-center transition {{ $inWishlist ? 'is-active' : '' }}"
                    aria-label="উইশলিস্ট">
                <i class="{{ $inWishlist ? 'fa-solid' : 'fa-regular' }} fa-heart text-sm" @style(['color:#ef4444' => $inWishlist])></i>
            </button>
            <button type="button" data-add-cart="{{ $product->id }}"
                    class="h-9 rounded-lg bg-brand-light text-brand hover:bg-brand hover:text-white grid place-items-center transition"
                    aria-label="কার্টে যোগ করুন">
                <i class="fa-solid fa-cart-shopping text-sm"></i>
            </button>
            <a href="{{ route('product.show', $product) }}"
               class="h-9 rounded-lg bg-canvas hover:bg-brand-light text-ink grid place-items-center transition"
               aria-label="বিস্তারিত দেখুন">
                <i class="fa-regular fa-eye text-sm"></i>
            </a>
        </div>

        {{-- Order button --}}
        <a href="{{ route('checkout.index', ['buy' => $product->id]) }}" class="btn-brand w-full h-9 text-sm mt-1.5 font-bold">
            <i class="fa-solid fa-bolt"></i>অর্ডার করুন
        </a>
    </div>
</div>
