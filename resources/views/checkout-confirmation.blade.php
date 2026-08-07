<x-layout title="অর্ডার সম্পন্ন">
    <div class="container-x py-10 max-w-2xl mx-auto">
        <div class="bg-white rounded-xl border border-line p-6 text-center">
            <div class="w-16 h-16 rounded-full bg-brand-light text-brand grid place-items-center text-3xl mx-auto mb-4">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h1 class="text-2xl font-extrabold">ধন্যবাদ! আপনার অর্ডার সম্পন্ন হয়েছে</h1>
            <p class="text-muted mt-2">অর্ডার নম্বর: <span class="font-bold text-ink">{{ $order->order_number }}</span></p>
            <p class="text-sm text-muted mt-1">আমরা শীঘ্রই {{ $order->phone }} নম্বরে যোগাযোগ করব।</p>
        </div>

        <div class="bg-white rounded-xl border border-line p-6 mt-5">
            <h2 class="font-bold mb-4">অর্ডার বিবরণ</h2>
            <div class="divide-y divide-line">
                @foreach($order->items as $item)
                    <div class="flex justify-between py-2 text-sm">
                        <span>{{ $item->name }} <span class="text-muted">× {{ $item->quantity }}</span></span>
                        <span class="font-semibold">{{ bdt($item->subtotal) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-line mt-3 pt-3 space-y-1 text-sm">
                <div class="flex justify-between"><span class="text-muted">পণ্য মূল্য:</span><span>{{ bdt($order->subtotal) }}</span></div>
                <div class="flex justify-between"><span class="text-muted">ডেলিভারি:</span><span>{{ bdt($order->shipping_fee) }}</span></div>
                <div class="flex justify-between font-bold text-lg border-t border-line pt-2"><span>মোট:</span><span class="text-brand">{{ bdt($order->total) }}</span></div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-line p-6 mt-5 text-sm">
            <h2 class="font-bold mb-3">ডেলিভারি তথ্য</h2>
            <div class="grid sm:grid-cols-2 gap-2 text-muted">
                <div><span class="text-ink font-semibold">নাম:</span> {{ $order->name }}</div>
                <div><span class="text-ink font-semibold">মোবাইল:</span> {{ $order->phone }}</div>
                <div class="sm:col-span-2"><span class="text-ink font-semibold">ঠিকানা:</span> {{ $order->address }}{{ $order->thana ? ', '.$order->thana : '' }}{{ $order->district ? ', '.$order->district : '' }}</div>
                <div><span class="text-ink font-semibold">পেমেন্ট:</span> {{ $order->payment_method === 'cod' ? 'ক্যাশ অন ডেলিভারি' : 'মোবাইল ব্যাংকিং' }}</div>
                <div><span class="text-ink font-semibold">এলাকা:</span> {{ $order->delivery_zone === 'inside_dhaka' ? 'ঢাকার ভেতরে' : 'ঢাকার বাইরে' }}</div>
            </div>
        </div>

        <div class="flex gap-3 mt-6 justify-center">
            <a href="{{ route('home') }}" class="h-11 px-6 rounded-lg border border-line bg-white inline-flex items-center font-semibold">হোমে ফিরুন</a>
            <a href="{{ route('shop') }}" class="btn-brand h-11 px-6">আরও কেনাকাটা</a>
        </div>
    </div>
</x-layout>
