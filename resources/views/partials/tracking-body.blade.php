@php
    $tracking = app(\App\Services\Tracking\TrackingSettings::class);
    $gtmId = $tracking->get('gtm_id');
@endphp

@if($gtmId)
    {{-- Google Tag Manager (noscript) — must be the first thing inside <body>. --}}
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif

{{-- Custom footer scripts from the admin page, inserted verbatim. --}}
{!! $tracking->get('custom_body_scripts') !!}
