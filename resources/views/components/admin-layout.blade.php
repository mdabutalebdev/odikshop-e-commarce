@props(['title' => 'Dashboard'])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - Admin | ODHIK SHOP</title>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Admin uses standalone Alpine (no Livewire here) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-canvas min-h-screen" x-data="{ sidebar: false }">

    @php
        $navItems = [
            ['route' => 'admin.dashboard', 'icon' => 'fa-gauge-high', 'label' => 'Dashboard', 'active' => 'admin.dashboard'],
            ['route' => 'admin.products.index', 'icon' => 'fa-box', 'label' => 'Products', 'active' => 'admin.products.*'],
            ['route' => 'admin.categories.index', 'icon' => 'fa-layer-group', 'label' => 'Categories', 'active' => 'admin.categories.*'],
            ['route' => 'admin.banners.index', 'icon' => 'fa-image', 'label' => 'Banners', 'active' => 'admin.banners.*'],
            ['route' => 'admin.orders.index', 'icon' => 'fa-bag-shopping', 'label' => 'Orders', 'active' => 'admin.orders.*'],
            ['route' => 'admin.reviews.index', 'icon' => 'fa-star', 'label' => 'Reviews', 'active' => 'admin.reviews.*'],
            ['route' => 'admin.settings.edit', 'icon' => 'fa-gear', 'label' => 'Settings', 'active' => 'admin.settings.*'],
        ];
    @endphp

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-brand-dark text-white flex flex-col transition-transform lg:translate-x-0"
           :class="sidebar ? 'translate-x-0' : '-translate-x-full'">
        <div class="h-16 flex items-center gap-2 px-4 border-b border-white/10">
            <span class="bg-white rounded-lg px-2 py-1.5 inline-flex">
                <img src="{{ asset('images/logo-header.png') }}" alt="ODHIK SHOP" width="400" height="171" class="h-6 w-auto object-contain">
            </span>
            <span class="text-white/70 font-medium text-sm">Admin</span>
        </div>
        <nav class="flex-1 overflow-y-auto p-3 space-y-1">
            @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs($item['active']) ? 'bg-white text-brand' : 'text-white/80 hover:bg-white/10' }}">
                    <i class="fa-solid {{ $item['icon'] }} w-5 text-center"></i>{{ $item['label'] }}
                </a>
            @endforeach
        </nav>
        <div class="p-3 border-t border-white/10">
            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-white/80 hover:bg-white/10"><i class="fa-solid fa-arrow-up-right-from-square w-5 text-center"></i>সাইট দেখুন</a>
        </div>
    </aside>

    {{-- Backdrop (mobile) --}}
    <div x-show="sidebar" x-cloak @click="sidebar=false" class="fixed inset-0 bg-black/50 z-30 lg:hidden"></div>

    {{-- Main --}}
    <div class="lg:ml-64">
        <header class="h-16 bg-white border-b border-line sticky top-0 z-20 flex items-center gap-3 px-4 lg:px-6">
            <button @click="sidebar=true" class="lg:hidden text-xl"><i class="fa-solid fa-bars"></i></button>
            <h1 class="font-bold text-lg">{{ $title }}</h1>
            <div class="ml-auto flex items-center gap-3">
                <span class="text-sm text-muted hidden sm:block">{{ auth()->user()->name }}</span>
                <span class="w-9 h-9 rounded-full bg-brand text-white grid place-items-center font-bold">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="h-9 px-3 rounded-lg border border-line text-sm hover:bg-canvas"><i class="fa-solid fa-right-from-bracket"></i></button>
                </form>
            </div>
        </header>

        <main class="p-4 lg:p-6">
            @if(session('success'))
                <div class="bg-brand-light border border-brand/20 text-brand rounded-lg px-4 py-3 text-sm mb-4 flex items-center gap-2"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-sale rounded-lg px-4 py-3 text-sm mb-4">
                    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    @stack('scripts')
</body>
</html>
