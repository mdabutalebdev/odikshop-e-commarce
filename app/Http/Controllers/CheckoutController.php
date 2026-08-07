<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Services\Cart;
use App\Services\PipraPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    private const SHIPPING_FEE = 60;

    public function index(Cart $cart)
    {
        $items = $cart->items();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $subtotal = $cart->subtotal();
        $shippingFee = self::SHIPPING_FEE;
        $total = $subtotal + $shippingFee;

        return view('checkout', compact('items', 'subtotal', 'shippingFee', 'total'));
    }

    public function store(Request $request, Cart $cart)
    {
        $items = $cart->items();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:cod,piprapay'],
        ]);

        foreach ($items as $item) {
            if ($item->quantity > $item->product->stock) {
                return back()->withErrors(['quantity' => "Only {$item->product->stock} units of {$item->product->name} are available."])->withInput();
            }
        }

        $subtotal = $cart->subtotal();
        $shippingFee = self::SHIPPING_FEE;
        $total = $subtotal + $shippingFee;

        $order = DB::transaction(function () use ($data, $items, $subtotal, $shippingFee, $total) {
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => auth()->id(),
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'address' => $data['address'],
                'city' => $data['city'],
                'notes' => $data['notes'] ?? null,
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total' => $total,
                'payment_method' => $data['payment_method'],
                'payment_status' => 'unpaid',
                'status' => 'pending',
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'product_name' => $item->product->name,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            return $order;
        });

        $cart->clear();

        if ($order->payment_method === 'piprapay') {
            return $this->initiatePipraPayPayment($order);
        }

        return redirect()->route('checkout.confirmation', $order);
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
