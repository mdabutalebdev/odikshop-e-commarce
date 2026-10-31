<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Services\Tracking\MetaConversionsApi;
use App\Services\Tracking\TrackingSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TrackingController extends Controller
{
    /** Checkbox keys — absent from the POST means "off", so they need explicit handling. */
    private const TOGGLES = [
        'meta_pixel_enabled',
        'meta_advanced_matching',
        'meta_event_pageview',
        'meta_event_viewcontent',
        'meta_event_addtocart',
        'meta_event_initiatecheckout',
        'meta_event_purchase',
    ];

    public function index(TrackingSettings $settings)
    {
        return view('admin.tracking.index', ['settings' => $settings]);
    }

    public function update(Request $request, TrackingSettings $settings)
    {
        $data = $request->validate([
            'meta_pixel_id' => ['nullable', 'string', 'max:50'],
            'meta_capi_token' => ['nullable', 'string', 'max:1000'],
            'meta_test_event_code' => ['nullable', 'string', 'max:50'],
            'gtm_id' => ['nullable', 'string', 'max:50'],
            'ga4_id' => ['nullable', 'string', 'max:50'],
            'tiktok_pixel_id' => ['nullable', 'string', 'max:50'],
            'custom_head_scripts' => ['nullable', 'string', 'max:20000'],
            'custom_body_scripts' => ['nullable', 'string', 'max:20000'],
        ]);

        foreach ($data as $key => $value) {
            SiteSetting::set($key, $value);
        }

        foreach (self::TOGGLES as $toggle) {
            SiteSetting::set($toggle, $request->boolean($toggle) ? '1' : '0');
        }

        $settings->flush();

        return back()->with('success', 'Tracking settings saved.');
    }

    /**
     * Fire a single test event straight at Meta so the shop owner can confirm the
     * Pixel ID and access token work before relying on live traffic. Uses the
     * Test Event Code from the form, which makes it appear in Events Manager →
     * Test Events within seconds.
     */
    public function testCapi(Request $request, MetaConversionsApi $capi, TrackingSettings $settings)
    {
        $request->validate(['test_event_code' => ['nullable', 'string', 'max:50']]);

        if (! $capi->isConfigured()) {
            return response()->json([
                'ok' => false,
                'message' => 'Save a Meta Pixel ID and a CAPI access token first.',
            ]);
        }

        $event = $capi->buildEvent('PageView', (string) Str::uuid(), [], [
            'email' => $request->user()?->email,
        ], $request);

        $result = $capi->send([$event], $request->input('test_event_code') ?: $settings->get('meta_test_event_code'));

        return response()->json([
            'ok' => $result['ok'],
            'message' => $result['ok']
                ? 'Meta accepted the test event. Open Events Manager → Test Events to see it.'
                : ($result['error'] ?: 'Meta rejected the event.'),
            'received' => data_get($result['body'], 'events_received'),
            'trace' => data_get($result['body'], 'fbtrace_id'),
        ]);
    }
}
