<x-layout title="Login">
    <div class="container-x py-8 max-w-md mx-auto">
        <div class="text-center mb-6">
            <img src="{{ asset('images/logo-header.png') }}" alt="ODHIK SHOP" width="400" height="171" class="h-11 w-auto object-contain mx-auto mb-3">
            <h1 class="text-xl font-extrabold">Login to your account</h1>
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
                        <label class="block text-sm font-medium mb-1">Email or Phone <span class="text-sale">*</span></label>
                        <input type="text" name="email" value="{{ old('email') }}" required autofocus class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Password <span class="text-sale">*</span></label>
                        <input type="password" name="password" required class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                    </div>
                    <label class="flex items-center gap-2 text-sm text-muted"><input type="checkbox" name="remember" class="accent-brand">Remember me</label>
                    <button type="submit" class="btn-brand w-full h-11">Login</button>
                </form>

                <div class="mt-4 text-center text-sm text-muted">
                    Don't have an account? <a href="{{ route('register.form') }}" class="text-brand font-semibold hover:underline">Register</a>
                </div>
            </div>
        </div>
    </div>
</x-layout>
