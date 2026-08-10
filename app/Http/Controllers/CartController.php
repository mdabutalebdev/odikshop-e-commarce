<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Cart $cart)
    {
        $items = $cart->items();
        $subtotal = $cart->subtotal();

        return view('cart', compact('items', 'subtotal'));
    }

    /** Rendered cart-items fragment for the slide-out drawer (AJAX). */
    public function partial(Cart $cart)
    {
        return response()->json($this->cartPayload($cart));
    }

    private function cartPayload(Cart $cart): array
    {
        return [
            'ok' => true,
            'count' => $cart->count(),
            'subtotal' => $cart->subtotal(),
            'html' => view('partials.cart-items', [
                'items' => $cart->items(),
                'subtotal' => $cart->subtotal(),
            ])->render(),
        ];
    }

    public function add(Request $request, Cart $cart, Product $product)
    {
        $quantity = max(1, (int) $request->input('quantity', 1));
        
        $options = $request->input('options', []);

        // Validate that the customer picked one value from every variant group.
        if (! empty($product->attributes) && is_array($product->attributes)) {
            foreach ($product->attributes as $group) {
                $name = $group['name'] ?? null;
                $opts = array_filter($group['options'] ?? []);
                if ($name && ! empty($opts) && empty($options[$name])) {
                    return back()->withErrors(['options' => "অনুগ্রহ করে {$name} নির্বাচন করুন।"])->withInput();
                }
            }
        }

        if (is_array($options)) {
            $options = array_filter($options);
        } else {
            $options = [];
        }

        $cart->add($product, $quantity, $options);

        if ($request->input('redirect') === 'checkout') {
            return redirect()->route('checkout.index');
        }

        if ($request->wantsJson()) {
            return response()->json($this->cartPayload($cart) + ['message' => "{$product->name} added to cart."]);
        }

        return back()->with('status', "{$product->name} added to cart.");
    }

    public function update(Request $request, Cart $cart, string $id)
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:0']]);

        $cart->update($id, $data['quantity']);

        if ($request->wantsJson()) {
            return response()->json($this->cartPayload($cart));
        }

        return back()->with('status', 'Cart updated.');
    }

    public function remove(Request $request, Cart $cart, string $id)
    {
        $cart->remove($id);

        if ($request->wantsJson()) {
            return response()->json($this->cartPayload($cart));
        }

        return back()->with('status', 'Item removed from cart.');
    }
}
