{{-- Customer reviews — horizontally sliding cards (expects $testimonials) --}}
@if($testimonials->isNotEmpty())
    <section data-row class="container-x mt-12">
        <div class="flex items-end justify-between mb-5">
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold">What Our Customers Say</h2>
                <p class="text-muted text-sm mt-1">Real reviews from happy shoppers across Bangladesh</p>
            </div>
            <div class="hidden sm:flex items-center gap-2">
                <button type="button" data-row-prev class="w-9 h-9 rounded-full border border-line bg-white grid place-items-center hover:bg-brand hover:text-white hover:border-brand transition"><i class="fa-solid fa-chevron-left text-xs"></i></button>
                <button type="button" data-row-next class="w-9 h-9 rounded-full border border-line bg-white grid place-items-center hover:bg-brand hover:text-white hover:border-brand transition"><i class="fa-solid fa-chevron-right text-xs"></i></button>
            </div>
        </div>
        <div data-row-scroll class="flex gap-4 overflow-x-auto no-scrollbar snap-x pb-2">
            @foreach($testimonials as $t)
                <div class="w-80 max-w-[85%] shrink-0 snap-start bg-white rounded-xl border border-line shadow-soft p-5 flex flex-col">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex text-amber-400 text-sm">
                            @for($s = 1; $s <= 5; $s++)<i class="fa-solid fa-star {{ $s <= $t->rating ? '' : 'text-gray-300' }}"></i>@endfor
                        </div>
                        <i class="fa-solid fa-quote-right text-2xl text-brand-light"></i>
                    </div>
                    <p class="text-sm text-ink/80 leading-relaxed mb-4 flex-1">“{{ $t->text }}”</p>
                    <div class="flex items-center gap-3 pt-3 border-t border-line">
                        <span class="w-10 h-10 rounded-full bg-brand text-white grid place-items-center font-bold shrink-0">{{ mb_substr($t->name, 0, 1) }}</span>
                        <div class="min-w-0">
                            <div class="font-semibold text-sm truncate">{{ $t->name }}</div>
                            <div class="text-xs text-muted">{{ $t->location }}</div>
                        </div>
                        <span class="ml-auto text-brand text-xs font-semibold flex items-center gap-1"><i class="fa-solid fa-circle-check"></i>Verified</span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif
