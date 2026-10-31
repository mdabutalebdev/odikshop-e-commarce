<?php

namespace App\Services\Tracking;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Meta Conversions API (server-side tracking).
 *
 * Browser pixel events are blocked by iOS 14.5+ ATT, ad blockers and ITP, so every
 * event is also sent straight from this server. Both copies carry the same event_id
 * and Meta de-duplicates them, which is why the browser snippet and this class must
 * always be given the identical id.
 *
 * @see https://developers.facebook.com/docs/marketing-api/conversions-api
 */
class MetaConversionsApi
{
    /** Pinned so a Graph API default version bump cannot silently change behaviour. */
    private const GRAPH_VERSION = 'v21.0';

    public function __construct(private TrackingSettings $settings)
    {
    }

    public function isConfigured(): bool
    {
        return filled($this->settings->get('meta_pixel_id'))
            && filled($this->settings->get('meta_capi_token'));
    }

    /**
     * Send one or more events. Returns Meta's decoded response so the admin
     * "Test Connection" button can show the real result.
     *
     * @param  array<int, array<string, mixed>>  $events
     * @return array{ok: bool, status: int|null, body: array, error: string|null}
     */
    public function send(array $events, ?string $testEventCode = null): array
    {
        if (! $this->isConfigured() || empty($events)) {
            return ['ok' => false, 'status' => null, 'body' => [], 'error' => 'Meta Pixel ID or CAPI access token is missing.'];
        }

        $pixelId = $this->settings->get('meta_pixel_id');
        $payload = ['data' => $events];

        $testCode = $testEventCode ?: $this->settings->get('meta_test_event_code');
        if (filled($testCode)) {
            $payload['test_event_code'] = $testCode;
        }

        try {
            $response = Http::timeout(8)
                ->withToken($this->settings->get('meta_capi_token'))
                ->post(sprintf('https://graph.facebook.com/%s/%s/events', self::GRAPH_VERSION, $pixelId), $payload);

            $body = $response->json() ?? [];

            if (! $response->successful()) {
                $message = data_get($body, 'error.message', 'HTTP '.$response->status());
                Log::warning('Meta CAPI rejected events', ['status' => $response->status(), 'body' => $body]);

                return ['ok' => false, 'status' => $response->status(), 'body' => $body, 'error' => $message];
            }

            return ['ok' => true, 'status' => $response->status(), 'body' => $body, 'error' => null];
        } catch (\Throwable $e) {
            // Tracking must never break a page or an order.
            Log::warning('Meta CAPI request failed', ['message' => $e->getMessage()]);

            return ['ok' => false, 'status' => null, 'body' => [], 'error' => $e->getMessage()];
        }
    }

    /**
     * Build one event in the shape Meta expects.
     *
     * @param  array<string, mixed>  $customData  currency / value / contents / …
     * @param  array<string, mixed>  $user        raw (un-hashed) customer details
     */
    public function buildEvent(string $name, string $eventId, array $customData, array $user, Request $request): array
    {
        $event = [
            'event_name' => $name,
            'event_time' => time(),
            'event_id' => $eventId,
            'event_source_url' => $request->fullUrl(),
            'action_source' => 'website',
            'user_data' => $this->userData($user, $request),
        ];

        if (! empty($customData)) {
            $event['custom_data'] = $customData;
        }

        return $event;
    }

    /**
     * Hash the identifiers Meta expects and attach the browser cookies that make
     * attribution work. Everything personal is SHA-256'd before it leaves here.
     *
     * @param  array<string, mixed>  $user
     */
    private function userData(array $user, Request $request): array
    {
        $data = array_filter([
            'client_ip_address' => $request->ip(),
            'client_user_agent' => $request->userAgent(),
            'fbp' => $request->cookie('_fbp'),
            'fbc' => $this->fbc($request),
        ]);

        // Advanced matching: only sent when the shop owner has it switched on.
        if (! $this->settings->bool('meta_advanced_matching', true)) {
            return $data;
        }

        $map = [
            'em' => $this->normalizeEmail($user['email'] ?? null),
            'ph' => $this->normalizePhone($user['phone'] ?? null),
            'fn' => $this->normalizeName($user['first_name'] ?? null),
            'ln' => $this->normalizeName($user['last_name'] ?? null),
            'ct' => $this->normalizeName($user['city'] ?? null),
            'country' => $user['country'] ?? 'bd',
            'external_id' => $user['external_id'] ?? null,
        ];

        foreach ($map as $key => $value) {
            if (filled($value)) {
                $data[$key] = [hash('sha256', (string) $value)];
            }
        }

        return $data;
    }

    /**
     * Meta's click id. Prefer the _fbc cookie; fall back to building it from the
     * ?fbclid= on the landing URL, which is what the pixel itself would have done.
     */
    private function fbc(Request $request): ?string
    {
        if ($cookie = $request->cookie('_fbc')) {
            return $cookie;
        }

        $fbclid = $request->query('fbclid');

        return $fbclid ? 'fb.1.'.(time() * 1000).'.'.$fbclid : null;
    }

    private function normalizeEmail(?string $value): ?string
    {
        $value = trim(mb_strtolower((string) $value));

        return filter_var($value, FILTER_VALIDATE_EMAIL) ? $value : null;
    }

    /**
     * Meta wants digits only, including the country code. Bangladeshi numbers are
     * normally typed as 01XXXXXXXXX, so prepend 88 when that local form is given.
     */
    private function normalizePhone(?string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $value);

        if (blank($digits)) {
            return null;
        }

        if (str_starts_with($digits, '01') && strlen($digits) === 11) {
            $digits = '88'.$digits;
        }

        return strlen($digits) >= 10 ? $digits : null;
    }

    private function normalizeName(?string $value): ?string
    {
        $value = preg_replace('/\s+/', ' ', trim(mb_strtolower((string) $value)));

        return $value !== '' ? $value : null;
    }
}
