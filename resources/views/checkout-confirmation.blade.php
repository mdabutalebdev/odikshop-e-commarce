<x-layout title="Order Placed">
    <div class="container-x py-10 max-w-2xl mx-auto">
        <div class="bg-white rounded-xl border border-line p-6 text-center">
            <div class="w-16 h-16 rounded-full bg-brand-light text-brand grid place-items-center text-3xl mx-auto mb-4">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h1 class="text-2xl font-extrabold">Thank you! Your order has been placed.</h1>
            <p class="text-muted mt-2">Order Number: <span class="font-bold text-ink">{{ $order->order_number }}</span></p>
            <p class="text-sm text-muted mt-1">We will contact you shortly at {{ $order->phone }}.</p>
        </div>

        <div class="bg-white rounded-xl border border-line p-6 mt-5">
            <h2 class="font-bold mb-4">Order Details</h2>
            <div class="divide-y divide-line">
                @foreach($order->items as $item)
                    <div class="flex justify-between py-2 text-sm">
                        <span>{{ $item->name }} <span class="text-muted">× {{ $item->quantity }}</span></span>
                        <span class="font-semibold">{{ bdt($item->subtotal) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-line mt-3 pt-3 space-y-1 text-sm">
                <div class="flex justify-between"><span class="text-muted">Subtotal:</span><span>{{ bdt($order->subtotal) }}</span></div>
                <div class="flex justify-between"><span class="text-muted">Delivery:</span><span>{{ bdt($order->shipping_fee) }}</span></div>
                <div class="flex justify-between font-bold text-lg border-t border-line pt-2"><span>Total:</span><span class="text-brand">{{ bdt($order->total) }}</span></div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-line p-6 mt-5 text-sm">
            <h2 class="font-bold mb-3">Delivery Information</h2>
            <div class="grid sm:grid-cols-2 gap-2 text-muted">
                <div><span class="text-ink font-semibold">Name:</span> {{ $order->name }}</div>
                <div><span class="text-ink font-semibold">Mobile:</span> {{ $order->phone }}</div>
                <div class="sm:col-span-2"><span class="text-ink font-semibold">Address:</span> {{ $order->address }}{{ $order->thana ? ', '.$order->thana : '' }}{{ $order->district ? ', '.$order->district : '' }}</div>
                <div><span class="text-ink font-semibold">Payment:</span> {{ $order->payment_method === 'cod' ? 'Cash on Delivery' : 'Mobile Banking' }}</div>
                <div><span class="text-ink font-semibold">Area:</span> {{ $order->delivery_zone === 'inside_dhaka' ? 'Inside Dhaka' : 'Outside Dhaka' }}</div>
            </div>
        </div>

        <div class="flex gap-3 mt-6 justify-center">
            <a href="{{ route('home') }}" class="h-11 px-6 rounded-lg border border-line bg-white inline-flex items-center font-semibold">Back to Home</a>
            <a href="{{ route('shop') }}" class="btn-accent h-11 px-6">Continue Shopping</a>
        </div>
    </div>
</x-layout>
