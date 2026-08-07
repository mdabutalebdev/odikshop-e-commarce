<x-admin-layout title="সাইট সেটিংস">
    <form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-2xl space-y-6">
        @csrf @method('PUT')

        <div class="bg-white rounded-xl border border-line p-5 space-y-4">
            <h2 class="font-bold flex items-center gap-2"><i class="fa-solid fa-store text-brand"></i>সাধারণ তথ্য</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">সাইটের নাম</label>
                    <input type="text" name="site_name" value="{{ $settings['site_name'] ?? '' }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">ট্যাগলাইন</label>
                    <input type="text" name="site_tagline" value="{{ $settings['site_tagline'] ?? '' }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium mb-1">টপবার টেক্সট</label>
                    <input type="text" name="topbar_text" value="{{ $settings['topbar_text'] ?? '' }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium mb-1">ফুটার টেক্সট</label>
                    <textarea name="footer_text" rows="2" class="w-full rounded-lg border border-line px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">{{ $settings['footer_text'] ?? '' }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-line p-5 space-y-4">
            <h2 class="font-bold flex items-center gap-2"><i class="fa-solid fa-address-book text-brand"></i>যোগাযোগ ও সোশ্যাল</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium mb-1">ফোন</label><input type="text" name="phone" value="{{ $settings['phone'] ?? '' }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30"></div>
                <div><label class="block text-sm font-medium mb-1">ইমেইল</label><input type="text" name="email" value="{{ $settings['email'] ?? '' }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30"></div>
                <div class="sm:col-span-2"><label class="block text-sm font-medium mb-1">ঠিকানা</label><input type="text" name="address" value="{{ $settings['address'] ?? '' }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30"></div>
                <div><label class="block text-sm font-medium mb-1">ফেসবুক</label><input type="text" name="facebook" value="{{ $settings['facebook'] ?? '' }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30"></div>
                <div><label class="block text-sm font-medium mb-1">ইনস্টাগ্রাম</label><input type="text" name="instagram" value="{{ $settings['instagram'] ?? '' }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30"></div>
                <div><label class="block text-sm font-medium mb-1">হোয়াটসঅ্যাপ</label><input type="text" name="whatsapp" value="{{ $settings['whatsapp'] ?? '' }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30"></div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-line p-5 space-y-4">
            <h2 class="font-bold flex items-center gap-2"><i class="fa-solid fa-truck text-brand"></i>ডেলিভারি চার্জ</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium mb-1">ঢাকার ভেতরে (BDT)</label><input type="number" name="shipping_inside_dhaka" value="{{ $settings['shipping_inside_dhaka'] ?? 60 }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30"></div>
                <div><label class="block text-sm font-medium mb-1">ঢাকার বাইরে (BDT)</label><input type="number" name="shipping_outside_dhaka" value="{{ $settings['shipping_outside_dhaka'] ?? 120 }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30"></div>
            </div>
        </div>

        <button type="submit" class="btn-brand h-11 px-8"><i class="fa-solid fa-floppy-disk"></i>সেটিংস সংরক্ষণ করুন</button>
    </form>
</x-admin-layout>
