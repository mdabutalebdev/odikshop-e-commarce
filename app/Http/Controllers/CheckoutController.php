<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Services\Cart;
use App\Services\PipraPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index(Request $request, Cart $cart)
    {
        [$items, $subtotal, $buy, $buyQty] = $this->resolveItems($request, $cart);

        if ($items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $settings = SiteSetting::getAll();
        $insideFee = (int) ($settings['shipping_inside_dhaka'] ?? 60);
        $outsideFee = (int) ($settings['shipping_outside_dhaka'] ?? 120);
        $bdGeo = config('bd_geo', []);

        return view('checkout', compact('items', 'subtotal', 'insideFee', 'outsideFee', 'buy', 'buyQty', 'bdGeo'));
    }

    public function store(Request $request, Cart $cart)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'district' => ['required', 'string', 'max:100'],
            'thana' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'delivery_zone' => ['required', 'in:inside_dhaka,outside_dhaka'],
            'payment_method' => ['required', 'in:cod,mobile_banking'],
            'buy' => ['nullable', 'integer'],
            'qty' => ['nullable', 'integer', 'min:1'],
        ], [
            'name.required' => 'পূর্ণ নাম দিন।',
            'phone.required' => 'মোবাইল নম্বর দিন।',
            'district.required' => 'জেলা নির্বাচন করুন।',
            'thana.required' => 'থানা নির্বাচন করুন।',
            'address.required' => 'ঠিকানা দিন।',
        ]);

        [$items, $subtotal, $buy, $buyQty] = $this->resolveItems($request, $cart);

        if ($items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        foreach ($items as $item) {
            if ($item->quantity > $item->product->stock) {
                return back()->withErrors(['quantity' => "Only {$item->product->stock} unit(s) of {$item->product->name} are in stock."])->withInput();
            }
        }

        $settings = SiteSetting::getAll();
        $shippingFee = $data['delivery_zone'] === 'inside_dhaka'
            ? (int) ($settings['shipping_inside_dhaka'] ?? 60)
            : (int) ($settings['shipping_outside_dhaka'] ?? 120);
        $total = $subtotal + $shippingFee;

        $order = DB::transaction(function () use ($data, $items, $subtotal, $shippingFee, $total) {
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => auth()->id(),
                'name' => $data['name'],
                'phone' => $data['phone'],
                'division' => null,
                'district' => $data['district'] ?? null,
                'thana' => $data['thana'] ?? null,
                'address' => $data['address'],
                'notes' => $data['notes'] ?? null,
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total' => $total,
                'delivery_zone' => $data['delivery_zone'],
                'payment_method' => $data['payment_method'],
                'payment_status' => 'unpaid',
                'status' => 'pending',
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'name' => $item->product->name,
                    'options' => $item->options ?? [],
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            return $order;
        });

        // Only empty the session cart for a normal cart checkout — a "buy now"
        // order never touched the cart.
        if (! $request->filled('buy')) {
            $cart->clear();
        }

        return redirect()->route('checkout.confirmation', $order);
    }

    /**
     * Resolve the line items for checkout: either a single "buy now" product
     * (via ?buy=ID&qty=N) or the current session cart.
     *
     * @return array{0: Collection, 1: float|int, 2: int|null, 3: int}
     */
    private function resolveItems(Request $request, Cart $cart): array
    {
        $buyQty = max(1, (int) $request->input('qty', 1));
        $buyId = (int) $request->input('buy');

        if ($buyId) {
            $product = Product::find($buyId);

            if (! $product) {
                return [collect(), 0, null, $buyQty];
            }

            return [collect([$cart->makeItem($product, $buyQty)]), $product->price * $buyQty, $buyId, $buyQty];
        }

        return [$cart->items(), $cart->subtotal(), null, $buyQty];
    }

    public function pay(Order $order)
    {
        abort_if($order->payment_method !== 'piprapay' || $order->payment_status === 'paid', 404);

        return $this->initiatePipraPayPayment($order);
    }

    public function callback(Request $request, Order $order, PipraPayService $pipraPay)
    {
        $ppId = $request->query('pp_id');

        if ($ppId) {
            $this->verifyAndUpdate($order, $ppId, $pipraPay);
        }

        return redirect()->route('checkout.confirmation', $order);
    }

    public function cancel(Order $order)
    {
        return redirect()->route('checkout.confirmation', $order)->with('status', 'Payment was cancelled. You can retry payment below.');
    }

    public function webhook(Request $request, PipraPayService $pipraPay)
    {
        $ppId = $request->input('pp_id');

        if (! $ppId) {
            return response()->json(['status' => false], 400);
        }

        $payment = Payment::where('pp_id', $ppId)->first();

        if ($payment) {
            $this->verifyAndUpdate($payment->order, $ppId, $pipraPay);
        }

        return response()->json(['status' => true]);
    }

    public function confirmation(Order $order)
    {
        $order->load('items');

        return view('checkout-confirmation', compact('order'));
    }

    private function initiatePipraPayPayment(Order $order)
    {
        $pipraPay = app(PipraPayService::class);

        if (! $pipraPay->isConfigured()) {
            return redirect()->route('checkout.confirmation', $order)
                ->with('status', 'Online payment is not available right now. Please contact support or choose Cash on Delivery for a new order.');
        }

        $response = $pipraPay->createCharge([
            'full_name' => $order->name,
            'email_mobile' => $order->email ?: $order->phone,
            'amount' => $order->total,
            'metadata' => ['order_number' => $order->order_number],
            'redirect_url' => route('checkout.callback', $order),
            'cancel_url' => route('checkout.cancel', $order),
            'webhook_url' => route('checkout.webhook'),
        ]);

        if (! ($response['status'] ?? false)) {
            Log::warning('PipraPay createCharge failed', ['order' => $order->order_number, 'response' => $response]);

            return redirect()->route('checkout.confirmation', $order)
                ->with('status', 'Could not start online payment: '.($response['message'] ?? 'unknown error').'. Please retry or contact support.');
        }

        Payment::create([
            'order_id' => $order->id,
            'gateway' => 'piprapay',
            'pp_id' => $response['pp_id'],
            'amount' => $order->total,
            'status' => 'pending',
            'raw_response' => $response,
        ]);

        return redirect()->away($response['pp_url']);
    }

    private function verifyAndUpdate(Order $order, string $ppId, PipraPayService $pipraPay): void
    {
        $result = $pipraPay->verifyPayment($ppId);
        $isCompleted = ($result['status'] ?? null) === 'completed';

        $payment = Payment::where('order_id', $order->id)->where('pp_id', $ppId)->first();

        if ($payment) {
            $payment->update([
                'transaction_id' => $result['transaction_id'] ?? null,
                'status' => $isCompleted ? 'completed' : 'failed',
                'raw_response' => $result,
            ]);
        }

        if ($isCompleted) {
            $order->update(['payment_status' => 'paid', 'status' => 'processing']);
        }
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'RM'.now()->format('ymd').strtoupper(Str::random(5));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
