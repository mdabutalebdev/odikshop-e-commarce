<x-layout title="Contact">
    @php
        // Falls back to sensible defaults so the panel never renders half-empty on a
        // fresh install; every value is overridable from Admin → Site Settings.
        $phone = $settings['phone'] ?? '';
        $email = $settings['email'] ?? '';
        $address = $settings['address'] ?? 'Banasree, Rampura, Dhaka-1219';
        $hours = $settings['business_hours'] ?? 'Every day, 9:00 AM – 10:00 PM';
        $whatsapp = $settings['whatsapp'] ?? '';
    @endphp

    {{-- Inline CSS on purpose: the compiled Tailwind bundle is fixed at build time,
         so a page that ships as a Blade edit alone cannot introduce new utilities. --}}
    <style>
        .ctc-panel { background: #fff; border: 1px solid var(--color-line); border-radius: 16px; overflow: hidden; }
        .ctc-head { background: var(--color-brand); color: #fff; padding: 1.15rem 1.25rem; }
        .ctc-head h2 { font-size: 1.05rem; font-weight: 800; margin: 0; }
        .ctc-head p { font-size: .8125rem; opacity: .9; margin: .2rem 0 0; }

        .ctc-row { display: flex; gap: .85rem; align-items: flex-start; padding: .95rem 1.25rem; border-top: 1px solid var(--color-line); }
        .ctc-ico { width: 2.5rem; height: 2.5rem; flex: 0 0 auto; border-radius: 9999px; background: var(--color-brand-light); color: var(--color-brand); display: grid; place-items: center; font-size: .95rem; }
        .ctc-label { font-size: .75rem; color: var(--color-muted); margin-bottom: .1rem; }
        .ctc-val { font-weight: 700; color: var(--color-ink); line-height: 1.45; word-break: break-word; }
        .ctc-val a { color: inherit; text-decoration: none; }
        .ctc-val a:hover { color: var(--color-brand); }
        .ctc-note { font-size: .75rem; color: var(--color-muted); font-weight: 500; margin-top: .15rem; }

        .ctc-badge { display: inline-flex; align-items: center; gap: .35rem; margin-top: .35rem; background: #e7f7ee; color: #12794a; font-size: .7rem; font-weight: 700; padding: .25rem .55rem; border-radius: 9999px; }

        .ctc-wa { display: flex; align-items: center; justify-content: center; gap: .5rem; margin: 1rem 1.25rem 1.25rem; height: 2.75rem; border-radius: 10px; background: #25D366; color: #fff; font-weight: 700; font-size: .875rem; text-decoration: none; transition: background .2s ease; }
        .ctc-wa:hover { background: #1da851; }

        .ctc-form-head { margin-bottom: 1.1rem; }
        .ctc-form-head h2 { font-size: 1.05rem; font-weight: 800; color: var(--color-ink); margin: 0; }
        .ctc-form-head p { font-size: .8125rem; color: var(--color-muted); margin: .2rem 0 0; }
    </style>

    <div class="container-x py-6">
        <h1 class="text-2xl font-extrabold mb-1">Contact Us</h1>
        <p class="text-muted text-sm mb-6">Have a question or need help? Reach out to us anytime.</p>

        @if(session('success'))
            <div class="bg-brand-light border border-brand/20 text-brand rounded-lg p-3 mb-4 text-sm"><i class="fa-solid fa-circle-check mr-1"></i>{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="bg-white border border-sale/30 text-sale rounded-lg p-3 mb-4 text-sm">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i>{{ $errors->first() }}
            </div>
        @endif

        <div class="grid md:grid-cols-3 gap-6">
            {{-- Info panel --}}
            <div>
                <div class="ctc-panel">
                    <div class="ctc-head">
                        <h2>Get in Touch</h2>
                        <p>We are always here to help you</p>
                    </div>

                    <div class="ctc-row" style="border-top: 0;">
                        <span class="ctc-ico"><i class="fa-solid fa-location-dot"></i></span>
                        <div>
                            <div class="ctc-label">Address</div>
                            <div class="ctc-val">{{ $address }}</div>
                        </div>
                    </div>

                    @if($phone)
                        <div class="ctc-row">
                            <span class="ctc-ico"><i class="fa-solid fa-phone"></i></span>
                            <div>
                                <div class="ctc-label">Phone / Hotline</div>
                                <div class="ctc-val"><a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">{{ $phone }}</a></div>
                                <div class="ctc-note">Call us anytime</div>
                            </div>
                        </div>
                    @endif

                    @if($email)
                        <div class="ctc-row">
                            <span class="ctc-ico"><i class="fa-solid fa-envelope"></i></span>
                            <div>
                                <div class="ctc-label">Email</div>
                                <div class="ctc-val"><a href="mailto:{{ $email }}">{{ $email }}</a></div>
                                <div class="ctc-note">We reply within 24 hours</div>
                            </div>
                        </div>
                    @endif

                    <div class="ctc-row">
                        <span class="ctc-ico"><i class="fa-regular fa-clock"></i></span>
                        <div>
                            <div class="ctc-label">Business Hours</div>
                            <div class="ctc-val">{{ $hours }}</div>
                            <span class="ctc-badge"><i class="fa-solid fa-cart-shopping"></i>Ordering open 24/7</span>
                        </div>
                    </div>

                    @if($whatsapp)
                        <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener" class="ctc-wa">
                            <i class="fa-brands fa-whatsapp"></i>Message us on WhatsApp
                        </a>
                    @endif
                </div>
            </div>

            {{-- Form --}}
            <div class="md:col-span-2 bg-white rounded-xl border border-line p-5">
                <div class="ctc-form-head">
                    <h2>Send us a Message</h2>
                    <p>Fill in the form below and we will get back to you shortly.</p>
                </div>

                <form method="POST" action="{{ route('contact.submit') }}" class="grid sm:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium mb-1">Name <span class="text-sale">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium mb-1">Message <span class="text-sale">*</span></label>
                        <textarea name="message" required rows="5" class="w-full rounded-lg border border-line px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">{{ old('message') }}</textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <button type="submit" class="btn-brand h-11 px-8"><i class="fa-solid fa-paper-plane"></i>Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
