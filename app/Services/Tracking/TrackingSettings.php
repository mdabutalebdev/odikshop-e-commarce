<?php

namespace App\Services\Tracking;

use App\Models\SiteSetting;

/**
 * Typed read access to the tracking rows in site_settings, loaded once per request.
 *
 * Everything the Pixel & Tracking admin page writes is read back through here, so
 * the storefront never has to know that the values live in a key/value table.
 */
class TrackingSettings
{
    /** @var array<string, string|null>|null */
    private ?array $values = null;

    /** Keys owned by the Pixel & Tracking page — the whitelist used when saving. */
    public const KEYS = [
        'meta_pixel_enabled',
        'meta_pixel_id',
        'meta_capi_token',
        'meta_test_event_code',
        'meta_advanced_matching',
        'meta_event_pageview',
        'meta_event_viewcontent',
        'meta_event_addtocart',
        'meta_event_initiatecheckout',
        'meta_event_purchase',
        'gtm_id',
        'ga4_id',
        'tiktok_pixel_id',
        'custom_head_scripts',
        'custom_body_scripts',
    ];

    /** Toggles that should default to on for a shop that has only entered a Pixel ID. */
    private const DEFAULT_ON = [
        'meta_pixel_enabled',
        'meta_advanced_matching',
        'meta_event_pageview',
        'meta_event_viewcontent',
        'meta_event_addtocart',
        'meta_event_initiatecheckout',
        'meta_event_purchase',
    ];

    public function get(string $key, ?string $default = null): ?string
    {
        $this->values ??= SiteSetting::getAll();

        $value = $this->values[$key] ?? null;

        return ($value === null || $value === '') ? $default : $value;
    }

    public function bool(string $key, ?bool $default = null): bool
    {
        $default ??= in_array($key, self::DEFAULT_ON, true);
        $value = $this->get($key);

        return $value === null ? $default : (bool) (int) $value;
    }

    /** @return array<string, string|null> */
    public function all(): array
    {
        $this->values ??= SiteSetting::getAll();

        return $this->values;
    }

    /** True when the browser pixel should be rendered at all. */
    public function metaEnabled(): bool
    {
        return $this->bool('meta_pixel_enabled') && filled($this->get('meta_pixel_id'));
    }

    /** Standard events are individually switchable from the admin page. */
    public function eventEnabled(string $event): bool
    {
        $key = 'meta_event_'.strtolower($event);

        return in_array($key, self::KEYS, true) ? $this->bool($key) : true;
    }

    /** Forget the cached row set — used right after the admin page saves. */
    public function flush(): void
    {
        $this->values = null;
    }
}
