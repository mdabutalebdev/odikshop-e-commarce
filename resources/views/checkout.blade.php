<x-layout title="চেকআউট">
    <div class="container-x py-6"
         x-data="{
            zone: 'inside_dhaka',
            subtotal: {{ (int) $subtotal }},
            inside: {{ (int) $insideFee }},
            outside: {{ (int) $outsideFee }},
            get shipping() { return this.zone === 'inside_dhaka' ? this.inside : this.outside },
            get total() { return this.subtotal + this.shipping },
            fmt(n) { return 'BDT ' + n.toLocaleString('en-US') }
         }">

        <h1 class="text-2xl font-extrabold mb-5">চেকআউট ও অর্ডার</h1>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-sale rounded-lg p-3 mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('checkout.store') }}" class="grid lg:grid-cols-[1fr_360px] gap-6">
            @csrf
            @if($buy)
                <input type="hidden" name="buy" value="{{ $buy }}">
                <input type="hidden" name="qty" value="{{ $buyQty }}">
            @endif

            <div class="space-y-5">
                {{-- Delivery address --}}
                <div class="bg-white rounded-xl border border-line p-5">
                    <h2 class="font-bold mb-4 flex items-center gap-2"><i class="fa-solid fa-location-dot text-brand"></i>ডেলিভারি ঠিকানা</h2>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">নাম <span class="text-sale">*</span></label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" required class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">মোবাইল <span class="text-sale">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone', auth()->user()?->phone) }}" required placeholder="01XXXXXXXXX" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">বিভাগ</label>
                            <input type="text" name="division" value="{{ old('division') }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">জেলা</label>
                            <input type="text" name="district" value="{{ old('district') }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">থানা/উপজেলা</label>
                            <input type="text" name="thana" value="{{ old('thana') }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium mb-1">বাড়ি/রাস্তা/গ্রাম <span class="text-sale">*</span></label>
                            <textarea name="address" required rows="2" class="w-full rounded-lg border border-line px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">{{ old('address') }}</textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium mb-1">অর্ডার নোট (ঐচ্ছিক)</label>
                            <textarea name="notes" rows="2" class="w-full rounded-lg border border-line px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Delivery zone --}}
                <div class="bg-white rounded-xl border border-line p-5">
                    <h2 class="font-bold mb-4 flex items-center gap-2"><i class="fa-solid fa-truck text-brand"></i>ডেলিভারি এলাকা</h2>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 border rounded-lg p-3 cursor-pointer" :class="zone==='inside_dhaka' ? 'border-brand bg-brand-light' : 'border-line'">
                            <input type="radio" name="delivery_zone" value="inside_dhaka" x-model="zone" class="accent-brand">
                            <span class="text-sm font-medium">ঢাকার ভেতরে <span class="text-muted">({{ bdt($insideFee) }})</span></span>
                        </label>
                        <label class="flex items-center gap-3 border rounded-lg p-3 cursor-pointer" :class="zone==='outside_dhaka' ? 'border-brand bg-brand-light' : 'border-line'">
                            <input type="radio" name="delivery_zone" value="outside_dhaka" x-model="zone" class="accent-brand">
                            <span class="text-sm font-medium">ঢাকার বাইরে <span class="text-muted">({{ bdt($outsideFee) }})</span></span>
                        </label>
                    </div>
                </div>

                {{-- Payment --}}
                <div class="bg-white rounded-xl border border-line p-5" x-data="{ pay: 'cod' }">
                    <h2 class="font-bold mb-4 flex items-center gap-2"><i class="fa-solid fa-wallet text-brand"></i>পেমেন্ট পদ্ধতি</h2>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 border rounded-lg p-3 cursor-pointer" :class="pay==='cod' ? 'border-brand bg-brand-light' : 'border-line'">
                            <input type="radio" name="payment_method" value="cod" x-model="pay" class="accent-brand" checked>
                            <span class="text-sm font-medium flex items-center gap-2"><i class="fa-solid fa-money-bill-wave text-brand"></i>ক্যাশ অন ডেলিভারি</span>
                        </label>
                        <label class="flex items-center gap-3 border rounded-lg p-3 cursor-pointer" :class="pay==='mobile_banking' ? 'border-brand bg-brand-light' : 'border-line'">
                            <input type="radio" name="payment_method" value="mobile_banking" x-model="pay" class="accent-brand">
                            <span class="text-sm font-medium flex items-center gap-2"><i class="fa-solid fa-mobile-screen text-brand"></i>মোবাইল ব্যাংকিং (বিকাশ/নগদ)</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Order review --}}
            <div class="bg-white rounded-xl border border-line p-5 h-fit lg:sticky lg:top-32">
                <h2 class="font-bold mb-4 flex items-center gap-2"><i class="fa-solid fa-receipt text-brand"></i>অর্ডার রিভিউ</h2>
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @foreach($items as $item)
                        <div class="flex gap-3 items-center">
                            <div class="w-14 h-14 rounded-lg bg-canvas overflow-hidden shrink-0">
                                <img src="{{ image_url($item['product']->main_image, $item['product']->name) }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-semibold clamp-2">{{ $item['product']->name }}</div>
                                <div class="text-xs text-muted">{{ $item['quantity'] }} × {{ bdt($item['product']->price) }}</div>
                            </div>
                            <div class="text-sm font-bold">{{ bdt($item['subtotal']) }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-line mt-4 pt-4 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-muted">পণ্য মূল্য:</span><span class="font-semibold">{{ bdt($subtotal) }}</span></div>
                    <div class="flex justify-between"><span class="text-muted">ডেলিভারি:</span><span class="font-semibold" x-text="fmt(shipping)"></span></div>
                    <div class="flex justify-between text-lg font-bold border-t border-line pt-2"><span>মোট:</span><span class="text-brand" x-text="fmt(total)"></span></div>
                </div>

                <button type="submit" class="btn-brand w-full h-12 mt-5 text-base font-bold">
                    <i class="fa-solid fa-circle-check"></i>অর্ডার সম্পন্ন করুন
                </button>
                <p class="text-xs text-muted text-center mt-3"><i class="fa-solid fa-lock mr-1"></i>নিরাপদ ও নিশ্চিন্ত চেকআউট</p>
            </div>
        </form>
    </div>
</x-layout>
