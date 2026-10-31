<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SiteSetting;
use App\Services\SteadfastService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SteadfastController extends Controller
{
    private const TOGGLES = ['steadfast_enabled', 'steadfast_auto_book'];

    public function index(SteadfastService $steadfast)
    {
        return view('admin.steadfast.index', [
            'settings' => SiteSetting::getAll(),
            'steadfast' => $steadfast,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'steadfast_api_key' => ['nullable', 'string', 'max:255'],
            'steadfast_secret_key' => ['nullable', 'string', 'max:255'],
            'steadfast_webhook_token' => ['nullable', 'string', 'max:255'],
            'steadfast_base_url' => ['nullable', 'url', 'max:255'],
        ]);

        // An empty secret field means "keep the saved one" rather than wipe it,
        // because the form renders it masked.
        if (blank($data['steadfast_secret_key'])) {
            unset($data['steadfast_secret_key']);
        }

        $data['steadfast_base_url'] = $data['steadfast_base_url'] ?: SteadfastService::DEFAULT_BASE_URL;

        foreach ($data as $key => $value) {
            SiteSetting::set($key, $value);
        }

        foreach (self::TOGGLES as $toggle) {
            SiteSetting::set($toggle, $request->boolean($toggle) ? '1' : '0');
        }

        return back()->with('success', 'Steadfast settings saved.');
    }

    /** Generate a webhook signing token for the merchant portal. */
    public function generateToken()
    {
        return response()->json(['token' => Str::random(40)]);
    }

    public function ping(SteadfastService $steadfast)
    {
        return response()->json($steadfast->ping());
    }

    public function balance(SteadfastService $steadfast)
    {
        $result = $steadfast->balance();

        return response()->json([
            'ok' => $result['ok'],
            'message' => $result['message'],
            'balance' => $result['data']['current_balance'] ?? null,
        ]);
    }

    /** Look a parcel up by consignment id, tracking code or our invoice number. */
    public function track(Request $request, SteadfastService $steadfast)
    {
        $request->validate(['reference' => ['required', 'string', 'max:100']]);

        $reference = trim($request->input('reference'));

        $result = ctype_digit($reference)
            ? $steadfast->statusByConsignmentId($reference)
            : $steadfast->statusByTrackingCode($reference);

        // Fall back to our own invoice number, which is how orders are numbered here.
        if (! $result['ok'] || blank($result['data']['delivery_status'] ?? null)) {
            $byInvoice = $steadfast->statusByInvoice($reference);

            if ($byInvoice['ok'] && filled($byInvoice['data']['delivery_status'] ?? null)) {
                $result = $byInvoice;
            }
        }

        return response()->json([
            'ok' => $result['ok'],
            'message' => $result['message'],
            'status' => $result['data']['delivery_status'] ?? null,
            'reference' => $reference,
        ]);
    }

    /** Book a single order from the order detail page. */
    public function bookOrder(Request $request, Order $order, SteadfastService $steadfast)
    {
        $codAmount = $request->filled('cod_amount') ? (float) $request->input('cod_amount') : null;
        $note = $request->filled('delivery_note') ? (string) $request->input('delivery_note') : null;

        $result = $steadfast->bookOrder($order, $codAmount, $note);

        return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    /** Pull the live courier status for one order. */
    public function refreshOrder(Order $order, SteadfastService $steadfast)
    {
        $result = $steadfast->refreshOrderStatus($order);

        return back()->with(
            $result['ok'] ? 'success' : 'error',
            $result['ok'] ? 'Courier status updated: '.($order->fresh()->courier_status ?? 'unknown') : $result['message']
        );
    }
}
