<x-admin-layout title="অর্ডার #{{ $order->order_number }}">
    <a href="{{ route('admin.orders.index') }}" class="text-sm text-muted hover:text-brand mb-4 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i>অর্ডার তালিকা</a>

    <div class="grid lg:grid-cols-[1fr_320px] gap-6">
        {{-- Items --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-line p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold">অর্ডার আইটেম</h2>
                    <span class="text-xs text-muted">{{ $order->created_at->format('d M Y, g:i A') }}</span>
                </div>
                <div class="divide-y divide-line">
                    @foreach($order->items as $item)
                        <div class="flex items-center gap-3 py-3">
                            <div class="flex-1"><div class="font-semibold text-sm">{{ $item->name }}</div><div class="text-xs text-muted">{{ $item->quantity }} × {{ bdt($item->price) }}</div></div>
                            <div class="font-bold">{{ bdt($item->subtotal) }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="border-t border-line mt-3 pt-3 space-y-1 text-sm">
                    <div class="flex justify-between"><span class="text-muted">পণ্য মূল্য</span><span>{{ bdt($order->subtotal) }}</span></div>
                    <div class="flex justify-between"><span class="text-muted">ডেলিভারি</span><span>{{ bdt($order->shipping_fee) }}</span></div>
                    <div class="flex justify-between font-bold text-lg border-t border-line pt-2"><span>মোট</span><span class="text-brand">{{ bdt($order->total) }}</span></div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-line p-5 text-sm">
                <h2 class="font-bold mb-3">গ্রাহক ও ডেলিভারি</h2>
                <div class="grid sm:grid-cols-2 gap-2 text-muted">
                    <div><span class="text-ink font-semibold">নাম:</span> {{ $order->name }}</div>
                    <div><span class="text-ink font-semibold">মোবাইল:</span> {{ $order->phone }}</div>
                    <div class="sm:col-span-2"><span class="text-ink font-semibold">ঠিকানা:</span> {{ $order->address }}{{ $order->thana ? ', '.$order->thana : '' }}{{ $order->district ? ', '.$order->district : '' }}{{ $order->division ? ', '.$order->division : '' }}</div>
                    <div><span class="text-ink font-semibold">এলাকা:</span> {{ $order->delivery_zone === 'inside_dhaka' ? 'ঢাকার ভেতরে' : 'ঢাকার বাইরে' }}</div>
                    <div><span class="text-ink font-semibold">পেমেন্ট:</span> {{ $order->payment_method === 'cod' ? 'ক্যাশ অন ডেলিভারি' : 'মোবাইল ব্যাংকিং' }}</div>
                    @if($order->notes)<div class="sm:col-span-2"><span class="text-ink font-semibold">নোট:</span> {{ $order->notes }}</div>@endif
                </div>
            </div>
        </div>

        {{-- Status update --}}
        <div class="bg-white rounded-xl border border-line p-5 h-fit">
            <h2 class="font-bold mb-4">স্ট্যাটাস আপডেট</h2>
            <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="space-y-3">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-sm font-medium mb-1">অর্ডার স্ট্যাটাস</label>
                    <select name="status" class="w-full h-11 rounded-lg border border-line px-3 text-sm bg-white">
                        @foreach(\App\Models\Order::STATUS_LABELS as $key => $label)
                            <option value="{{ $key }}" @selected($order->status===$key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">পেমেন্ট স্ট্যাটাস</label>
                    <select name="payment_status" class="w-full h-11 rounded-lg border border-line px-3 text-sm bg-white">
                        @foreach(['unpaid' => 'বকেয়া', 'paid' => 'পরিশোধিত', 'failed' => 'ব্যর্থ'] as $key => $label)
                            <option value="{{ $key }}" @selected($order->payment_status===$key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn-brand w-full h-11">আপডেট করুন</button>
            </form>
        </div>
    </div>
</x-admin-layout>
