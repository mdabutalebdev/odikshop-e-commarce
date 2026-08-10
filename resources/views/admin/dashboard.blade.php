<x-admin-layout title="Dashboard">
    @php
        $cards = [
            ['label' => 'Total Revenue', 'value' => bdt($stats['revenue']), 'icon' => 'fa-sack-dollar', 'grad' => 'from-teal-500 to-emerald-600'],
            ['label' => 'Total Orders', 'value' => $stats['orders'], 'icon' => 'fa-bag-shopping', 'grad' => 'from-orange-500 to-amber-500'],
            ['label' => 'Pending Orders', 'value' => $stats['pending'], 'icon' => 'fa-clock', 'grad' => 'from-rose-500 to-pink-600'],
            ['label' => 'Products', 'value' => $stats['products'], 'icon' => 'fa-box', 'grad' => 'from-blue-500 to-indigo-600'],
            ['label' => 'Customers', 'value' => $stats['customers'], 'icon' => 'fa-users', 'grad' => 'from-violet-500 to-purple-600'],
            ['label' => 'Pending Reviews', 'value' => $stats['reviews'], 'icon' => 'fa-star', 'grad' => 'from-cyan-500 to-sky-600'],
        ];
        $maxSale = collect($salesChart)->max('value') ?: 1;
    @endphp

    {{-- Gradient stat cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        @foreach($cards as $card)
            <div class="rounded-2xl p-5 text-white bg-gradient-to-br {{ $card['grad'] }} shadow-card">
                <span class="w-10 h-10 rounded-xl bg-white/20 grid place-items-center text-lg mb-3"><i class="fa-solid {{ $card['icon'] }}"></i></span>
                <div class="text-2xl font-extrabold leading-tight">{{ $card['value'] }}</div>
                <div class="text-sm text-white/85 mt-0.5">{{ $card['label'] }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-[1fr_320px] gap-6 mt-6">
        {{-- Revenue chart (last 7 days) --}}
        <div class="bg-white rounded-2xl border border-line p-5">
            <div class="flex items-center justify-between mb-5">
                <h2 class="font-bold">Revenue — Last 7 Days</h2>
                <span class="text-xs text-muted">Total orders value per day</span>
            </div>
            <div class="flex items-end justify-between gap-2 sm:gap-4 h-52">
                @foreach($salesChart as $bar)
                    <div class="flex-1 flex flex-col items-center gap-2 group">
                        <div class="text-[10px] font-bold text-brand opacity-0 group-hover:opacity-100 transition">{{ bdt($bar['value']) }}</div>
                        <div class="w-full rounded-t-lg bg-gradient-to-t from-brand to-teal-400 transition-all hover:from-accent hover:to-orange-400"
                             style="height: {{ max(4, round($bar['value'] / $maxSale * 100)) }}%"></div>
                        <span class="text-xs text-muted">{{ $bar['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Order status breakdown + top products --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-line p-5">
                <h2 class="font-bold mb-4">Orders by Status</h2>
                <div class="space-y-2.5">
                    @foreach(\App\Models\Order::STATUS_LABELS as $key => $label)
                        @php $c = $statusBreakdown[$key] ?? 0; $pct = $stats['orders'] ? round($c / $stats['orders'] * 100) : 0; @endphp
                        <div>
                            <div class="flex justify-between text-xs mb-1"><span class="font-medium">{{ $label }}</span><span class="text-muted">{{ $c }}</span></div>
                            <div class="h-2 rounded-full bg-canvas overflow-hidden"><div class="h-full rounded-full bg-brand" style="width: {{ $pct }}%"></div></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-[1fr_320px] gap-6 mt-6">
        {{-- Recent orders --}}
        <div class="bg-white rounded-2xl border border-line">
            <div class="flex items-center justify-between p-5 border-b border-line">
                <h2 class="font-bold">Recent Orders</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-brand text-sm font-semibold hover:underline">View All →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-canvas text-muted text-left">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Order</th>
                            <th class="px-5 py-3 font-semibold">Customer</th>
                            <th class="px-5 py-3 font-semibold">Total</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-canvas">
                                <td class="px-5 py-3"><a href="{{ route('admin.orders.show', $order) }}" class="font-semibold text-brand">{{ $order->order_number }}</a></td>
                                <td class="px-5 py-3">{{ $order->name }}<div class="text-xs text-muted">{{ $order->phone }}</div></td>
                                <td class="px-5 py-3 font-bold">{{ bdt($order->total) }}</td>
                                <td class="px-5 py-3"><span class="text-xs font-semibold px-2 py-1 rounded-full bg-brand-light text-brand">{{ \App\Models\Order::STATUS_LABELS[$order->status] ?? $order->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-8 text-center text-muted">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Top products --}}
        <div class="bg-white rounded-2xl border border-line p-5">
            <h2 class="font-bold mb-4">Top Selling Products</h2>
            <div class="space-y-3">
                @forelse($topProducts as $p)
                    <div class="flex items-center gap-3">
                        <img src="{{ image_url($p->main_image, $p->name) }}" class="w-10 h-10 rounded-lg object-cover border border-line shrink-0">
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium truncate">{{ $p->name }}</div>
                            <div class="text-xs text-muted">{{ $p->order_items_count }} sold</div>
                        </div>
                        <span class="text-sm font-bold text-brand">{{ bdt($p->price) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-muted">No data yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
