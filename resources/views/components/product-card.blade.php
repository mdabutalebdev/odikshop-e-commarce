@props(['product'])

@php $inWishlist = in_array($product->id, $wishlistIds ?? []); @endphp

<div class="group bg-white rounded-xl border border-line shadow-soft hover:shadow-card hover:border-brand/30 transition overflow-hidden flex flex-col h-full">
    <div class="relative overflow-hidden">
        @if($product->discount_percent > 0)
            <span class="absolute top-2 left-2 z-10 bg-accent text-white text-[11px] font-bold px-1.5 py-0.5 rounded">-{{ $product->discount_percent }}%</span>
        @endif

        <a href="{{ route('product.show', $product) }}" class="block aspect-square bg-canvas overflow-hidden">
            <img src="{{ image_url($product->main_image, $product->name) }}" alt="{{ $product->name }}" loading="lazy"
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
        </a>
    </div>

    <div class="p-2.5 flex flex-col flex-1">
        <h3 class="text-sm font-normal leading-snug clamp-2 min-h-[2.5rem]">
            <a href="{{ route('product.show', $product) }}" class="hover:text-brand transition">{{ $product->name }}</a>
        </h3>

        <div class="flex items-center gap-1 mt-1">
            @php $r = (int) round($product->rating); @endphp
            <span class="flex text-[9px]">
                @for($s = 1; $s <= 5; $s++)
                    <i class="fa-solid fa-star {{ $s <= $r ? 'text-amber-400' : 'text-gray-300' }}"></i>
                @endfor
            </span>
            @if($product->reviews_count)
                <span class="text-muted text-[10px]">({{ $product->reviews_count }})</span>
            @endif
        </div>

        <div class="flex items-center flex-wrap gap-x-2 mt-1.5">
            <span class="text-brand font-bold text-[13px]">{{ bdt($product->price) }}</span>
            @if($product->old_price && $product->old_price > $product->price)
                <span class="text-muted text-[11px] line-through">{{ bdt($product->old_price) }}</span>
            @endif
        </div>

        {{-- Order now (teal) — brand button, normal weight per design spec --}}
        <a href="{{ route('checkout.index', ['buy' => $product->id]) }}" class="btn-brand w-full h-9 text-sm mt-2.5 !font-normal">
            Order Now
        </a>
    </div>
</div>
