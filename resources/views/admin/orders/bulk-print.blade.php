<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Print Invoices</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f3f4f6; color: #1f2937; }
        .invoice-page { 
            background: white; 
            width: 100%; 
            max-width: 800px; 
            margin: 0 auto 2rem auto; 
            padding: 2rem; 
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        @media print {
            body { background: white; margin: 0; padding: 0; }
            .invoice-page { 
                box-shadow: none; 
                margin: 0; 
                padding: 0; 
                max-width: 100%;
                border-radius: 0;
                page-break-after: always; 
            }
            .invoice-page:last-child { page-break-after: auto; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="max-w-[800px] mx-auto mb-4 mt-4 no-print flex justify-end">
        <button onclick="window.print()" class="bg-brand text-white px-6 py-2 rounded-lg font-bold">Print All</button>
    </div>

    @foreach($orders as $order)
        <div class="invoice-page">
            <div class="flex justify-between items-start mb-8 border-b border-gray-200 pb-4">
                <div>
                    <img src="{{ asset('images/logo-wide.png') }}" alt="Logo" class="h-14 w-auto mb-2">
                    <div class="text-sm text-gray-500">
                        {{ \App\Models\SiteSetting::getAll()['address'] ?? 'Dhaka, Bangladesh' }}<br>
                        Phone: {{ \App\Models\SiteSetting::getAll()['phone'] ?? '' }}
                    </div>
                </div>
                <div class="text-right">
                    <h1 class="text-2xl font-bold text-gray-800">INVOICE</h1>
                    <div class="font-bold text-brand text-lg">#{{ $order->order_number }}</div>
                    <div class="text-sm text-gray-500">{{ $order->created_at->format('d M Y, g:i A') }}</div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8 mb-8">
                <div>
                    <h3 class="font-bold text-gray-800 mb-2 border-b border-gray-200 pb-1">Billed To:</h3>
                    <div class="text-sm font-semibold">{{ $order->name }}</div>
                    <div class="text-sm">{{ $order->phone }}</div>
                    <div class="text-sm">{{ $order->address }}</div>
                    <div class="text-sm">{{ $order->thana ? $order->thana.', ' : '' }}{{ $order->district ? $order->district.', ' : '' }}{{ $order->division }}</div>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 mb-2 border-b border-gray-200 pb-1">Payment Info:</h3>
                    <div class="text-sm"><span class="font-semibold">Method:</span> {{ $order->payment_method === 'cod' ? 'Cash on Delivery' : 'Mobile Banking' }}</div>
                    @if($order->payment_method === 'mobile_banking')
                        <div class="text-sm"><span class="font-semibold">Channel:</span> {{ $order->payment_channel ? ucfirst($order->payment_channel) : '—' }}</div>
                        <div class="text-sm"><span class="font-semibold">Sender:</span> {{ $order->payment_sender_number ?: '—' }}</div>
                        <div class="text-sm"><span class="font-semibold">TrxID:</span> {{ $order->payment_txn_id ?: '—' }}</div>
                    @endif
                    <div class="text-sm"><span class="font-semibold">Status:</span> {{ ucfirst($order->payment_status) }}</div>
                    @if($order->notes)
                        <div class="text-sm mt-2"><span class="font-semibold">Notes:</span> {{ $order->notes }}</div>
                    @endif
                </div>
            </div>

            <table class="w-full text-sm mb-8">
                <thead class="bg-gray-100 text-gray-600 text-left">
                    <tr>
                        <th class="px-3 py-2 font-semibold rounded-tl-lg">Item</th>
                        <th class="px-3 py-2 font-semibold text-center">Qty</th>
                        <th class="px-3 py-2 font-semibold text-right">Price</th>
                        <th class="px-3 py-2 font-semibold text-right rounded-tr-lg">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 border-b border-gray-200">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="px-3 py-3 font-medium">{{ $item->name }}</td>
                            <td class="px-3 py-3 text-center">{{ $item->quantity }}</td>
                            <td class="px-3 py-3 text-right">{{ bdt($item->price) }}</td>
                            <td class="px-3 py-3 text-right font-semibold">{{ bdt($item->subtotal) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="flex justify-end text-sm">
                <div class="w-64 space-y-2">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span>{{ bdt($order->subtotal) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Shipping Fee</span>
                        <span>{{ bdt($order->shipping_fee) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2 mt-2">
                        <span>Total</span>
                        <span class="text-brand">{{ bdt($order->total) }}</span>
                    </div>
                </div>
            </div>
            
            <div class="mt-16 text-center text-sm text-gray-500 border-t border-gray-200 pt-4">
                Thank you for shopping with ODHIK SHOP!
            </div>
        </div>
    @endforeach

    <script>
        // Auto print on load
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
