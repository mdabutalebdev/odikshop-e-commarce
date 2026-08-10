<x-layout title="Register">
    <div class="container-x py-8 max-w-md mx-auto">
        <div class="text-center mb-6">
            <img src="{{ asset('images/logo-header.png') }}" alt="ODHIK SHOP" width="400" height="171" class="h-11 w-auto object-contain mx-auto mb-3">
            <h1 class="text-xl font-extrabold">Create a new account</h1>
        </div>

        <div class="bg-white rounded-xl border border-line overflow-hidden">
            <div class="p-5">
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-sale rounded-lg p-3 mb-4 text-sm">
                        @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium mb-1">Full Name <span class="text-sale">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Email <span class="text-sale">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Password <span class="text-sale">*</span></label>
                        <input type="password" name="password" required class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Confirm Password <span class="text-sale">*</span></label>
                        <input type="password" name="password_confirmation" required class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                    </div>
                    <button type="submit" class="btn-brand w-full h-11">Register</button>
                </form>

                <div class="mt-4 text-center text-sm text-muted">
                    Already have an account? <a href="{{ route('login') }}" class="text-brand font-semibold hover:underline">Login</a>
                </div>
            </div>
        </div>
    </div>
</x-layout>
