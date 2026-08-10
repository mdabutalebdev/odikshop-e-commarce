<footer class="bg-ink text-white/70 mt-10">
    <div class="container-x py-10 grid grid-cols-2 md:grid-cols-4 gap-8 text-sm">
        <div class="col-span-2 md:col-span-1">
            <div class="bg-white rounded-lg p-2.5 inline-block mb-3">
                <img src="{{ asset('images/logo-header.png') }}" alt="{{ $settings['site_name'] ?? 'ODHIK SHOP' }}" class="h-8 w-auto object-contain">
            </div>
            <p class="leading-relaxed">{{ $settings['footer_text'] ?? 'ODHIK SHOP E-commerce BD — your trusted online shopping platform.' }}</p>
            <div class="flex items-center gap-2.5 mt-4 text-base">
                @if(!empty($settings['facebook']))<a href="{{ $settings['facebook'] }}" target="_blank" aria-label="Facebook" class="w-9 h-9 grid place-items-center rounded-full bg-white/10 hover:bg-brand transition"><i class="fa-brands fa-facebook-f"></i></a>@endif
                @if(!empty($settings['instagram']))<a href="{{ $settings['instagram'] }}" target="_blank" aria-label="Instagram" class="w-9 h-9 grid place-items-center rounded-full bg-white/10 hover:bg-brand transition"><i class="fa-brands fa-instagram"></i></a>@endif
                @if(!empty($settings['youtube']))<a href="{{ $settings['youtube'] }}" target="_blank" aria-label="YouTube" class="w-9 h-9 grid place-items-center rounded-full bg-white/10 hover:bg-brand transition"><i class="fa-brands fa-youtube"></i></a>@endif
                @if(!empty($settings['tiktok']))<a href="{{ $settings['tiktok'] }}" target="_blank" aria-label="TikTok" class="w-9 h-9 grid place-items-center rounded-full bg-white/10 hover:bg-brand transition"><i class="fa-brands fa-tiktok"></i></a>@endif
                @if(!empty($settings['telegram']))<a href="{{ $settings['telegram'] }}" target="_blank" aria-label="Telegram" class="w-9 h-9 grid place-items-center rounded-full bg-white/10 hover:bg-brand transition"><i class="fa-brands fa-telegram"></i></a>@endif
                @if(!empty($settings['whatsapp']))<a href="https://wa.me/{{ $settings['whatsapp'] }}" target="_blank" aria-label="WhatsApp" class="w-9 h-9 grid place-items-center rounded-full bg-white/10 hover:bg-brand transition"><i class="fa-brands fa-whatsapp"></i></a>@endif
            </div>
        </div>

        <div>
            <h4 class="text-white font-bold mb-3">Information</h4>
            <ul class="space-y-2">
                <li><a href="{{ route('page', 'about') }}" class="hover:text-white transition">About Us</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-white transition">Contact Us</a></li>
                <li><a href="{{ route('page', 'terms') }}" class="hover:text-white transition">Terms &amp; Conditions</a></li>
                <li><a href="{{ route('page', 'privacy') }}" class="hover:text-white transition">Privacy Policy</a></li>
                <li><a href="{{ route('page', 'refund-policy') }}" class="hover:text-white transition">Return &amp; Refund</a></li>
            </ul>
        </div>

        <div>
            <h4 class="text-white font-bold mb-3">Support</h4>
            <ul class="space-y-2">
                <li><a href="{{ route('page', 'how-to-order') }}" class="hover:text-white transition">How to Order</a></li>
                <li><a href="{{ route('track') }}" class="hover:text-white transition">Order Tracking</a></li>
                <li><a href="{{ route('page', 'shipping') }}" class="hover:text-white transition">Shipping &amp; Delivery</a></li>
                <li><a href="{{ route('page', 'faq') }}" class="hover:text-white transition">FAQ</a></li>
                <li><a href="{{ route('wishlist.index') }}" class="hover:text-white transition">Wishlist</a></li>
            </ul>
        </div>

        <div>
            <h4 class="text-white font-bold mb-3">Contact Us</h4>
            <ul class="space-y-2">
                <li class="flex items-start gap-2"><i class="fa-solid fa-location-dot mt-1 text-brand"></i><span>{{ $settings['address'] ?? 'Banasree, Rampura, Dhaka-1219' }}</span></li>
                <li><a href="tel:{{ $settings['phone'] ?? '' }}" class="flex items-center gap-2 hover:text-white"><i class="fa-solid fa-phone text-brand"></i>{{ $settings['phone'] ?? '' }}</a></li>
                @if(!empty($settings['whatsapp']))<li><a href="https://wa.me/{{ $settings['whatsapp'] }}" target="_blank" class="flex items-center gap-2 hover:text-white"><i class="fa-brands fa-whatsapp text-brand"></i>WhatsApp</a></li>@endif
                <li><a href="mailto:{{ $settings['email'] ?? '' }}" class="flex items-center gap-2 hover:text-white"><i class="fa-solid fa-envelope text-brand"></i>{{ $settings['email'] ?? '' }}</a></li>
            </ul>
            <div class="flex items-center gap-2 mt-4 text-white/50 text-xs">
                <i class="fa-solid fa-truck-fast"></i> Cash on Delivery available
            </div>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="container-x py-4 text-center text-xs text-white/60">
            Copyright © {{ max((int) date('Y'), 2026) }} <span class="font-semibold text-white">ODHIK SHOP</span>
        </div>
    </div>
</footer>
