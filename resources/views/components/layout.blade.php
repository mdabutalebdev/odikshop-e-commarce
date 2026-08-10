@props(['title' => null])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title.' - ODHIK SHOP' : 'ODHIK SHOP | Online Shopping in Bangladesh' }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen flex flex-col antialiased"
      x-data="{ mobileMenu: false, cartOpen: false, searchOpen: false }"
      @open-cart.window="cartOpen = true">

    @include('partials.header')

    <main class="flex-1 pb-20 lg:pb-0">
        @if(session('success') || session('info') || session('error'))
            <div class="container-x pt-3">
                @if(session('success'))
                    <div class="bg-brand-light border border-brand/20 text-brand rounded-lg px-4 py-3 text-sm flex items-center gap-2"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</div>
                @endif
                @if(session('info'))
                    <div class="bg-blue-50 border border-blue-200 text-blue-700 rounded-lg px-4 py-3 text-sm flex items-center gap-2"><i class="fa-solid fa-circle-info"></i>{{ session('info') }}</div>
                @endif
                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-sale rounded-lg px-4 py-3 text-sm flex items-center gap-2"><i class="fa-solid fa-circle-exclamation"></i>{{ session('error') }}</div>
                @endif
            </div>
        @endif

        {{ $slot }}
    </main>

    @include('partials.footer')
    @include('partials.mobile-nav')
    @include('partials.cart-drawer')

    @livewireScripts
</body>
</html>
