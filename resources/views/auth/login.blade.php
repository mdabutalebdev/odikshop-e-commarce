<x-layout title="লগইন">
    <div class="container-x py-8 max-w-md mx-auto">
        <div class="text-center mb-6">
            <span class="grid place-items-center w-14 h-14 rounded-2xl bg-brand text-white font-extrabold text-2xl mx-auto mb-3">O</span>
            <h1 class="text-xl font-extrabold">আপনার অনলাইন শপ অ্যাকাউন্টে লগইন করুন</h1>
        </div>

        <div class="bg-white rounded-xl border border-line overflow-hidden">
            <div class="p-5">
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-sale rounded-lg p-3 mb-4 text-sm">
                        @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium mb-1">ইমেইল বা ফোন নাম্বার <span class="text-sale">*</span></label>
                        <input type="text" name="email" value="{{ old('email') }}" required class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">পাসওয়ার্ড <span class="text-sale">*</span></label>
                        <input type="password" name="password" required class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                    </div>
                    <label class="flex items-center gap-2 text-sm text-muted"><input type="checkbox" name="remember" class="accent-brand">মনে রাখুন</label>
                    <button type="submit" class="btn-brand w-full h-11">লগইন করুন</button>
                </form>

                <div class="mt-4 text-center text-sm">
                    অ্যাকাউন্ট নেই? <a href="{{ route('register.form') }}" class="text-brand font-medium hover:underline">রেজিস্টার করুন</a>
                </div>
            </div>
        </div>
    </div>
</x-layout>
