<x-layout title="Track Order">
    <div class="container-x py-6 max-w-2xl mx-auto">
        <h1 class="text-2xl font-extrabold mb-1">Track Your Order</h1>
        <p class="text-muted text-sm mb-5">Enter your order number to see its current status.</p>

        {{-- Search --}}
        <form method="GET" action="{{ route('track') }}" class="bg-white rounded-xl border border-line p-4 flex flex-col sm:flex-row gap-3">
            <input type="text" name="order_number" value="{{ request('order_number') }}" placeholder="e.g. RM260807XXXXX" required
                   class="flex-1 h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
            <button type="submit" class="btn-brand h-11 px-6 shrink-0"><i class="fa-solid fa-magnifying-glass"></i>Track</button>
        </form>

        @if($searched && ! $order)
            <div class="bg-white rounded-xl border border-line p-8 text-center text-muted mt-5">
                <i class="fa-solid fa-box-open text-4xl mb-3 opacity-30"></i>
                <p>No order found with this number. Please check and try again.</p>
            </div>
        @endif

        @if($order)
            <div class="bg-white rounded-xl border border-line p-5 mt-5">
                <div class="flex items-center justify-between flex-wrap gap-2 border-b border-line pb-4 mb-4">
                    <div>
                        <div class="text-xs text-muted">Order Number</div>
                        <div class="font-bold">{{ $order->order_number }}</div>
                        <div class="text-xs text-muted mt-1">{{ $order->created_at->format('d M Y, h:i A') }}</div>
                    </div>
                    <span class="text-sm font-semibold px-3 py-1.5 rounded-full bg-brand-light text-brand">
                        {{ \App\Models\Order::STATUS_LABELS[$order->status] ?? $order->status }}
                    </span>
                </div>

                {{-- Items --}}
                <div class="divide-y divide-line">
                    @foreach($order->items as $item)
                        <div class="flex justify-between py-2 text-sm gap-3">
                            <span class="min-w-0">{{ $item->name }} <span class="text-muted">× {{ $item->quantity }}</span></span>
                            <span class="font-semibold shrink-0">{{ bdt($item->subtotal) }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-line mt-3 pt-3 space-y-1 text-sm">
                    <div class="flex justify-between"><span class="text-muted">Subtotal:</span><span>{{ bdt($order->subtotal) }}</span></div>
                    <div class="flex justify-between"><span class="text-muted">Delivery:</span><span>{{ bdt($order->shipping_fee) }}</span></div>
                    <div class="flex justify-between font-bold text-lg border-t border-line pt-2"><span>Total:</span><span class="text-brand">{{ bdt($order->total) }}</span></div>
                </div>

                <div class="mt-4 pt-4 border-t border-line text-sm text-muted space-y-1">
                    <div><span class="text-ink font-semibold">Name:</span> {{ $order->name }}</div>
                    <div><span class="text-ink font-semibold">Mobile:</span> {{ $order->phone }}</div>
                    <div><span class="text-ink font-semibold">Address:</span> {{ $order->address }}{{ $order->thana ? ', '.$order->thana : '' }}{{ $order->district ? ', '.$order->district : '' }}</div>
                    <div><span class="text-ink font-semibold">Payment:</span> {{ $order->payment_method === 'cod' ? 'Cash on Delivery' : 'Mobile Banking' }}</div>
                </div>
            </div>
        @endif
    </div>
</x-layout>
