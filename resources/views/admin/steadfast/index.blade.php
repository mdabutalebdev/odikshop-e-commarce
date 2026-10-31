<x-admin-layout title="Steadfast Courier">
    @php
        $configured = $steadfast->isConfigured();
        $enabled = (bool) (int) ($settings['steadfast_enabled'] ?? 0);
        $autoBook = (bool) (int) ($settings['steadfast_auto_book'] ?? 0);
        $baseUrl = $settings['steadfast_base_url'] ?? \App\Services\SteadfastService::DEFAULT_BASE_URL;
        $callbackUrl = route('steadfast.webhook');
    @endphp

    {{-- Inline CSS: the compiled Tailwind bundle is fixed at build time, so a page
         that ships as a Blade file alone cannot introduce new utility classes. --}}
    <style>
        .sf-top { display: grid; gap: 1rem; margin-bottom: 1.25rem; }
        @media (min-width: 768px) { .sf-top { grid-template-columns: repeat(3, 1fr); } }

        .sf-stat { background: #fff; border: 1px solid var(--color-line); border-radius: 14px; padding: 1rem 1.15rem; display: flex; align-items: center; gap: .85rem; }
        .sf-stat-ico { width: 2.6rem; height: 2.6rem; flex: 0 0 auto; border-radius: 10px; display: grid; place-items: center; font-size: 1rem; }
        .sf-stat-key { font-size: .65rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--color-muted); }
        .sf-stat-val { font-size: 1rem; font-weight: 800; color: var(--color-ink); line-height: 1.3; }
        .sf-stat-sub { font-size: .7rem; color: var(--color-muted); }

        .sf-wrap { display: grid; gap: 1.25rem; align-items: start; }
        @media (min-width: 1024px) { .sf-wrap { grid-template-columns: 1fr 21rem; } }

        .sf-card { background: #fff; border: 1px solid var(--color-line); border-radius: 14px; overflow: hidden; margin-bottom: 1.25rem; }
        .sf-card-head { display: flex; align-items: center; gap: .75rem; padding: 1rem 1.25rem; border-bottom: 1px solid var(--color-line); }
        .sf-icon { width: 2.4rem; height: 2.4rem; flex: 0 0 auto; border-radius: 10px; display: grid; place-items: center; background: var(--color-brand-light); color: var(--color-brand); font-size: 1rem; }
        .sf-card-head h3 { margin: 0; font-size: .95rem; font-weight: 800; color: var(--color-ink); }
        .sf-card-head p { margin: .1rem 0 0; font-size: .75rem; color: var(--color-muted); }
        .sf-card-body { padding: 1.25rem; }

        .sf-field { margin-bottom: 1.1rem; }
        .sf-field:last-child { margin-bottom: 0; }
        .sf-label { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; font-size: .72rem; font-weight: 700; letter-spacing: .03em; text-transform: uppercase; color: var(--color-ink); margin-bottom: .4rem; }
        .sf-req { color: var(--color-sale); }
        .sf-muted-label { text-transform: none; font-weight: 500; color: var(--color-muted); letter-spacing: 0; }
        .sf-inputwrap { position: relative; display: flex; align-items: center; }
        .sf-inputwrap > i { position: absolute; left: .75rem; color: var(--color-muted); font-size: .8rem; pointer-events: none; }
        .sf-input { width: 100%; border: 1px solid var(--color-line); border-radius: 9px; padding: .65rem .8rem .65rem 2.2rem; font-size: .8125rem; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; background: #fff; color: var(--color-ink); }
        .sf-input:focus { outline: none; border-color: var(--color-brand); box-shadow: 0 0 0 3px rgba(14, 148, 149, .15); }
        .sf-inline-btn { position: absolute; right: .35rem; height: 1.9rem; padding: 0 .7rem; border: 1px solid var(--color-line); background: #fff; border-radius: 7px; font-size: .7rem; font-weight: 700; color: var(--color-ink); cursor: pointer; display: inline-flex; align-items: center; gap: .3rem; }
        .sf-inline-btn:hover { border-color: var(--color-brand); color: var(--color-brand); }
        .sf-hint { margin: .35rem 0 0; font-size: .72rem; color: var(--color-muted); }

        .sf-switchrow { display: flex; align-items: flex-start; gap: .7rem; padding: .85rem 0; border-top: 1px solid var(--color-line); }
        .sf-switchrow strong { display: block; font-size: .8125rem; color: var(--color-ink); }
        .sf-switchrow span { display: block; font-size: .72rem; color: var(--color-muted); margin-top: .1rem; }
        .sf-switch { position: relative; display: inline-flex; align-items: center; cursor: pointer; flex: 0 0 auto; }
        .sf-switch input { position: absolute; opacity: 0; width: 0; height: 0; }
        .sf-track { width: 2.75rem; height: 1.55rem; border-radius: 9999px; background: #cbd5e1; transition: background .2s ease; position: relative; }
        .sf-track::after { content: ''; position: absolute; top: .18rem; left: .18rem; width: 1.19rem; height: 1.19rem; border-radius: 9999px; background: #fff; transition: transform .2s ease; box-shadow: 0 1px 3px rgba(0,0,0,.25); }
        .sf-switch input:checked + .sf-track { background: var(--color-brand); }
        .sf-switch input:checked + .sf-track::after { transform: translateX(1.2rem); }

        .sf-side { background: #fff; border: 1px solid var(--color-line); border-radius: 14px; padding: 1.1rem 1.15rem; margin-bottom: 1.25rem; }
        .sf-side h4 { margin: 0 0 .35rem; font-size: .875rem; font-weight: 800; color: var(--color-ink); display: flex; align-items: center; gap: .45rem; }
        .sf-side p { margin: 0 0 .85rem; font-size: .75rem; color: var(--color-muted); line-height: 1.55; }

        .sf-url { background: var(--color-canvas); border: 1px solid var(--color-line); border-radius: 9px; padding: .6rem .7rem; display: flex; align-items: center; gap: .5rem; }
        .sf-url code { flex: 1; min-width: 0; font-size: .7rem; color: var(--color-ink); overflow-wrap: anywhere; }

        .sf-btn { display: inline-flex; align-items: center; justify-content: center; gap: .45rem; height: 2.6rem; padding: 0 1.1rem; border: 0; border-radius: 9px; font-size: .8125rem; font-weight: 700; cursor: pointer; text-decoration: none; transition: filter .15s ease; }
        .sf-btn:hover { filter: brightness(.94); }
        .sf-btn-brand { background: var(--color-brand); color: #fff; }
        .sf-btn-dark { background: #0f172a; color: #fff; width: 100%; }
        .sf-btn-ghost { background: #fff; color: var(--color-ink); border: 1px solid var(--color-line); }
        .sf-btn-sm { height: 2.2rem; padding: 0 .7rem; font-size: .72rem; }

        .sf-duo { display: grid; grid-template-columns: 1fr 1fr; gap: .5rem; }
        .sf-result { margin-top: .75rem; font-size: .75rem; border-radius: 9px; padding: .65rem .8rem; line-height: 1.55; display: none; }
        .sf-result-ok { background: #e7f7ee; color: #12794a; }
        .sf-result-bad { background: #fdf2f2; color: #c02626; }

        .sf-chip { display: inline-flex; align-items: center; gap: .35rem; font-size: .7rem; font-weight: 700; padding: .25rem .6rem; border-radius: 9999px; }
        .sf-chip-on { background: #e7f7ee; color: #12794a; }
        .sf-chip-off { background: #fdf2f2; color: #c02626; }
        .sf-dot { width: .45rem; height: .45rem; border-radius: 9999px; background: currentColor; }

        .sf-head { display: flex; flex-wrap: wrap; align-items: center; gap: .75rem; margin-bottom: 1.25rem; }
        .sf-head h2 { margin: 0; font-size: 1.35rem; font-weight: 800; color: var(--color-ink); }
        .sf-head p { margin: .2rem 0 0; font-size: .8125rem; color: var(--color-muted); }

        .sf-list { list-style: none; margin: 0; padding: 0; }
        .sf-list li { display: flex; gap: .5rem; align-items: flex-start; font-size: .73rem; color: var(--color-ink); padding: .25rem 0; line-height: 1.5; }
        .sf-list i { color: #12794a; margin-top: .15rem; }
    </style>

    <div class="sf-head">
        <div>
            <h2>Steadfast Courier</h2>
            <p>Automatic parcel booking, delivery tracking and COD reconciliation.</p>
        </div>
        <span class="sf-chip sf-chip-on" style="margin-left:auto">
            <i class="fa-solid fa-shield-halved"></i>Official Steadfast API v1
        </span>
    </div>

    {{-- ===== Status strip ===== --}}
    <div class="sf-top">
        <div class="sf-stat">
            <span class="sf-stat-ico" style="background:#e0f2fe;color:#0369a1"><i class="fa-solid fa-tower-broadcast"></i></span>
            <div style="min-width:0">
                <div class="sf-stat-key">Steadfast Server</div>
                <div class="sf-stat-val" id="sf-ping-state">{{ $configured ? 'Ready to Test' : 'Not Configured' }}</div>
            </div>
            <button type="button" class="sf-btn sf-btn-ghost sf-btn-sm" style="margin-left:auto" data-sf-action="ping">
                <i class="fa-solid fa-wifi"></i>Ping
            </button>
        </div>

        <div class="sf-stat">
            <span class="sf-stat-ico" style="background:#dcfce7;color:#15803d"><i class="fa-solid fa-wallet"></i></span>
            <div style="min-width:0">
                <div class="sf-stat-key">Courier Balance</div>
                <div class="sf-stat-val" id="sf-balance-state">—</div>
                <div class="sf-stat-sub">Delivered COD payout</div>
            </div>
            <button type="button" class="sf-btn sf-btn-ghost sf-btn-sm" style="margin-left:auto" data-sf-action="balance">
                <i class="fa-solid fa-rotate"></i>Check
            </button>
        </div>

        <div class="sf-stat">
            <span class="sf-stat-ico" style="background:#e3f3f3;color:#0e9495"><i class="fa-solid fa-key"></i></span>
            <div style="min-width:0">
                <div class="sf-stat-key">Credential Status</div>
                <div class="sf-stat-val">{{ $configured ? 'Keys Configured' : 'Keys Missing' }}</div>
                <div class="sf-stat-sub">{{ $autoBook ? 'Auto booking is on' : 'Auto booking is off' }}</div>
            </div>
            <span class="sf-chip {{ $enabled ? 'sf-chip-on' : 'sf-chip-off' }}" style="margin-left:auto">
                <span class="sf-dot"></span>{{ $enabled ? 'Active' : 'Off' }}
            </span>
        </div>
    </div>

    <div class="sf-wrap">
        {{-- ===== Credentials ===== --}}
        <div>
            <form method="POST" action="{{ route('admin.steadfast.update') }}">
                @csrf @method('PUT')

                <div class="sf-card">
                    <div class="sf-card-head">
                        <span class="sf-icon"><i class="fa-solid fa-sliders"></i></span>
                        <div>
                            <h3>API Credentials &amp; Webhook Settings</h3>
                            <p>Collect your Api-Key and Secret-Key from the Steadfast merchant portal.</p>
                        </div>
                    </div>
                    <div class="sf-card-body">
                        <div class="sf-field">
                            <label class="sf-label" for="steadfast_api_key">Steadfast API Key <span class="sf-req">*</span></label>
                            <div class="sf-inputwrap">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" id="steadfast_api_key" name="steadfast_api_key" class="sf-input"
                                       value="{{ old('steadfast_api_key', $settings['steadfast_api_key'] ?? '') }}"
                                       placeholder="invzbqu0jwkoqhb7ilga832emrne0w3l">
                            </div>
                            <p class="sf-hint">Steadfast Portal → Settings → API Information.</p>
                        </div>

                        <div class="sf-field">
                            <label class="sf-label" for="steadfast_secret_key">
                                Steadfast Secret Key <span class="sf-req">*</span>
                                @if(filled($settings['steadfast_secret_key'] ?? null))
                                    <span class="sf-chip sf-chip-on"><i class="fa-solid fa-lock"></i>Saved</span>
                                @endif
                            </label>
                            <div class="sf-inputwrap">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" id="steadfast_secret_key" name="steadfast_secret_key" class="sf-input"
                                       autocomplete="new-password"
                                       placeholder="{{ filled($settings['steadfast_secret_key'] ?? null) ? 'Leave blank to keep the saved key' : 'Paste your secret key' }}">
                                <button type="button" class="sf-inline-btn" data-sf-reveal="steadfast_secret_key">
                                    <i class="fa-regular fa-eye"></i>Show
                                </button>
                            </div>
                            <p class="sf-hint">Used to sign and authorise parcel bookings. Leaving this blank keeps the key already saved.</p>
                        </div>

                        <div class="sf-field">
                            <label class="sf-label" for="steadfast_webhook_token">
                                Webhook Auth Token <span class="sf-muted-label">(recommended)</span>
                            </label>
                            <div class="sf-inputwrap">
                                <i class="fa-solid fa-shield-halved"></i>
                                <input type="text" id="steadfast_webhook_token" name="steadfast_webhook_token" class="sf-input"
                                       value="{{ old('steadfast_webhook_token', $settings['steadfast_webhook_token'] ?? '') }}"
                                       placeholder="e.g. Np6lJ-NnIvnFaeDAA-tukMwr9fqboqBm">
                                <button type="button" class="sf-inline-btn" id="sf-gen-token">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i>Generate
                                </button>
                            </div>
                            <p class="sf-hint">Paste the same token into the portal's webhook settings. Steadfast signs each callback with it (HMAC-SHA256), and callbacks that fail the check are rejected. Leave it empty only while testing.</p>
                        </div>

                        <div class="sf-field">
                            <label class="sf-label" for="steadfast_base_url">
                                Base API URL
                                <button type="button" class="sf-inline-btn" style="position:static;margin-left:auto" id="sf-reset-url">Reset to default</button>
                            </label>
                            <div class="sf-inputwrap">
                                <i class="fa-solid fa-globe"></i>
                                <input type="text" id="steadfast_base_url" name="steadfast_base_url" class="sf-input"
                                       value="{{ old('steadfast_base_url', $baseUrl) }}">
                            </div>
                            <p class="sf-hint">Default: <code>{{ \App\Services\SteadfastService::DEFAULT_BASE_URL }}</code></p>
                        </div>

                        <div class="sf-switchrow">
                            <label class="sf-switch">
                                <input type="checkbox" name="steadfast_enabled" value="1" @checked($enabled)>
                                <span class="sf-track"></span>
                            </label>
                            <div>
                                <strong>Enable Steadfast integration</strong>
                                <span>Turns on booking, tracking and the delivery-status webhook.</span>
                            </div>
                        </div>

                        <div class="sf-switchrow">
                            <label class="sf-switch">
                                <input type="checkbox" name="steadfast_auto_book" value="1" @checked($autoBook)>
                                <span class="sf-track"></span>
                            </label>
                            <div>
                                <strong>Book every new order automatically</strong>
                                <span>A parcel is created as soon as an order is placed. Leave this off if you confirm orders by phone first — you can still book each order by hand from its detail page.</span>
                            </div>
                        </div>

                        <div style="display:flex;justify-content:flex-end;padding-top:1rem">
                            <button type="submit" class="sf-btn sf-btn-brand"><i class="fa-solid fa-floppy-disk"></i>Save Configuration</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- ===== Right column ===== --}}
        <div>
            <div class="sf-side">
                <h4><i class="fa-solid fa-diagram-project" style="color:var(--color-brand)"></i>Webhook Callback URL</h4>
                <p>Paste this into the Steadfast merchant portal's webhook settings to receive automatic parcel tracking and status updates.</p>
                <div class="sf-url">
                    <code id="sf-callback">{{ $callbackUrl }}</code>
                    <button type="button" class="sf-btn sf-btn-ghost sf-btn-sm" id="sf-copy"><i class="fa-regular fa-copy"></i>Copy</button>
                </div>
                <ul class="sf-list" style="margin-top:.75rem">
                    <li><i class="fa-solid fa-circle-check"></i><span><strong>Method:</strong> POST (JSON)</span></li>
                    <li><i class="fa-solid fa-circle-check"></i><span><strong>Signature:</strong> X-Signature (HMAC-SHA256)</span></li>
                    <li><i class="fa-solid fa-circle-check"></i><span><strong>Updates:</strong> order status and COD payment state</span></li>
                </ul>
            </div>

            <div class="sf-side">
                <h4><i class="fa-solid fa-heart-pulse" style="color:#dc2626"></i>Live Diagnostics</h4>
                <p>Real-time checks against the Steadfast API using the saved credentials.</p>
                <div class="sf-duo">
                    <button type="button" class="sf-btn sf-btn-ghost" data-sf-action="ping"><i class="fa-solid fa-tower-broadcast"></i>Test Ping</button>
                    <button type="button" class="sf-btn sf-btn-ghost" data-sf-action="balance"><i class="fa-solid fa-wallet"></i>Get Balance</button>
                </div>
                <div class="sf-result" id="sf-diag-result"></div>
            </div>

            <div class="sf-side">
                <h4><i class="fa-solid fa-magnifying-glass-location" style="color:var(--color-accent)"></i>Track Consignment</h4>
                <p>Look a parcel up by consignment ID, tracking code or the order number used as the invoice.</p>
                <div class="sf-field">
                    <div class="sf-inputwrap">
                        <i class="fa-solid fa-barcode"></i>
                        <input type="text" id="sf-track-ref" class="sf-input" placeholder="CID / Tracking / Invoice">
                    </div>
                </div>
                <button type="button" class="sf-btn sf-btn-dark" data-sf-action="track"><i class="fa-solid fa-magnifying-glass"></i>Track Order</button>
                <div class="sf-result" id="sf-track-result"></div>
            </div>
        </div>
    </div>

    @push('scripts')
    @php
        $endpoints = [
            'ping' => route('admin.steadfast.ping'),
            'balance' => route('admin.steadfast.balance'),
            'track' => route('admin.steadfast.track'),
            'token' => route('admin.steadfast.token'),
        ];
    @endphp
    <script>
        (function () {
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const urls = @json($endpoints);

            const post = async (url, body) => {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify(body || {}),
                });
                return response.json();
            };

            const show = (box, ok, html) => {
                box.className = 'sf-result ' + (ok ? 'sf-result-ok' : 'sf-result-bad');
                box.innerHTML = html;
                box.style.display = 'block';
            };

            const busy = (btn, on, original) => {
                btn.disabled = on;
                if (on) {
                    btn.dataset.html = btn.innerHTML;
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
                } else {
                    btn.innerHTML = original || btn.dataset.html;
                }
            };

            document.querySelectorAll('[data-sf-action]').forEach(function (btn) {
                btn.addEventListener('click', async function () {
                    const action = btn.getAttribute('data-sf-action');
                    const box = document.getElementById(action === 'track' ? 'sf-track-result' : 'sf-diag-result');
                    busy(btn, true);

                    try {
                        if (action === 'ping') {
                            const data = await post(urls.ping);
                            document.getElementById('sf-ping-state').textContent = data.ok ? 'Connected' : 'Unreachable';
                            show(box, data.ok, '<strong>' + (data.ok ? 'Connected' : 'Failed') + '</strong><br>' + data.message);
                        } else if (action === 'balance') {
                            const data = await post(urls.balance);
                            if (data.ok && data.balance !== null) {
                                document.getElementById('sf-balance-state').textContent = '৳' + data.balance;
                            }
                            show(box, data.ok, data.ok
                                ? '<strong>Current balance:</strong> ৳' + (data.balance ?? '0')
                                : '<strong>Failed</strong><br>' + data.message);
                        } else if (action === 'track') {
                            const reference = document.getElementById('sf-track-ref').value.trim();
                            if (!reference) {
                                show(box, false, 'Enter a consignment ID, tracking code or invoice first.');
                                busy(btn, false);
                                return;
                            }
                            const data = await post(urls.track, { reference: reference });
                            show(box, data.ok && data.status,
                                data.status
                                    ? '<strong>' + data.reference + '</strong><br>Delivery status: <strong>' + data.status + '</strong>'
                                    : '<strong>Not found</strong><br>' + data.message);
                        }
                    } catch (error) {
                        show(box, false, 'Request failed: ' + error.message);
                    }

                    busy(btn, false);
                });
            });

            document.getElementById('sf-copy').addEventListener('click', function () {
                navigator.clipboard.writeText(document.getElementById('sf-callback').textContent.trim())
                    .then(() => { this.innerHTML = '<i class="fa-solid fa-check"></i>Copied'; })
                    .catch(() => { this.innerHTML = 'Copy failed'; });
            });

            document.getElementById('sf-gen-token').addEventListener('click', async function () {
                const data = await post(urls.token);
                if (data.token) document.getElementById('steadfast_webhook_token').value = data.token;
            });

            document.getElementById('sf-reset-url').addEventListener('click', function () {
                document.getElementById('steadfast_base_url').value = @json(\App\Services\SteadfastService::DEFAULT_BASE_URL);
            });

            document.querySelectorAll('[data-sf-reveal]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const input = document.getElementById(btn.getAttribute('data-sf-reveal'));
                    const hidden = input.type === 'password';
                    input.type = hidden ? 'text' : 'password';
                    btn.innerHTML = hidden
                        ? '<i class="fa-regular fa-eye-slash"></i>Hide'
                        : '<i class="fa-regular fa-eye"></i>Show';
                });
            });
        })();
    </script>
    @endpush
</x-admin-layout>
