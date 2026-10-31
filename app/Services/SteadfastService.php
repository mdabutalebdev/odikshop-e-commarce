<?php

namespace App\Services;

use App\Models\Order;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Steadfast Courier (Packzy) merchant API v1.
 *
 * Every call carries the Api-Key / Secret-Key pair from the admin page. The
 * methods all return the same {ok, message, data} envelope so controllers never
 * have to care whether a failure came from HTTP, the courier, or a thrown
 * exception — booking a parcel must never be able to break an order.
 *
 * @see https://portal.packzy.com/api/v1
 */
class SteadfastService
{
    public const DEFAULT_BASE_URL = 'https://portal.packzy.com/api/v1';

    /** Settings keys owned by the Steadfast admin page. */
    public const KEYS = [
        'steadfast_enabled',
        'steadfast_auto_book',
        'steadfast_api_key',
        'steadfast_secret_key',
        'steadfast_webhook_token',
        'steadfast_base_url',
    ];

    /** Courier delivery statuses that mean the parcel reached the customer. */
    public const DELIVERED_STATUSES = ['delivered', 'partial_delivered'];

    /** @var array<string, string|null>|null */
    private ?array $settings = null;

    public function setting(string $key, ?string $default = null): ?string
    {
        $this->settings ??= SiteSetting::getAll();

        $value = $this->settings[$key] ?? null;

        return ($value === null || $value === '') ? $default : $value;
    }

    public function isConfigured(): bool
    {
        return filled($this->setting('steadfast_api_key'))
            && filled($this->setting('steadfast_secret_key'));
    }

    /** Automatic booking only runs when the module and the toggle are both on. */
    public function autoBookEnabled(): bool
    {
        return $this->isConfigured()
            && (bool) (int) $this->setting('steadfast_enabled', '0')
            && (bool) (int) $this->setting('steadfast_auto_book', '0');
    }

    public function baseUrl(): string
    {
        return rtrim($this->setting('steadfast_base_url', self::DEFAULT_BASE_URL), '/');
    }

    /*
    |--------------------------------------------------------------------------
    | Endpoints
    |--------------------------------------------------------------------------
    */

    /** Current COD payout balance held by the courier. */
    public function balance(): array
    {
        return $this->request('get', 'get_balance');
    }

    /**
     * Book one parcel.
     *
     * recipient_name / recipient_phone / recipient_address / cod_amount are
     * required by Steadfast; invoice must be unique per merchant account.
     */
    public function createOrder(array $payload): array
    {
        return $this->request('post', 'create_order', $payload);
    }

    public function statusByConsignmentId(string $consignmentId): array
    {
        return $this->request('get', 'status_by_cid/'.rawurlencode($consignmentId));
    }

    public function statusByInvoice(string $invoice): array
    {
        return $this->request('get', 'status_by_invoice/'.rawurlencode($invoice));
    }

    public function statusByTrackingCode(string $trackingCode): array
    {
        return $this->request('get', 'status_by_trackingcode/'.rawurlencode($trackingCode));
    }

