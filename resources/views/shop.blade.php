<x-layout :title="$title">
    {{-- Page header --}}
    <section class="bg-white border-b border-line">
        <div class="container-x py-6">
            <nav class="text-xs text-muted mb-1">
                <a href="{{ route('home') }}" class="hover:text-brand">হোম</a>
                <span class="mx-1">/</span>
                <span class="text-ink">{{ $title }}</span>
            </nav>
            <h1 class="text-2xl font-extrabold">{{ $title }}</h1>
            <p class="text-muted text-sm mt-1">আপনার পছন্দের আসল ও খাঁটি প্রোডাক্টের সংগ্রহ</p>
        </div>
    </section>

    <div class="container-x py-6 grid lg:grid-cols-[240px_1fr] gap-6">
        {{-- Sidebar filters --}}
        <aside class="hidden lg:block">
            <div class="bg-white rounded-xl border border-line p-4 sticky top-32">
                <h3 class="font-bold mb-3 flex items-center gap-2"><i class="fa-solid fa-layer-group text-brand"></i>ক্যাটাগরি</h3>
                <ul class="space-y-1 text-sm">
                    <li>
                        <a href="{{ route('shop') }}" class="block px-3 py-2 rounded-lg {{ !$activeCategory ? 'bg-brand-light text-brand font-semibold' : 'hover:bg-canvas' }}">সব পণ্য</a>
                    </li>
                    @foreach($categories as $cat)
                        <li>
                            <a href="{{ route('shop', ['category' => $cat->slug]) }}" class="flex items-center gap-2 px-3 py-2 rounded-lg {{ $activeCategory?->id === $cat->id ? 'bg-brand-light text-brand font-semibold' : 'hover:bg-canvas' }}">
                                <i class="{{ $cat->icon ?? 'fa-solid fa-tag' }} w-4"></i>{{ $cat->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </aside>

        {{-- Products --}}
        <div>
            {{-- Toolbar --}}
            <div class="flex items-center justify-between mb-4 gap-3">
                <p class="text-sm text-muted">{{ $products->total() }} টি পণ্য</p>
                <form method="GET" class="flex items-center gap-2">
                    @foreach(request()->except(['sort','page']) as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach
                    <label class="text-sm text-muted hidden sm:block">সাজান:</label>
                    <select name="sort" onchange="this.form.submit()" class="h-10 rounded-lg border border-line bg-white px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                        <option value="" @selected(request('sort')==='')>ডিফল্ট</option>
                        <option value="newest" @selected(request('sort')==='newest')>নতুন</option>
                        <option value="price_low" @selected(request('sort')==='price_low')>দাম: কম থেকে বেশি</option>
                        <option value="price_high" @selected(request('sort')==='price_high')>দাম: বেশি থেকে কম</option>
                    </select>
                </form>
            </div>

            {{-- Mobile category chips --}}
            <div class="lg:hidden flex gap-2 overflow-x-auto no-scrollbar pb-3 mb-1">
                <a href="{{ route('shop') }}" class="shrink-0 px-4 h-9 grid place-items-center rounded-full text-sm border {{ !$activeCategory ? 'bg-brand text-white border-brand' : 'bg-white border-line' }}">সব</a>
                @foreach($categories as $cat)
                    <a href="{{ route('shop', ['category' => $cat->slug]) }}" class="shrink-0 px-4 h-9 grid place-items-center rounded-full text-sm border {{ $activeCategory?->id === $cat->id ? 'bg-brand text-white border-brand' : 'bg-white border-line' }}">{{ $cat->name }}</a>
                @endforeach
            </div>

            @if($products->isEmpty())
                <div class="bg-white rounded-xl border border-line p-12 text-center text-muted">
                    <i class="fa-solid fa-box-open text-5xl mb-3 opacity-30"></i>
                    <p>কোনো পণ্য পাওয়া যায়নি।</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    @foreach($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layout>
