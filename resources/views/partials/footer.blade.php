<footer class="bg-brand-dark text-white/80 mt-10">
    <div class="container-x py-10 grid grid-cols-2 md:grid-cols-4 gap-8 text-sm">
        <div class="col-span-2 md:col-span-1">
            <div class="flex items-center gap-2 mb-3">
                <span class="grid place-items-center w-9 h-9 rounded-lg bg-white text-brand font-extrabold text-lg">O</span>
                <span class="font-extrabold text-lg text-white">{{ $settings['site_name'] ?? 'ODHIK SHOP' }}</span>
            </div>
            <p class="leading-relaxed">{{ $settings['footer_text'] ?? 'ODHIK SHOP - আপনার বিশ্বস্ত অনলাইন শপিং প্ল্যাটফর্ম।' }}</p>
            <div class="flex items-center gap-3 mt-4 text-lg">
                <a href="{{ $settings['facebook'] ?? '#' }}" class="w-9 h-9 grid place-items-center rounded-full bg-white/10 hover:bg-white/20 transition"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="{{ $settings['instagram'] ?? '#' }}" class="w-9 h-9 grid place-items-center rounded-full bg-white/10 hover:bg-white/20 transition"><i class="fa-brands fa-instagram"></i></a>
                <a href="https://wa.me/{{ $settings['whatsapp'] ?? '' }}" class="w-9 h-9 grid place-items-center rounded-full bg-white/10 hover:bg-white/20 transition"><i class="fa-brands fa-whatsapp"></i></a>
            </div>
        </div>

        <div>
            <h4 class="text-white font-bold mb-3">দ্রুত লিংক</h4>
            <ul class="space-y-2">
                <li><a href="{{ route('home') }}" class="hover:text-white transition">হোম</a></li>
                <li><a href="{{ route('shop') }}" class="hover:text-white transition">সব পণ্য</a></li>
                <li><a href="{{ route('wishlist.index') }}" class="hover:text-white transition">উইশলিস্ট</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-white transition">যোগাযোগ</a></li>
            </ul>
        </div>

        <div>
            <h4 class="text-white font-bold mb-3">ক্যাটাগরি</h4>
            <ul class="space-y-2">
                @foreach(($navCategories ?? collect())->take(5) as $cat)
                    <li><a href="{{ route('shop', ['category' => $cat->slug]) }}" class="hover:text-white transition">{{ $cat->name }}</a></li>
                @endforeach
            </ul>
        </div>

        <div>
            <h4 class="text-white font-bold mb-3">যোগাযোগ</h4>
            <ul class="space-y-2">
                <li><i class="fa-solid fa-phone mr-2"></i>{{ $settings['phone'] ?? '01700-000000' }}</li>
                <li><i class="fa-solid fa-envelope mr-2"></i>{{ $settings['email'] ?? 'support@odikshop.com' }}</li>
                <li><i class="fa-solid fa-location-dot mr-2"></i>{{ $settings['address'] ?? 'ঢাকা, বাংলাদেশ' }}</li>
            </ul>
            <div class="flex items-center gap-2 mt-4 text-white/60 text-xs">
                <i class="fa-solid fa-truck-fast"></i> ক্যাশ অন ডেলিভারি
            </div>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="container-x py-4 text-center text-xs text-white/70">
            © {{ date('Y') }} <span class="font-semibold text-white">{{ $settings['site_name'] ?? 'ODHIK SHOP' }}</span> — সব অধিকার সংরক্ষিত
        </div>
    </div>
</footer>
