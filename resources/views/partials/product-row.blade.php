{{-- Reusable horizontal product slider used for the home sections.
     Expects: $title (string), $icon (emoji), $products (Collection), $viewAll (url) --}}
@if($products->isNotEmpty())
    <section data-row class="container-x mt-8">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-extrabold flex items-center gap-2">
                <span class="w-1.5 h-6 rounded-full bg-accent inline-block"></span>{{ $title }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ $viewAll }}" class="text-brand text-sm font-semibold hover:underline">View All →</a>
                <button type="button" data-row-prev class="hidden sm:grid w-8 h-8 rounded-full border border-line bg-white place-items-center hover:bg-brand hover:text-white hover:border-brand transition"><i class="fa-solid fa-chevron-left text-xs"></i></button>
                <button type="button" data-row-next class="hidden sm:grid w-8 h-8 rounded-full border border-line bg-white place-items-center hover:bg-brand hover:text-white hover:border-brand transition"><i class="fa-solid fa-chevron-right text-xs"></i></button>
            </div>
        </div>
        <div data-row-scroll class="flex gap-3 overflow-x-auto no-scrollbar snap-x pb-1">
            @foreach($products as $product)
                <div class="w-40 sm:w-48 shrink-0 snap-start">
                    <x-product-card :product="$product" />
                </div>
            @endforeach
        </div>
    </section>
@endif
