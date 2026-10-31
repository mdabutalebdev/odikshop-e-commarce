<?php

namespace App\Services\Tracking;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Str;

/**
 * One call site for every tracking event.
 *
 * Each event gets a single id that is handed to both the browser pixel and the
 * Conversions API, which is what lets Meta de-duplicate the pair. Server-side
 * delivery is deferred to the terminating callback so a slow Graph API response
 * never holds up the page or an order confirmation.
 */
class Pixel
{
    /** Session key holding events that must render on the *next* request. */
    private const FLASH_KEY = '_pixel_events';

    /** @var array<int, array<string, mixed>> browser events for the response being built */
    private array $events = [];

    /** @var array<int, array<string, mixed>> server events waiting for terminate */
    private array $pending = [];

    private bool $terminatingBound = false;

    public function __construct(
        private TrackingSettings $settings,
        private MetaConversionsApi $capi,
    ) {
    }

    public function settings(): TrackingSettings
    {
        return $this->settings;
    }

    /**
     * Record an event. Returns the descriptor the browser needs, so a controller
     * can also hand it back in a JSON response or flash it across a redirect.
     *
     * @param  array<string, mixed>  $customData
     * @param  array<string, mixed>  $user
     * @return array{name: string, params: array, id: string}|null
     */
    public function track(string $event, array $customData = [], array $user = []): ?array
    {
        if (! $this->settings->metaEnabled() || ! $this->settings->eventEnabled($event)) {
            return null;
        }

        $descriptor = [
            'name' => $event,
            'params' => array_filter($customData, fn ($value) => $value !== null),
            'id' => (string) Str::uuid(),
        ];

        $this->events[] = $descriptor;
        $this->queueServerEvent($event, $descriptor['id'], $descriptor['params'], $user);

        return $descriptor;
    }

    /** Record an event that should fire in the browser on the next page load. */
    public function trackAfterRedirect(string $event, array $customData = [], array $user = []): void
    {
        if ($descriptor = $this->track($event, $customData, $user)) {
            $existing = session()->get(self::FLASH_KEY, []);
            session()->flash(self::FLASH_KEY, array_merge($existing, [$descriptor]));
        }
    }

    /**
     * Events the Blade partial should emit: whatever this request recorded, plus
     * anything flashed by the request before it (e.g. a non-AJAX add-to-cart).
     *
     * @return array<int, array<string, mixed>>
     */
    public function browserEvents(): array
    {
        return array_merge(session()->get(self::FLASH_KEY, []), $this->events);
    }

    /** Hand the server-side event to Meta once the response has been flushed. */
    private function queueServerEvent(string $event, string $eventId, array $customData, array $user): void
    {
        if (! $this->capi->isConfigured()) {
            return;
        }

        $this->pending[] = $this->capi->buildEvent($event, $eventId, $customData, $this->resolveUser($user), request());

        if ($this->terminatingBound) {
            return;
        }

        $this->terminatingBound = true;

        // Runs after the response reaches the browser, so tracking adds no latency.
        app()->terminating(function () {
            $events = $this->pending;
            $this->pending = [];

            if (! empty($events)) {
                $this->capi->send($events);
            }
        });
    }

    /** Fill in whatever we already know about the visitor. */
    private function resolveUser(array $user): array
    {
        $customer = auth()->user();

        if (! $customer) {
            return $user;
        }

        $user += array_filter([
            'email' => $customer->email ?? null,
            'phone' => $customer->phone ?? null,
            'external_id' => (string) $customer->id,
        ]);

        if (! isset($user['first_name']) && filled($customer->name)) {
            $user += $this->splitName($customer->name);
        }

        return $user;
    }

    /** @return array{first_name: string|null, last_name: string|null} */
    private function splitName(?string $full): array
    {
        $parts = preg_split('/\s+/', trim((string) $full), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return [
            'first_name' => $parts[0] ?? null,
            'last_name' => count($parts) > 1 ? end($parts) : null,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Event shapes
    |--------------------------------------------------------------------------
    | Meta matches catalogue items on content_ids, so the product id used here has
    | to be the same one any Facebook catalogue feed uses.
    */

    public function viewContent(Product $product): ?array
    {
        return $this->track('ViewContent', [
            'currency' => 'BDT',
            'value' => (float) $product->price,
            'content_type' => 'product',
            'content_ids' => [(string) $product->id],
            'content_name' => $product->name,
            'content_category' => $product->category->name ?? null,
            'contents' => [[
                'id' => (string) $product->id,
                'quantity' => 1,
                'item_price' => (float) $product->price,
            ]],
        ]);
    }

    public function addToCart(Product $product, int $quantity): ?array
    {
        return $this->track('AddToCart', $this->addToCartData($product, $quantity));
    }

    /**
     * The AddToCart payload on its own, for the non-AJAX path where the event has
     * to be flashed across the redirect back to the product page.
     *
     * @return array<string, mixed>
     */
    public function addToCartData(Product $product, int $quantity): array
    {
        return [
            'currency' => 'BDT',
            'value' => (float) $product->price * $quantity,
            'content_type' => 'product',
            'content_ids' => [(string) $product->id],
            'content_name' => $product->name,
            'contents' => [[
                'id' => (string) $product->id,
                'quantity' => $quantity,
                'item_price' => (float) $product->price,
            ]],
        ];
    }

    /** @param  iterable<object>  $items  cart items exposing product and quantity */
    public function initiateCheckout(iterable $items, float $subtotal): ?array
    {
        $contents = [];
        $ids = [];
        $count = 0;

        foreach ($items as $item) {
            $ids[] = (string) $item->product->id;
            $contents[] = [
                'id' => (string) $item->product->id,
                'quantity' => (int) $item->quantity,
                'item_price' => (float) $item->product->price,
            ];
            $count += (int) $item->quantity;
        }

        return $this->track('InitiateCheckout', [
            'currency' => 'BDT',
            'value' => $subtotal,
            'content_type' => 'product',
            'content_ids' => $ids,
            'contents' => $contents,
            'num_items' => $count,
        ]);
    }

    public function purchase(Order $order): ?array
    {
        $contents = [];
        $ids = [];
        $count = 0;

        foreach ($order->items as $item) {
            $ids[] = (string) $item->product_id;
            $contents[] = [
                'id' => (string) $item->product_id,
                'quantity' => (int) $item->quantity,
                'item_price' => (float) $item->price,
            ];
            $count += (int) $item->quantity;
        }

        return $this->track('Purchase', [
            'currency' => 'BDT',
            'value' => (float) $order->total,
            'content_type' => 'product',
            'content_ids' => $ids,
            'contents' => $contents,
            'num_items' => $count,
            'order_id' => $order->order_number,
        ], array_filter($this->splitName($order->name) + [
            'phone' => $order->phone,
            'city' => $order->district,
            'external_id' => $order->order_number,
        ]));
    }
}