    /** Cheap reachability probe for the admin "Test Ping" button. */
    public function ping(): array
    {
        $result = $this->balance();

        return [
            'ok' => $result['ok'],
            'message' => $result['ok'] ? 'Steadfast API reachable and credentials accepted.' : $result['message'],
            'data' => $result['data'],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Order helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Turn one of our orders into the payload Steadfast expects.
     *
     * COD is only collected for unpaid cash-on-delivery orders; a prepaid order
     * is booked with a zero COD amount so the rider does not ask for money.
     */
    public function payloadForOrder(Order $order): array
    {
        $isPrepaid = $order->payment_status === 'paid' || $order->payment_method !== 'cod';

        $address = collect([$order->address, $order->thana, $order->district])
            ->filter()
            ->implode(', ');

        $itemSummary = $order->items
            ->map(fn ($item) => $item->name.' x'.$item->quantity)
            ->implode(', ');

        return array_filter([
            'invoice' => $order->order_number,
            'recipient_name' => $order->name,
            'recipient_phone' => $this->normalizePhone($order->phone),
            'recipient_address' => $address,
            'cod_amount' => $isPrepaid ? 0 : (float) $order->total,
            'note' => trim($order->notes.' | '.$itemSummary, ' |') ?: null,
        ], fn ($value) => $value !== null);
    }

    /**
     * Book an order and persist the consignment details.
     *
     * @return array{ok: bool, message: string, data: array}
     */
    public function bookOrder(Order $order, ?float $codAmount = null, ?string $customNote = null): array
    {
        if (! $this->isConfigured()) {
            return ['ok' => false, 'message' => 'Steadfast API credentials are not configured.', 'data' => []];
        }

        if (filled($order->courier_consignment_id)) {
            return ['ok' => false, 'message' => 'This order is already booked with Steadfast.', 'data' => []];
        }

        $order->loadMissing('items');
        $payload = $this->payloadForOrder($order);

        if ($codAmount !== null) {
            $payload['cod_amount'] = max(0, $codAmount);
        }

        if ($customNote !== null && trim($customNote) !== '') {
            $payload['note'] = trim($customNote);
        }

        $result = $this->createOrder($payload);

        if (! $result['ok']) {
            return $result;
        }

        // Steadfast wraps the parcel in a `consignment` object; fall back to the
        // top level so a flat response still books instead of saving nulls.
        $consignment = $result['data']['consignment'] ?? $result['data'] ?? [];

        if (blank($consignment['consignment_id'] ?? null)) {
            return ['ok' => false, 'message' => 'Steadfast accepted the request but returned no consignment id.', 'data' => $result['data']];
        }

        $order->forceFill([
            'courier_consignment_id' => $consignment['consignment_id'] ?? null,
            'courier_tracking_code' => $consignment['tracking_code'] ?? null,
            'courier_status' => $consignment['status'] ?? 'in_review',
            'courier_booked_at' => now(),
            'courier_note' => $customNote ?: null,
            'status' => 'processing',
        ])->save();

        return ['ok' => true, 'message' => 'Parcel booked with Steadfast.', 'data' => $consignment];
    }

    /** Pull the latest delivery status for an already-booked order. */
    public function refreshOrderStatus(Order $order): array
    {
        if (blank($order->courier_consignment_id)) {
            return ['ok' => false, 'message' => 'This order has no Steadfast consignment yet.', 'data' => []];
        }

        $result = $this->statusByConsignmentId((string) $order->courier_consignment_id);

        if ($result['ok'] && filled($result['data']['delivery_status'] ?? null)) {
            $order->forceFill(['courier_status' => $result['data']['delivery_status']])->save();
        }

        return $result;
    }

    /**
     * Steadfast signs webhooks with HMAC-SHA256 over the raw body using the
     * webhook token from the merchant portal. An unset token means the shop has
     * not enabled signing yet, so the check is skipped rather than rejecting.
     */
    public function verifyWebhookSignature(string $rawBody, ?string $signature): bool
    {
        $token = $this->setting('steadfast_webhook_token');

        if (blank($token)) {
            return true;
        }

        if (blank($signature)) {
            return false;
        }

        return hash_equals(hash_hmac('sha256', $rawBody, $token), $signature);
    }

    /** Local order status implied by a courier delivery status. */
    public function mapDeliveryStatus(?string $deliveryStatus): ?string
    {
        return match ($deliveryStatus) {
            'delivered', 'partial_delivered' => 'delivered',
            'cancelled', 'return', 'returned' => 'cancelled',
            'in_review', 'pending' => 'processing',
            'hold', 'delivery_attempt', 'in_transit', 'shipped' => 'shipped',
            default => null,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Transport
    |--------------------------------------------------------------------------
    */

    /** @return array{ok: bool, message: string, data: array} */
    private function request(string $method, string $path, array $payload = []): array
    {
        if (! $this->isConfigured()) {
            return ['ok' => false, 'message' => 'Steadfast API credentials are not configured.', 'data' => []];
        }

        try {
            $request = Http::timeout(20)->acceptJson()->withHeaders([
                'Api-Key' => $this->setting('steadfast_api_key'),
                'Secret-Key' => $this->setting('steadfast_secret_key'),
                'Content-Type' => 'application/json',
            ]);

            $url = $this->baseUrl().'/'.ltrim($path, '/');
            $response = $method === 'post' ? $request->post($url, $payload) : $request->get($url);
            $body = $response->json() ?? [];

            // Steadfast answers 200 with its own status field, so check both.
            $status = $body['status'] ?? null;
            $ok = $response->successful() && (! is_numeric($status) || (int) $status === 200);

            if (! $ok) {
                $message = $body['message'] ?? $this->firstError($body) ?? 'Steadfast returned HTTP '.$response->status();
                Log::warning('Steadfast API call failed', ['path' => $path, 'status' => $response->status(), 'body' => $body]);

                return ['ok' => false, 'message' => $message, 'data' => $body];
            }

            return ['ok' => true, 'message' => $body['message'] ?? 'OK', 'data' => $body];
        } catch (\Throwable $e) {
            Log::warning('Steadfast API request threw', ['path' => $path, 'message' => $e->getMessage()]);

            return ['ok' => false, 'message' => $e->getMessage(), 'data' => []];
        }
    }

    private function firstError(array $body): ?string
    {
        $errors = $body['errors'] ?? null;

        if (is_array($errors)) {
            $first = reset($errors);

            return is_array($first) ? (string) reset($first) : (string) $first;
        }

        return null;
    }

    /** Steadfast expects a local 11-digit Bangladeshi number. */
    private function normalizePhone(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if (str_starts_with($digits, '880')) {
            $digits = '0'.substr($digits, 3);
        } elseif (strlen($digits) === 10 && str_starts_with($digits, '1')) {
            $digits = '0'.$digits;
        }

        return $digits;
    }
}
