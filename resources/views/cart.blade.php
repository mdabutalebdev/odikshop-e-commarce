<x-layout title="শপিং কার্ট">
    <div class="container-x py-6">
        <h1 class="text-2xl font-extrabold mb-5">আপনার শপিং কার্ট</h1>

        @if($items->isEmpty())
            <div class="bg-white rounded-xl border border-line p-12 text-center text-muted">
                <i class="fa-solid fa-cart-shopping text-5xl mb-3 opacity-30"></i>
                <p class="mb-3">আপনার কার্ট খালি।</p>
                <a href="{{ route('shop') }}" class="btn-brand h-10 px-6 inline-flex">কেনাকাটা শুরু করুন</a>
            </div>
        @else
            <div class="grid lg:grid-cols-[1fr_320px] gap-6">
                {{-- Items --}}
                <div class="bg-white rounded-xl border border-line divide-y divide-line">
                    @foreach($items as $item)
                        <div class="flex gap-4 p-4">
                            <a href="{{ route('product.show', $item['product']) }}" class="w-20 h-20 rounded-lg bg-canvas overflow-hidden shrink-0">
                                <img src="{{ image_url($item['product']->main_image, $item['product']->name) }}" class="w-full h-full object-cover">
                            </a>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('product.show', $item['product']) }}" class="font-semibold clamp-2">{{ $item['product']->name }}</a>
                                <div class="text-brand font-bold mt-1">{{ bdt($item['product']->price) }}</div>
                                <div class="flex items-center gap-3 mt-2">
                                    <form method="POST" action="{{ route('cart.update', $item['key']) }}" class="flex items-center border border-line rounded-lg overflow-hidden">
                                        @csrf @method('PATCH')
                                        <button name="quantity" value="{{ $item['quantity'] - 1 }}" class="w-8 h-8 grid place-items-center hover:bg-canvas">−</button>
                                        <span class="w-10 h-8 grid place-items-center text-sm font-bold border-x border-line">{{ $item['quantity'] }}</span>
                                        <button name="quantity" value="{{ $item['quantity'] + 1 }}" class="w-8 h-8 grid place-items-center hover:bg-canvas">+</button>
                                    </form>
                                    <button type="button" data-remove-cart="{{ $item['key'] }}" class="text-muted hover:text-sale text-sm flex items-center gap-1"><i class="fa-solid fa-trash-can"></i>মুছুন</button>
                                </div>
                            </div>
                            <div class="text-right font-bold">{{ bdt($item['subtotal']) }}</div>
                        </div>
                    @endforeach
                </div>

                {{-- Summary --}}
                <div class="bg-white rounded-xl border border-line p-5 h-fit lg:sticky lg:top-32">
                    <h3 class="font-bold mb-4">অর্ডার সারাংশ</h3>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-muted">পণ্য মূল্য</span>
                        <span class="font-semibold">{{ bdt($subtotal) }}</span>
                    </div>
                    <div class="flex justify-between text-sm mb-3">
                        <span class="text-muted">ডেলিভারি</span>
                        <span class="text-muted">চেকআউটে গণনা</span>
                    </div>
                    <div class="border-t border-line pt-3 flex justify-between font-bold text-lg">
                        <span>মোট</span>
                        <span class="text-brand">{{ bdt($subtotal) }}</span>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="btn-brand w-full h-11 mt-4">অর্ডার সম্পন্ন করুন</a>
                    <a href="{{ route('shop') }}" class="block text-center text-sm text-brand mt-3 hover:underline">আরও কেনাকাটা করুন</a>
                </div>
            </div>
        @endif
    </div>
</x-layout>
