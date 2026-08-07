<x-admin-layout title="অর্ডার">
    <form method="GET" class="flex items-center gap-2 mb-4 flex-wrap">
        <div class="relative flex-1 max-w-xs">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="অর্ডার/ফোন খুঁজুন..." class="w-full h-10 rounded-lg border border-line pl-9 pr-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand/30">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-muted text-sm"></i>
        </div>
        <select name="status" onchange="this.form.submit()" class="h-10 rounded-lg border border-line px-3 text-sm bg-white">
            <option value="">সব স্ট্যাটাস</option>
            @foreach(\App\Models\Order::STATUS_LABELS as $key => $label)
                <option value="{{ $key }}" @selected(request('status')===$key)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="h-10 px-4 rounded-lg bg-brand text-white text-sm font-semibold">ফিল্টার</button>
    </form>

    <div class="bg-white rounded-xl border border-line overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-canvas text-muted text-left">
                    <tr>
                        <th class="px-4 py-3 font-semibold">অর্ডার নং</th>
                        <th class="px-4 py-3 font-semibold">গ্রাহক</th>
                        <th class="px-4 py-3 font-semibold">পণ্য</th>
                        <th class="px-4 py-3 font-semibold">মোট</th>
                        <th class="px-4 py-3 font-semibold">পেমেন্ট</th>
                        <th class="px-4 py-3 font-semibold">স্ট্যাটাস</th>
                        <th class="px-4 py-3 font-semibold text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($orders as $order)
                        <tr class="hover:bg-canvas">
                            <td class="px-4 py-3"><a href="{{ route('admin.orders.show', $order) }}" class="font-semibold text-brand">{{ $order->order_number }}</a><div class="text-xs text-muted">{{ $order->created_at->format('d M, g:i A') }}</div></td>
                            <td class="px-4 py-3">{{ $order->name }}<div class="text-xs text-muted">{{ $order->phone }}</div></td>
                            <td class="px-4 py-3">{{ $order->items_count }} টি</td>
                            <td class="px-4 py-3 font-bold">{{ bdt($order->total) }}</td>
                            <td class="px-4 py-3"><span class="text-xs">{{ $order->payment_method === 'cod' ? 'COD' : 'মোবাইল' }}</span></td>
                            <td class="px-4 py-3"><span class="text-xs font-semibold px-2 py-1 rounded-full bg-brand-light text-brand">{{ \App\Models\Order::STATUS_LABELS[$order->status] ?? $order->status }}</span></td>
                            <td class="px-4 py-3 text-right"><a href="{{ route('admin.orders.show', $order) }}" class="text-brand"><i class="fa-solid fa-eye"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-muted">কোনো অর্ডার নেই।</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
</x-admin-layout>
