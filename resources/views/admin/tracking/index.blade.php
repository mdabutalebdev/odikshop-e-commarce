<x-admin-layout title="Pixel & Tracking">
    @php
        $metaReady = filled($settings->get('meta_pixel_id'));
        $capiReady = filled($settings->get('meta_capi_token'));
        $tokenLength = strlen((string) $settings->get('meta_capi_token'));

        $events = [
            'meta_event_pageview' => ['PageView', 'Fires on every storefront page'],
            'meta_event_viewcontent' => ['ViewContent', 'Fires when a product page is opened'],
            'meta_event_addtocart' => ['AddToCart', 'Fires on Add to Cart / Buy Now'],
            'meta_event_initiatecheckout' => ['InitiateCheckout', 'Fires when the checkout page loads'],
            'meta_event_purchase' => ['Purchase', 'Fires once on order confirmation'],
            'meta_advanced_matching' => ['Advanced Matching', 'Sends hashed phone / email for attribution'],
        ];
    @endphp

    {{-- Inline CSS: the compiled Tailwind bundle is fixed at build time, so a page
         that ships as a Blade file alone cannot introduce new utility classes. --}}
    <style>
        .px-wrap { display: grid; gap: 1.25rem; align-items: start; }
        @media (min-width: 1024px) { .px-wrap { grid-template-columns: 1fr 20rem; } }

        .px-head { display: flex; flex-wrap: wrap; align-items: center; gap: .75rem; margin-bottom: 1.25rem; }
        .px-head h2 { margin: 0; font-size: 1.35rem; font-weight: 800; color: var(--color-ink); }
        .px-head p { margin: .2rem 0 0; font-size: .8125rem; color: var(--color-muted); }
        .px-head-actions { margin-left: auto; display: flex; gap: .5rem; flex-wrap: wrap; }

        .px-chip { display: inline-flex; align-items: center; gap: .35rem; font-size: .7rem; font-weight: 700; padding: .25rem .6rem; border-radius: 9999px; }
        .px-chip-on { background: #e7f7ee; color: #12794a; }
        .px-chip-off { background: #fdf2f2; color: #c02626; }
        .px-dot { width: .45rem; height: .45rem; border-radius: 9999px; background: currentColor; }

        .px-card { background: #fff; border: 1px solid var(--color-line); border-radius: 14px; overflow: hidden; margin-bottom: 1.25rem; }
        .px-card-head { display: flex; align-items: center; gap: .75rem; padding: 1rem 1.25rem; border-bottom: 1px solid var(--color-line); }
        .px-icon { width: 2.4rem; height: 2.4rem; flex: 0 0 auto; border-radius: 10px; display: grid; place-items: center; color: #fff; font-size: 1rem; }
        .px-card-head h3 { margin: 0; font-size: .95rem; font-weight: 800; color: var(--color-ink); }
        .px-card-head p { margin: .1rem 0 0; font-size: .75rem; color: var(--color-muted); }
        .px-card-body { padding: 1.25rem; }

        .px-field { margin-bottom: 1.1rem; }
        .px-field:last-child { margin-bottom: 0; }
        .px-label { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; font-size: .72rem; font-weight: 700; letter-spacing: .03em; text-transform: uppercase; color: var(--color-ink); margin-bottom: .4rem; }
        .px-req { color: var(--color-sale); }
        .px-input, .px-area { width: 100%; border: 1px solid var(--color-line); border-radius: 9px; padding: .65rem .8rem; font-size: .875rem; background: #fff; color: var(--color-ink); font-family: inherit; }
        .px-area { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: .75rem; line-height: 1.6; resize: vertical; }
        .px-input:focus, .px-area:focus { outline: none; border-color: var(--color-brand); box-shadow: 0 0 0 3px rgba(14, 148, 149, .15); }
        .px-hint { margin: .35rem 0 0; font-size: .72rem; color: var(--color-muted); }

        .px-grid2 { display: grid; gap: 1rem; }
        @media (min-width: 640px) { .px-grid2 { grid-template-columns: 1fr 1fr; } }

        .px-switch { position: relative; display: inline-flex; align-items: center; cursor: pointer; }
        .px-switch input { position: absolute; opacity: 0; width: 0; height: 0; }
        .px-track { width: 2.75rem; height: 1.55rem; border-radius: 9999px; background: #cbd5e1; transition: background .2s ease; position: relative; flex: 0 0 auto; }
        .px-track::after { content: ''; position: absolute; top: .18rem; left: .18rem; width: 1.19rem; height: 1.19rem; border-radius: 9999px; background: #fff; transition: transform .2s ease; box-shadow: 0 1px 3px rgba(0,0,0,.25); }
        .px-switch input:checked + .px-track { background: var(--color-brand); }
        .px-switch input:checked + .px-track::after { transform: translateX(1.2rem); }

        .px-check { display: flex; align-items: flex-start; gap: .6rem; border: 1px solid var(--color-line); border-radius: 10px; padding: .7rem .8rem; cursor: pointer; transition: border-color .15s ease, background .15s ease; }
        .px-check:hover { border-color: var(--color-brand); }
        .px-check input { margin-top: .15rem; width: 1rem; height: 1rem; accent-color: var(--color-brand); flex: 0 0 auto; }
        .px-check strong { display: block; font-size: .8125rem; color: var(--color-ink); }
        .px-check span { display: block; font-size: .7rem; color: var(--color-muted); margin-top: .1rem; }

        .px-side-card { background: #fff; border: 1px solid var(--color-line); border-radius: 14px; padding: 1.1rem 1.15rem; margin-bottom: 1.25rem; }
        .px-side-card h4 { margin: 0 0 .35rem; font-size: .875rem; font-weight: 800; color: var(--color-ink); display: flex; align-items: center; gap: .4rem; }
        .px-side-card p { margin: 0 0 .85rem; font-size: .75rem; color: var(--color-muted); line-height: 1.55; }

        .px-btn { display: inline-flex; align-items: center; justify-content: center; gap: .45rem; height: 2.6rem; padding: 0 1.1rem; border: 0; border-radius: 9px; font-size: .8125rem; font-weight: 700; cursor: pointer; text-decoration: none; transition: filter .15s ease; }
        .px-btn:hover { filter: brightness(.94); }
        .px-btn-brand { background: var(--color-brand); color: #fff; }
        .px-btn-accent { background: var(--color-accent); color: #fff; }
        .px-btn-blue { background: #1877F2; color: #fff; width: 100%; }
        .px-btn-ghost { background: #fff; color: var(--color-ink); border: 1px solid var(--color-line); }

        .px-result { margin-top: .75rem; font-size: .75rem; border-radius: 9px; padding: .65rem .8rem; line-height: 1.55; display: none; }
        .px-result-ok { background: #e7f7ee; color: #12794a; }
        .px-result-bad { background: #fdf2f2; color: #c02626; }

        .px-list { list-style: none; margin: 0; padding: 0; }
        .px-list li { display: flex; gap: .5rem; align-items: flex-start; font-size: .75rem; color: var(--color-ink); padding: .3rem 0; line-height: 1.5; }
        .px-list i { color: #12794a; margin-top: .15rem; }

        .px-bar { display: flex; align-items: center; gap: .75rem; flex-wrap: wrap; background: #fff; border: 1px solid var(--color-line); border-radius: 14px; padding: .9rem 1.15rem; }
        .px-bar span { font-size: .78rem; color: var(--color-muted); }
    </style>

    <form method="POST" action="{{ route('admin.tracking.update') }}" id="tracking-form">
        @csrf @method('PUT')

        <div class="px-head">
            <div>
                <h2>Pixel &amp; Tracking Setup
                    <span class="px-chip {{ $metaReady ? 'px-chip-on' : 'px-chip-off' }}" style="vertical-align: middle; margin-left: .4rem;">
                        <span class="px-dot"></span>{{ $metaReady ? 'Active' : 'Not configured' }}
                    </span>
                </h2>
                <p>Meta (Facebook) Pixel, server-side Conversions API, Google Tag Manager, GA4 and TikTok Pixel.</p>
            </div>
            <div class="px-head-actions">
                <a href="https://business.facebook.com/events_manager2" target="_blank" rel="noopener" class="px-btn px-btn-ghost">
                    <i class="fa-brands fa-facebook" style="color:#1877F2"></i>Meta Events Manager
                    <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:.65rem"></i>
                </a>
                <button type="submit" class="px-btn px-btn-accent"><i class="fa-solid fa-floppy-disk"></i>Save All Settings</button>
            </div>
        </div>

        <div class="px-wrap">
            <div>
                {{-- ===== Meta Pixel & CAPI ===== --}}
                <div class="px-card">
                    <div class="px-card-head">
                        <span class="px-icon" style="background:#1877F2"><i class="fa-brands fa-facebook-f"></i></span>
                        <div>
                            <h3>Meta (Facebook) Pixel &amp; CAPI</h3>
                            <p>Browser &amp; server-side tracking for Meta Ads</p>
                        </div>
                        <label class="px-switch" style="margin-left:auto" title="Enable Meta tracking">
                            <input type="checkbox" name="meta_pixel_enabled" value="1" @checked($settings->bool('meta_pixel_enabled'))>
                            <span class="px-track"></span>
                        </label>
                    </div>
                    <div class="px-card-body">
                        <div class="px-field">
                            <label class="px-label" for="meta_pixel_id">Meta Pixel ID <span class="px-req">*</span></label>
                            <input type="text" id="meta_pixel_id" name="meta_pixel_id" class="px-input" inputmode="numeric"
                                   value="{{ old('meta_pixel_id', $settings->get('meta_pixel_id')) }}" placeholder="1146156437996318">
                            <p class="px-hint">Events Manager → Data Sources → Settings.</p>
                        </div>

                        <div class="px-field">
                            <label class="px-label" for="meta_capi_token">
                                Conversions API (CAPI) Access Token
                                @if($capiReady)
                                    <span class="px-chip px-chip-on"><i class="fa-solid fa-circle-check"></i>Token saved ({{ $tokenLength }} chars)</span>
                                @endif
                            </label>
                            <textarea id="meta_capi_token" name="meta_capi_token" class="px-area" rows="3"
                                      placeholder="EAAbMPOyWIjUBS0iBQHgA6ZAZBcbddx7Aw...">{{ old('meta_capi_token', $settings->get('meta_capi_token')) }}</textarea>
                            <p class="px-hint">Required for server-side tracking — this is what keeps events flowing past iOS 14.5+ and ad blockers. Generate it in Events Manager → Settings → Conversions API → Generate access token.</p>
                        </div>

                        <div class="px-field">
                            <label class="px-label" for="meta_test_event_code">Meta Test Event Code <span style="text-transform:none;font-weight:500;color:var(--color-muted)">(optional)</span></label>
                            <input type="text" id="meta_test_event_code" name="meta_test_event_code" class="px-input"
                                   value="{{ old('meta_test_event_code', $settings->get('meta_test_event_code')) }}" placeholder="TEST27386">
                            <p class="px-hint">While this is filled in, events are tagged as test traffic in Events Manager → Test Events. <strong>Clear it once you go live</strong>, or real conversions will not be counted.</p>
                        </div>

                        <div class="px-field">
                            <div class="px-label">Standard E-commerce Events</div>
                            <div class="px-grid2">
                                @foreach($events as $key => [$label, $note])
                                    <label class="px-check">
                                        <input type="checkbox" name="{{ $key }}" value="1" @checked($settings->bool($key))>
                                        <span>
                                            <strong>{{ $label }}</strong>
                                            <span>{{ $note }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== Google & TikTok ===== --}}
                <div class="px-card">
                    <div class="px-card-head">
                        <span class="px-icon" style="background:#F9AB00"><i class="fa-brands fa-google"></i></span>
                        <div>
                            <h3>Google &amp; TikTok Analytics</h3>
                            <p>Google Tag Manager, Google Analytics 4 &amp; TikTok Pixel</p>
                        </div>
                    </div>
                    <div class="px-card-body">
                        <div class="px-grid2">
                            <div class="px-field">
                                <label class="px-label" for="gtm_id">Google Tag Manager (GTM) ID</label>
                                <input type="text" id="gtm_id" name="gtm_id" class="px-input"
                                       value="{{ old('gtm_id', $settings->get('gtm_id')) }}" placeholder="GTM-XXXXXXX">
                            </div>
                            <div class="px-field">
                                <label class="px-label" for="ga4_id">Google Analytics 4 (GA4) ID</label>
                                <input type="text" id="ga4_id" name="ga4_id" class="px-input"
                                       value="{{ old('ga4_id', $settings->get('ga4_id')) }}" placeholder="G-XXXXXXXXXX">
                            </div>
                        </div>
                        <div class="px-field">
                            <label class="px-label" for="tiktok_pixel_id">TikTok Pixel ID</label>
                            <input type="text" id="tiktok_pixel_id" name="tiktok_pixel_id" class="px-input"
                                   value="{{ old('tiktok_pixel_id', $settings->get('tiktok_pixel_id')) }}" placeholder="C1234567890ABC">
                        </div>
                    </div>
                </div>

                {{-- ===== Custom scripts ===== --}}
                <div class="px-card">
                    <div class="px-card-head">
                        <span class="px-icon" style="background:#7c3aed"><i class="fa-solid fa-code"></i></span>
                        <div>
                            <h3>Custom Tracking Scripts</h3>
                            <p>Verification tags, live chat widgets or any other snippet</p>
                        </div>
                    </div>
                    <div class="px-card-body">
                        <div class="px-field">
                            <label class="px-label" for="custom_head_scripts">Custom &lt;head&gt; scripts</label>
                            <textarea id="custom_head_scripts" name="custom_head_scripts" class="px-area" rows="5"
                                      placeholder="&lt;meta name=&quot;facebook-domain-verification&quot; content=&quot;...&quot;&gt;">{{ old('custom_head_scripts', $settings->get('custom_head_scripts')) }}</textarea>
                            <p class="px-hint">Injected right before <code>&lt;/head&gt;</code> on every storefront page.</p>
                        </div>
                        <div class="px-field">
                            <label class="px-label" for="custom_body_scripts">Custom &lt;body&gt; scripts (footer)</label>
                            <textarea id="custom_body_scripts" name="custom_body_scripts" class="px-area" rows="4"
                                      placeholder="&lt;script&gt;/* live chat widget */&lt;/script&gt;">{{ old('custom_body_scripts', $settings->get('custom_body_scripts')) }}</textarea>
                            <p class="px-hint">Injected at the top of <code>&lt;body&gt;</code> on every storefront page. Pasted markup runs as-is — only add code you trust.</p>
                        </div>
                    </div>
                </div>

                <div class="px-bar">
                    <span>Changes take effect immediately across all storefront pages.</span>
                    <button type="submit" class="px-btn px-btn-accent" style="margin-left:auto"><i class="fa-solid fa-floppy-disk"></i>Save All Settings</button>
                </div>
            </div>

            {{-- ===== Right column ===== --}}
            <div>
                <div class="px-side-card">
                    <h4><i class="fa-solid fa-bolt" style="color:var(--color-accent)"></i>Test CAPI Server Connection</h4>
                    <p>Sends a test event straight from this server to Meta, so you can confirm the Pixel ID and access token work without spending on ads.</p>

                    <div class="px-field">
                        <label class="px-label" for="test_event_code">Test Event Code</label>
                        <input type="text" id="test_event_code" class="px-input"
                               value="{{ $settings->get('meta_test_event_code') }}" placeholder="TEST27386">
                        <p class="px-hint">Events Manager → Test Events.</p>
                    </div>

                    <button type="button" class="px-btn px-btn-blue" id="capi-test-btn">
                        <i class="fa-solid fa-paper-plane"></i>Send Test CAPI Event
                    </button>
                    <div class="px-result" id="capi-result"></div>
                </div>

                <div class="px-side-card">
                    <h4><i class="fa-solid fa-list-check" style="color:#12794a"></i>Setup Checklist</h4>
                    <ul class="px-list">
                        <li><i class="fa-solid {{ $metaReady ? 'fa-circle-check' : 'fa-circle' }}" @style(['color:#cbd5e1' => ! $metaReady])></i>Meta Pixel ID entered and enabled</li>
                        <li><i class="fa-solid {{ $capiReady ? 'fa-circle-check' : 'fa-circle' }}" @style(['color:#cbd5e1' => ! $capiReady])></i>CAPI access token generated in Events Manager</li>
                        <li><i class="fa-solid fa-circle-check"></i>Browser and server events share one event ID, so Meta de-duplicates them</li>
                        <li><i class="fa-solid fa-circle-check"></i>Phone and email are SHA-256 hashed before they leave the server</li>
                        <li><i class="fa-solid fa-circle-check"></i>Server events are sent after the page is delivered — no added page latency</li>
                    </ul>
                </div>

                <div class="px-side-card">
                    <h4><i class="fa-solid fa-circle-info" style="color:#1877F2"></i>Verify it is working</h4>
                    <ul class="px-list">
                        <li><i class="fa-solid fa-angle-right" style="color:var(--color-muted)"></i>Install the <strong>Meta Pixel Helper</strong> Chrome extension and open the shop</li>
                        <li><i class="fa-solid fa-angle-right" style="color:var(--color-muted)"></i>Events Manager → Test Events shows both <em>Browser</em> and <em>Server</em> rows</li>
                        <li><i class="fa-solid fa-angle-right" style="color:var(--color-muted)"></i>A de-duplicated pair is correct — it counts as one event</li>
                    </ul>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        (function () {
            const btn = document.getElementById('capi-test-btn');
            const box = document.getElementById('capi-result');
            if (!btn) return;

            btn.addEventListener('click', async function () {
                const original = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>Sending…';
                box.style.display = 'none';

                try {
                    const response = await fetch(@json(route('admin.tracking.test_capi')), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ test_event_code: document.getElementById('test_event_code').value }),
                    });
                    const data = await response.json();

                    box.className = 'px-result ' + (data.ok ? 'px-result-ok' : 'px-result-bad');
                    box.innerHTML = '<strong>' + (data.ok ? 'Success' : 'Failed') + '</strong><br>' + data.message
                        + (data.received ? '<br>Events received: ' + data.received : '')
                        + (data.trace ? '<br>Trace: ' + data.trace : '');
                } catch (error) {
                    box.className = 'px-result px-result-bad';
                    box.textContent = 'Request failed: ' + error.message;
                }

                box.style.display = 'block';
                btn.disabled = false;
                btn.innerHTML = original;
            });
        })();
    </script>
    @endpush
</x-admin-layout>
