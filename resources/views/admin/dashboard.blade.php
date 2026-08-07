<x-admin-layout title="ড্যাশবোর্ড">
    {{-- Stat cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'মোট পণ্য', 'value' => $stats['products'], 'icon' => 'fa-box', 'color' => 'bg-blue-500'],
            ['label' => 'মোট অর্ডার', 'value' => $stats['orders'], 'icon' => 'fa-bag-shopping', 'color' => 'bg-brand'],
            ['label' => 'অপেক্ষমাণ অর্ডার', 'value' => $stats['pending'], 'icon' => 'fa-clock', 'color' => 'bg-amber-500'],
            ['label' => 'মোট আয়', 'value' => bdt($stats['revenue']), 'icon' => 'fa-sack-dollar', 'color' => 'bg-emerald-600'],
        ] as $card)
            <div class="bg-white rounded-xl border border-line p-5">
                <div class="flex items-center justify-between">
                    <span class="w-11 h-11 rounded-lg {{ $card['color'] }} text-white grid place-items-center text-lg"><i class="fa-solid {{ $card['icon'] }}"></i></span>
                </div>
                <div class="text-2xl font-extrabold mt-3">{{ $card['value'] }}</div>
                <div class="text-sm text-muted">{{ $card['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Recent orders --}}
    <div class="bg-white rounded-xl border border-line mt-6">
        <div class="flex items-center justify-between p-5 border-b border-line">
            <h2 class="font-bold">সাম্প্রতিক অর্ডার</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-brand text-sm font-semibold hover:underline">সব দেখুন →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-canvas text-muted text-left">
                    <tr>
                        <th class="px-5 py-3 font-semibold">অর্ডার</th>
                        <th class="px-5 py-3 font-semibold">গ্রাহক</th>
                        <th class="px-5 py-3 font-semibold">মোট</th>
                        <th class="px-5 py-3 font-semibold">স্ট্যাটাস</th>
                        <th class="px-5 py-3 font-semibold">তারিখ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-canvas">
                            <td class="px-5 py-3"><a href="{{ route('admin.orders.show', $order) }}" class="font-semibold text-brand">{{ $order->order_number }}</a></td>
                            <td class="px-5 py-3">{{ $order->name }}<div class="text-xs text-muted">{{ $order->phone }}</div></td>
                            <td class="px-5 py-3 font-bold">{{ bdt($order->total) }}</td>
                            <td class="px-5 py-3"><span class="text-xs font-semibold px-2 py-1 rounded-full bg-brand-light text-brand">{{ \App\Models\Order::STATUS_LABELS[$order->status] ?? $order->status }}</span></td>
                            <td class="px-5 py-3 text-muted">{{ $order->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-muted">এখনো কোনো অর্ডার নেই।</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
