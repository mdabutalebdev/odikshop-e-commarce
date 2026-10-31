<x-layout title="চেকআউট">
    <div class="container-x py-6"
         x-data="{
            zone: 'inside_dhaka',
            subtotal: {{ (int) $subtotal }},
            inside: {{ (int) $insideFee }},
            outside: {{ (int) $outsideFee }},
            district: '',
            thanas: [],
            geo: {{ Illuminate\Support\Js::from($bdGeo) }},
            onDistrictChange() { this.thanas = this.district && this.geo[this.district] ? this.geo[this.district] : [] },
            get shipping() { return this.zone === 'inside_dhaka' ? this.inside : this.outside },
            get total() { return this.subtotal + this.shipping },
            fmt(n) { return 'BDT ' + n.toLocaleString('en-US') }
         }">

        <h1 class="text-xl font-extrabold mb-4">চেকআউট</h1>

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
                    <h2 class="font-bold mb-4 flex items-center gap-2 text-sm">Shipping Details</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">আপনার নাম <span class="text-sale">*</span></label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" placeholder="আপনার নাম লিখুন" required class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-1">মোবাইল নাম্বার <span class="text-sale">*</span></label>
                            <div class="flex items-center h-11 rounded-lg border border-line overflow-hidden focus-within:ring-2 focus-within:ring-brand/30">
                                <div class="px-3 bg-canvas border-r border-line h-full flex items-center justify-center text-lg shrink-0">
                                    🇧🇩
                                </div>
                                <input type="text" name="phone" value="{{ old('phone', auth()->user()?->phone) }}" required placeholder="01XXXXXXX" class="w-full h-full px-3 text-sm focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">জেলা <span class="text-sale">*</span></label>
                            <select name="district" x-model="district" @change="onDistrictChange()" required class="w-full h-11 rounded-lg border border-line px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand/30">
                                <option value="">জেলা সিলেক্ট করুন</option>
                                @foreach(array_keys($bdGeo) as $district)
                                    <option value="{{ $district }}" @selected(old('district') === $district)>{{ $district }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-1">থানা/উপজেলা <span class="text-sale">*</span></label>
                            <select name="thana" required :disabled="!thanas.length" class="w-full h-11 rounded-lg border border-line px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand/30 disabled:bg-canvas disabled:text-muted">
                                <option value="">থানা/উপজেলা সিলেক্ট করুন</option>
                                <template x-for="t in thanas" :key="t">
                                    <option :value="t" x-text="t" :selected="'{{ old('thana') }}' === t"></option>
                                </template>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-1">সম্পূর্ণ ঠিকানা <span class="text-sale">*</span></label>
                            <textarea name="address" required rows="2" placeholder="গ্রাম/এলাকা/রাস্তা/বাড়ী" class="w-full rounded-lg border border-line px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">{{ old('address') }}</textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-1">অর্ডার নোট <span class="text-muted font-normal">(Optional)</span></label>
                            <textarea name="notes" rows="2" placeholder="কিছু বলার থাকলে এখানে লিখুন।" class="w-full rounded-lg border border-line px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Delivery zone --}}
                <div class="bg-white rounded-xl border border-line p-5">
                    <h2 class="font-bold mb-4 flex items-center gap-2 text-sm"><i class="fa-solid fa-truck text-brand"></i>ডেলিভারি এলাকা</h2>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 border rounded-lg p-3 cursor-pointer" :class="zone==='inside_dhaka' ? 'border-brand bg-brand-light' : 'border-line'">
                            <input type="radio" name="delivery_zone" value="inside_dhaka" x-model="zone" class="accent-brand">
                            <span class="text-sm font-medium">ঢাকার ভিতরে <span class="text-muted">({{ bdt($insideFee) }})</span></span>
                        </label>
                        <label class="flex items-center gap-3 border rounded-lg p-3 cursor-pointer" :class="zone==='outside_dhaka' ? 'border-brand bg-brand-light' : 'border-line'">
                            <input type="radio" name="delivery_zone" value="outside_dhaka" x-model="zone" class="accent-brand">
                            <span class="text-sm font-medium">ঢাকার বাইরে <span class="text-muted">({{ bdt($outsideFee) }})</span></span>
                        </label>
                    </div>
                </div>

                {{-- Payment --}}
                @php $paySettings = \App\Models\SiteSetting::getAll(); @endphp
                <div class="bg-white rounded-xl border border-line p-5" x-data="{ pay: 'cod', channel: '' }">
                    <h2 class="font-bold mb-4 flex items-center gap-2 text-sm"><i class="fa-solid fa-wallet text-brand"></i>পেমেন্ট পদ্ধতি</h2>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 border rounded-lg p-3 cursor-pointer" :class="pay==='cod' ? 'border-brand bg-brand-light' : 'border-line'">
                            <input type="radio" name="payment_method" value="cod" x-model="pay" class="accent-brand" checked>
                            <span class="text-sm font-medium flex items-center gap-2"><i class="fa-solid fa-money-bill-wave text-brand"></i>ক্যাশ অন ডেলিভারি</span>
                        </label>
                        <label class="flex items-center gap-3 border rounded-lg p-3 cursor-pointer" :class="pay==='mobile_banking' ? 'border-brand bg-brand-light' : 'border-line'">
                            <input type="radio" name="payment_method" value="mobile_banking" x-model="pay" class="accent-brand">
                            <span class="text-sm font-medium flex items-center gap-2"><i class="fa-solid fa-mobile-screen text-brand"></i>মোবাইল ব্যাংকিং (বিকাশ / নগদ)</span>
                        </label>
                    </div>

                    {{-- Manual bKash/Nagad payment details (shown only for mobile banking) --}}
                    <div x-show="pay==='mobile_banking'" x-cloak class="mt-4 border-t border-line pt-4 space-y-4">
                        <div class="bg-brand-light rounded-lg p-3 text-sm space-y-1.5">
                            <p class="font-semibold text-ink">{{ $paySettings['payment_instruction'] ?? 'অনুগ্রহ করে আগে পেমেন্ট সম্পন্ন করুন, তারপর নিচের তথ্যগুলো দিন।' }}</p>
                            @if(!empty($paySettings['bkash_number']))
                                <p class="text-ink/80"><span class="inline-block w-16 font-semibold">bKash</span>: <span class="font-bold text-brand">{{ $paySettings['bkash_number'] }}</span> <span class="text-muted text-xs">(Send Money)</span></p>
                            @endif
                            @if(!empty($paySettings['nagad_number']))
                                <p class="text-ink/80"><span class="inline-block w-16 font-semibold">Nagad</span>: <span class="font-bold text-brand">{{ $paySettings['nagad_number'] }}</span> <span class="text-muted text-xs">(Send Money)</span></p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1.5">কোন মাধ্যমে পাঠিয়েছেন? <span class="text-sale">*</span></label>
                            <div class="flex gap-3">
                                <label class="flex items-center gap-2 border rounded-lg px-3 h-11 cursor-pointer flex-1" :class="channel==='bkash' ? 'border-brand bg-brand-light' : 'border-line'">
                                    <input type="radio" name="payment_channel" value="bkash" x-model="channel" class="accent-brand" :required="pay==='mobile_banking'">
                                    <span class="text-sm font-medium">bKash</span>
                                </label>
                                <label class="flex items-center gap-2 border rounded-lg px-3 h-11 cursor-pointer flex-1" :class="channel==='nagad' ? 'border-brand bg-brand-light' : 'border-line'">
                                    <input type="radio" name="payment_channel" value="nagad" x-model="channel" class="accent-brand" :required="pay==='mobile_banking'">
                                    <span class="text-sm font-medium">Nagad</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">যে নম্বর থেকে পাঠিয়েছেন <span class="text-sale">*</span></label>
                            <input type="text" name="payment_sender_number" value="{{ old('payment_sender_number') }}" placeholder="01XXXXXXXXX" :required="pay==='mobile_banking'" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Transaction ID (TrxID) <span class="text-sale">*</span></label>
                            <input type="text" name="payment_txn_id" value="{{ old('payment_txn_id') }}" placeholder="যেমন: 8N7A6D5EE7M" :required="pay==='mobile_banking'" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order review --}}
            <div class="bg-white rounded-xl border border-line p-5 h-fit lg:sticky lg:top-32">
                <h2 class="font-bold mb-4 flex items-center gap-2 text-sm"><i class="fa-solid fa-receipt text-brand"></i>Order Summary</h2>
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @foreach($items as $item)
                        <div class="flex gap-3 items-center">
                            <div class="w-14 h-14 rounded-lg bg-canvas overflow-hidden shrink-0">
                                <img src="{{ image_url($item['product']->main_image, $item['product']->name) }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-semibold clamp-2">{{ $item['product']->name }}</div>
                                @if(!empty($item['options']))
                                    <div class="text-[11px] text-muted">
                                        @foreach($item['options'] as $k => $v)
                                            <span class="mr-1">{{ $k }}: <span class="text-ink font-semibold">{{ $v }}</span></span>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="text-xs text-muted">{{ $item['quantity'] }} × {{ bdt($item['product']->price) }}</div>
                            </div>
                            <div class="text-sm font-bold">{{ bdt($item['subtotal']) }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-line mt-4 pt-4 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-muted">মোট:</span><span class="font-semibold">{{ bdt($subtotal) }}</span></div>
                    <div class="flex justify-between"><span class="text-muted">ডেলিভারি চার্জ:</span><span class="font-semibold" x-text="fmt(shipping)"></span></div>
                    <div class="flex justify-between text-lg font-bold border-t border-line pt-2"><span>সর্বমোট:</span><span class="text-brand" x-text="fmt(total)"></span></div>
                </div>

                <button type="submit" class="btn-accent w-full h-12 mt-5 text-base">
                    <i class="fa-solid fa-circle-check"></i>অর্ডার নিশ্চিত করুন
                </button>
                <p class="text-xs text-muted text-center mt-3"><i class="fa-solid fa-lock mr-1"></i>নিরাপদ ও সুরক্ষিত চেকআউট</p>
            </div>
        </form>
    </div>
</x-layout>
