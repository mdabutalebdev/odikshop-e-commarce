<x-layout title="উইশলিস্ট">
    <div class="container-x py-6">
        <h1 class="text-2xl font-extrabold mb-5">আমার উইশলিস্ট</h1>

        @if($items->isEmpty())
            <div class="bg-white rounded-xl border border-line p-12 text-center text-muted">
                <i class="fa-regular fa-heart text-5xl mb-3 opacity-30"></i>
                <p class="mb-3">আপনার উইশলিস্ট খালি।</p>
                <a href="{{ route('shop') }}" class="btn-brand h-10 px-6 inline-flex">পণ্য দেখুন</a>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach($items as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        @endif
    </div>
</x-layout>
