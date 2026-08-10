@if($items->isEmpty())
    <div class="text-center text-muted py-12">
        <i class="fa-solid fa-cart-shopping text-4xl mb-3 opacity-30"></i>
        <p>Your cart is empty.</p>
        <a href="{{ route('shop') }}" class="text-brand font-semibold text-sm mt-2 inline-block">Start shopping →</a>
    </div>
@else
    <div class="space-y-3">
        @foreach($items as $item)
            <div class="flex gap-3 items-center">
                <a href="{{ route('product.show', $item['product']) }}" class="w-16 h-16 rounded-lg bg-canvas overflow-hidden shrink-0">
                    <img src="{{ image_url($item['product']->main_image, $item['product']->name) }}" alt="" class="w-full h-full object-cover">
                </a>
                <div class="flex-1 min-w-0">
                    <a href="{{ route('product.show', $item['product']) }}" class="text-sm font-semibold clamp-2 leading-snug">{{ $item['product']->name }}</a>
                    <div class="text-xs text-muted mt-0.5">{{ $item['quantity'] }} × {{ bdt($item['product']->price) }}</div>
                    <div class="text-brand font-bold text-sm">{{ bdt($item['subtotal']) }}</div>
                </div>
                <button type="button" data-remove-cart="{{ $item['key'] }}" class="w-8 h-8 grid place-items-center text-muted hover:text-sale transition" aria-label="Remove">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
        @endforeach
    </div>
@endif
