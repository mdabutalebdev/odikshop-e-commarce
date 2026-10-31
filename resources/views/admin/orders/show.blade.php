<x-admin-layout title="Order {{ $order->order_number }}">
    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders.index') }}" class="w-9 h-9 rounded-lg border border-line bg-white hover:bg-canvas flex items-center justify-center text-muted hover:text-ink transition">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-ink flex items-center gap-2">
                    Order {{ $order->order_number }}
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-brand-light text-brand">
                        {{ \App\Models\Order::STATUS_LABELS[$order->status] ?? ucfirst($order->status) }}
                    </span>
                </h1>
                <p class="text-xs text-muted mt-0.5">{{ $order->created_at->format('d M, Y h:i A') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('admin.orders.bulk_print') }}" target="_blank">
                @csrf
                <input type="hidden" name="order_ids[]" value="{{ $order->id }}">
                <button type="submit" class="h-9 px-3.5 rounded-lg border border-line bg-white hover:bg-canvas text-xs font-semibold text-ink flex items-center gap-1.5 shadow-sm transition">
                    <i class="fa-solid fa-print text-muted"></i> Print Invoice
                </button>
            </form>
        </div>
    </div>

    @php 
        $steadfast = app(\App\Services\SteadfastService::class);
        $sfConfigured = $steadfast->isConfigured();
        $isBooked = filled($order->courier_consignment_id);
    @endphp

    <div class="grid lg:grid-cols-12 gap-6">
        {{-- Left Column: Items & Details --}}
        <div class="lg:col-span-7 space-y-6">
            {{-- Order Items --}}
            <div class="bg-white rounded-2xl border border-line overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-line flex items-center justify-between">
                    <h2 class="font-bold text-ink text-base">Items</h2>
                    <span class="text-xs text-muted">{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}</span>
                </div>
                <div class="p-6">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs text-muted uppercase tracking-wider border-b border-line pb-2">
                                <th class="text-left font-semibold pb-3">Product</th>
                                <th class="text-right font-semibold pb-3">Price</th>
                                <th class="text-center font-semibold pb-3">Qty</th>
                                <th class="text-right font-semibold pb-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="py-3.5 pr-4">
                                        <div class="font-semibold text-ink text-sm">{{ $item->name }}</div>
                                        @if(!empty($item->options))
                                            <div class="text-xs text-muted mt-0.5">
                                                @foreach($item->options as $k => $v)
                                                    <span class="mr-2">{{ ucfirst($k) }}: <strong class="text-ink">{{ $v }}</strong></span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 text-right text-muted whitespace-nowrap">{{ bdt($item->price) }}</td>
                                    <td class="py-3.5 text-center font-semibold text-ink whitespace-nowrap">{{ $item->quantity }}</td>
                                    <td class="py-3.5 text-right font-bold text-ink whitespace-nowrap">{{ bdt($item->subtotal) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="border-t border-line mt-4 pt-4 space-y-2 text-sm max-w-xs ml-auto">
                        <div class="flex justify-between text-muted">
                            <span>Subtotal</span>
                            <span class="font-semibold text-ink">{{ bdt($order->subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-muted">
                            <span>Shipping</span>
                            <span class="font-semibold text-ink">{{ bdt($order->shipping_fee) }}</span>
                        </div>
                        <div class="flex justify-between text-base font-bold text-ink border-t border-line pt-2">
                            <span>Total</span>
                            <span class="text-brand">{{ bdt($order->total) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notes & Status --}}
            <div class="bg-white rounded-2xl border border-line p-6 shadow-sm">
                <h2 class="font-bold text-ink text-base mb-4">Notes & Status</h2>
                <div class="space-y-4">
                    <div>
                        <div class="text-xs font-semibold text-muted uppercase tracking-wider mb-1">Customer Note</div>
                        <div class="p-3 bg-canvas/40 rounded-xl border border-line/60 text-sm text-ink min-h-[46px]">
                            {{ $order->notes ?: 'No customer note provided.' }}
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="pt-2 border-t border-line grid sm:grid-cols-2 gap-4">
                        @csrf @method('PATCH')
                        <div>
                            <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1.5">Order Status</label>
                            <select name="status" class="w-full h-10 rounded-xl border border-line px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand/30">
                                @foreach(\App\Models\Order::STATUS_LABELS as $key => $label)
                                    <option value="{{ $key }}" @selected($order->status===$key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1.5">Payment Status</label>
                            <select name="payment_status" class="w-full h-10 rounded-xl border border-line px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand/30">
                                @foreach(['unpaid' => 'Unpaid', 'paid' => 'Paid', 'failed' => 'Failed'] as $key => $label)
                                    <option value="{{ $key }}" @selected($order->payment_status===$key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-2 flex justify-end">
                            <button type="submit" class="h-10 px-5 rounded-xl bg-ink text-white text-xs font-bold hover:bg-black transition">
                                Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right Column: Shipping Details & Steadfast Card --}}
        <div class="lg:col-span-5 space-y-6">
            {{-- Shipping Details Card --}}
            <div class="bg-white rounded-2xl border border-line p-6 shadow-sm">
                <div class="flex items-center justify-between pb-3 border-b border-line mb-4">
                    <h2 class="font-bold text-ink text-base">Shipping Details</h2>
                    <span class="text-[11px] font-mono text-muted">#{{ $order->order_number }}</span>
                </div>

                <div class="space-y-3.5 text-sm">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-muted flex items-center gap-1.5">
                            <i class="fa-solid fa-user text-muted/80"></i> Receiver
                        </div>
                        <div class="font-semibold text-ink mt-0.5 text-sm">{{ $order->name }}</div>
                    </div>

                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-muted flex items-center gap-1.5">
                            <i class="fa-solid fa-phone text-muted/80"></i> Phone
                        </div>
                        <div class="flex items-center justify-between mt-0.5">
                            <span class="font-semibold text-ink font-mono">{{ $order->phone }}</span>
                            <a href="tel:{{ $order->phone }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200 flex items-center gap-1">
                                <i class="fa-solid fa-phone text-[10px]"></i> Call
                            </a>
                        </div>
                    </div>

                    @if($order->user?->email)
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-muted flex items-center gap-1.5">
                                <i class="fa-solid fa-envelope text-muted/80"></i> Email
                            </div>
                            <div class="text-ink mt-0.5 text-xs font-mono">{{ $order->user->email }}</div>
                        </div>
                    @endif

                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-muted flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot text-muted/80"></i> Address
                        </div>
                        <div class="text-ink mt-0.5 text-sm leading-relaxed bg-canvas/40 p-3 rounded-xl border border-line/60">
                            {{ $order->address }}
                            @if($order->thana || $order->district)
                                <div class="text-xs text-muted font-medium mt-1">
                                    {{ implode(', ', array_filter([$order->thana, $order->district, $order->division])) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-2 border-t border-line grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-muted block text-[10px] uppercase font-bold">Zone</span>
                            <span class="font-semibold text-ink">{{ $order->delivery_zone === 'inside_dhaka' ? 'Inside Dhaka' : 'Outside Dhaka' }}</span>
                        </div>
                        <div>
                            <span class="text-muted block text-[10px] uppercase font-bold">Payment Method</span>
                            <span class="font-semibold text-ink">{{ $order->payment_method === 'cod' ? 'Cash on Delivery' : 'Mobile Banking' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Steadfast Courier Box (Raimart Style) --}}
            <div class="bg-white rounded-2xl border border-line p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-line mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-200 flex items-center justify-center text-orange-600 shadow-sm">
                            <i class="fa-solid fa-truck-fast text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-ink text-sm leading-tight">Steadfast Courier</h3>
                            <p class="text-[11px] text-muted leading-tight mt-0.5">Parcel Delivery &amp; Tracking</p>
                        </div>
                    </div>
                    <div>
                        @if($isBooked)
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                {{ ucfirst(str_replace('_', ' ', $order->courier_status ?: 'Booked')) }}
                            </span>
                        @else
                            <span class="inline-flex items-center text-xs font-bold px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                Not Booked
                            </span>
                        @endif
                    </div>
                </div>

                @if($isBooked)
                    {{-- Already Booked Info --}}
                    <div class="space-y-3 bg-canvas/40 p-4 rounded-xl border border-line/70 text-xs">
                        <div class="flex justify-between items-center py-1 border-b border-line/60">
                            <span class="text-muted font-medium">Consignment ID</span>
                            <strong class="font-mono text-ink text-sm font-bold">{{ $order->courier_consignment_id }}</strong>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-line/60">
                            <span class="text-muted font-medium">Tracking Code</span>
                            <strong class="font-mono text-brand font-bold">{{ $order->courier_tracking_code ?: '—' }}</strong>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-line/60">
                            <span class="text-muted font-medium">Delivery Status</span>
                            <span class="font-bold text-ink">{{ ucfirst(str_replace('_', ' ', $order->courier_status ?: 'in review')) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1">
                            <span class="text-muted font-medium">Booked At</span>
                            <span class="text-ink">{{ optional($order->courier_booked_at)->format('d M, Y h:i A') ?: '—' }}</span>
                        </div>
                        @if($order->courier_note)
                            <div class="pt-2 border-t border-line/60">
                                <span class="text-muted block text-[10px] uppercase font-bold">Courier Note</span>
                                <span class="text-ink mt-0.5 block italic">{{ $order->courier_note }}</span>
                            </div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('admin.steadfast.refresh', $order) }}" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full h-11 rounded-xl border border-line bg-white hover:bg-canvas text-xs font-bold text-ink flex items-center justify-center gap-2 shadow-sm transition">
                            <i class="fa-solid fa-rotate text-muted"></i> Refresh Courier Status
                        </button>
                    </form>
                @else
                    {{-- Booking Form (Raimart Style) --}}
                    @if($sfConfigured)
                        <form method="POST" action="{{ route('admin.steadfast.book', $order) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-ink mb-1.5">COD to collect:</label>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3.5 text-xs text-muted font-bold">৳</span>
                                    <input type="number" step="0.01" name="cod_amount"
                                           value="{{ $order->payment_method === 'cod' && $order->payment_status !== 'paid' ? (float) $order->total : 0 }}"
                                           class="w-full h-11 pl-8 pr-4 rounded-xl border border-line text-sm font-bold text-ink bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-ink mb-1.5">Delivery note:</label>
                                <input type="text" name="delivery_note"
                                       placeholder="Fragile / Check parcel before receive"
                                       value="{{ $order->notes }}"
                                       class="w-full h-11 px-3.5 rounded-xl border border-line text-xs text-ink bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500">
                            </div>

                            <button type="submit" class="w-full h-11 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center justify-center gap-2 shadow-sm hover:shadow transition">
                                <i class="fa-solid fa-paper-plane text-[11px]"></i> Send to Steadfast
                            </button>
                        </form>
                    @else
                        <div class="p-4 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-800 space-y-2">
                            <div class="flex items-center gap-2 font-bold">
                                <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                                Steadfast Credentials Missing
                            </div>
                            <p class="text-[11px] text-amber-700 leading-relaxed">
                                Please configure your Steadfast API Key and Secret Key in settings to enable parcel booking.
                            </p>
                            <a href="{{ route('admin.steadfast.index') }}" class="inline-block mt-1 font-bold underline hover:text-amber-950">
                                Go to Steadfast Settings &rarr;
                            </a>
                        </div>
                    @endif
                @endif

                {{-- Customer Delivery Record Footer Link --}}
                <div class="mt-4 pt-3.5 border-t border-line flex items-center justify-between text-xs text-muted">
                    <span class="flex items-center gap-1.5 font-medium">
                        <i class="fa-solid fa-shield-halved text-emerald-600"></i> Customer Delivery Record
                    </span>
                    <a href="tel:{{ $order->phone }}" class="text-xs font-semibold text-brand hover:underline flex items-center gap-1">
                        <i class="fa-solid fa-magnifying-glass text-[10px]"></i> Check Record
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
