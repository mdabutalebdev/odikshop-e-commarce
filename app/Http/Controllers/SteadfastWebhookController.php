<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\SteadfastService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Receives delivery-status callbacks from the Steadfast merchant portal.
 *
 * The URL is public, so the payload is only trusted after the HMAC signature
 * checks out against the webhook token configured in both places.
 */
class SteadfastWebhookController extends Controller
{
    public function __invoke(Request $request, SteadfastService $steadfast)
    {
        if (! $steadfast->verifyWebhookSignature($request->getContent(), $request->header('X-Signature'))) {
            Log::warning('Steadfast webhook rejected: bad signature', ['ip' => $request->ip()]);

            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 401);
        }

        $consignmentId = $request->input('consignment_id');
        $invoice = $request->input('invoice');
        $deliveryStatus = $request->input('status') ?? $request->input('delivery_status');

        $order = Order::query()
            ->when($consignmentId, fn ($query) => $query->orWhere('courier_consignment_id', $consignmentId))
            ->when($invoice, fn ($query) => $query->orWhere('order_number', $invoice))
            ->first();

        if (! $order) {
            // Acknowledge anyway: retrying would not help find an order we do not have.
            Log::info('Steadfast webhook for unknown order', ['consignment_id' => $consignmentId, 'invoice' => $invoice]);

            return response()->json(['status' => 'ok', 'message' => 'Order not found']);
        }

        $changes = array_filter([
            'courier_status' => $deliveryStatus,
            'courier_tracking_code' => $request->input('tracking_code'),
            'courier_note' => $request->input('note') ?? $request->input('reason'),
        ], fn ($value) => $value !== null);

        // Mirror the courier status onto the order, but never undo a cancellation
        // the shop made by hand.
        if ($mapped = $steadfast->mapDeliveryStatus($deliveryStatus)) {
            if ($order->status !== 'cancelled' || $mapped === 'delivered') {
                $changes['status'] = $mapped;
            }
        }

        // A delivered COD parcel means the courier collected the money.
        if (in_array($deliveryStatus, SteadfastService::DELIVERED_STATUSES, true) && $order->payment_status !== 'paid') {
            $changes['payment_status'] = 'paid';
        }

        $order->forceFill($changes)->save();

        return response()->json(['status' => 'ok']);
    }
}
