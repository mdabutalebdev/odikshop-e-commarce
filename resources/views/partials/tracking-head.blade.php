@php
    /**
     * All browser-side tracking tags. Rendered inside <head> on every storefront page.
     *
     * PageView is recorded here rather than in a controller so it covers every route,
     * and the same call registers the server-side copy. Each event carries an eventID
     * that matches the Conversions API payload, which is what tells Meta the two are
     * one event and not two.
     */
    $pixel = app(\App\Services\Tracking\Pixel::class);
    $tracking = $pixel->settings();

    $pixel->track('PageView');

    $metaEnabled = $tracking->metaEnabled();
    $metaPixelId = $tracking->get('meta_pixel_id');
    $browserEvents = $metaEnabled ? $pixel->browserEvents() : [];

    $gtmId = $tracking->get('gtm_id');
    $ga4Id = $tracking->get('ga4_id');
    $tiktokId = $tracking->get('tiktok_pixel_id');
@endphp

@if($metaEnabled)
    {{-- Meta Pixel base code --}}
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window,document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');

        fbq('init', @json($metaPixelId));

        // Fires the browser half of an event. The matching server event is sent from
        // PHP with the same eventID so Meta de-duplicates the pair.
        window.odhikTrack = function (descriptor) {
            if (!descriptor || !window.fbq) return;
            fbq('track', descriptor.name, descriptor.params || {}, { eventID: descriptor.id });
        };

        @foreach($browserEvents as $event)
            window.odhikTrack(@json($event));
        @endforeach

        // Add-to-cart is an AJAX call, so the event descriptor rides back in the JSON
        // response. Wrapping fetch keeps this out of the compiled bundle.
        (function () {
            const original = window.fetch;
            if (!original) return;
            window.fetch = function (...args) {
                const result = original.apply(this, args);
                try {
                    const target = typeof args[0] === 'string' ? args[0] : (args[0] && args[0].url);
                    if (target && target.indexOf('/cart/add/') !== -1) {
                        result.then(response => {
                            response.clone().json()
                                .then(data => { if (data && data.track) window.odhikTrack(data.track); })
                                .catch(() => {});
                        }).catch(() => {});
                    }
                } catch (e) { /* tracking must never break the cart */ }
                return result;
            };
        })();
    </script>
    <noscript><img height="1" width="1" style="display:none" alt=""
        src="https://www.facebook.com/tr?id={{ $metaPixelId }}&ev=PageView&noscript=1"></noscript>
@endif

@if($gtmId)
    {{-- Google Tag Manager --}}
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer',@json($gtmId));</script>
@endif

@if($ga4Id)
    {{-- Google Analytics 4 --}}
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Id }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', @json($ga4Id));
    </script>
@endif

@if($tiktokId)
    {{-- TikTok Pixel --}}
    <script>
        !function (w, d, t) {
          w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
          ttq.load(@json($tiktokId));
          ttq.page();
        }(window, document, 'ttq');
    </script>
@endif

{{-- Custom <head> scripts from the admin page, inserted verbatim. --}}
{!! $tracking->get('custom_head_scripts') !!}
