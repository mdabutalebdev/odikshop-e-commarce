<x-layout title="যোগাযোগ">
    <div class="container-x py-6">
        <h1 class="text-2xl font-extrabold mb-1">যোগাযোগ করুন</h1>
        <p class="text-muted text-sm mb-6">যেকোনো প্রশ্ন বা সহায়তার জন্য আমাদের সাথে যোগাযোগ করুন</p>

        @if(session('success'))
            <div class="bg-brand-light border border-brand/20 text-brand rounded-lg p-3 mb-4 text-sm"><i class="fa-solid fa-circle-check mr-1"></i>{{ session('success') }}</div>
        @endif

        <div class="grid md:grid-cols-3 gap-6">
            {{-- Info --}}
            <div class="space-y-3">
                @foreach([
                    ['fa-phone', 'ফোন', $settings['phone'] ?? '01700-000000'],
                    ['fa-envelope', 'ইমেইল', $settings['email'] ?? 'support@odikshop.com'],
                    ['fa-location-dot', 'ঠিকানা', $settings['address'] ?? 'ঢাকা, বাংলাদেশ'],
                ] as $info)
                    <div class="bg-white rounded-xl border border-line p-4 flex items-center gap-3">
                        <span class="w-11 h-11 rounded-full bg-brand-light text-brand grid place-items-center text-lg shrink-0"><i class="fa-solid {{ $info[0] }}"></i></span>
                        <div>
                            <div class="text-xs text-muted">{{ $info[1] }}</div>
                            <div class="font-semibold">{{ $info[2] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Form --}}
            <div class="md:col-span-2 bg-white rounded-xl border border-line p-5">
                <form method="POST" action="{{ route('contact.submit') }}" class="grid sm:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium mb-1">নাম <span class="text-sale">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">ফোন</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium mb-1">ইমেইল</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium mb-1">বার্তা <span class="text-sale">*</span></label>
                        <textarea name="message" required rows="4" class="w-full rounded-lg border border-line px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">{{ old('message') }}</textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <button type="submit" class="btn-brand h-11 px-8"><i class="fa-solid fa-paper-plane"></i>বার্তা পাঠান</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
