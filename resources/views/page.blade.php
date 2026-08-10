<x-layout :title="$page['title']">
    <div class="container-x py-8 max-w-3xl mx-auto">
        <nav class="text-xs text-muted mb-3">
            <a href="{{ route('home') }}" class="hover:text-brand">Home</a>
            <span class="mx-1">/</span>
            <span class="text-ink">{{ $page['title'] }}</span>
        </nav>

        <div class="bg-white rounded-xl border border-line p-6 sm:p-8">
            <h1 class="text-2xl font-extrabold mb-5 flex items-center gap-2">
                <span class="w-1.5 h-7 rounded-full bg-accent inline-block"></span>{{ $page['title'] }}
            </h1>
            <div class="prose-sm text-sm text-ink/80 leading-relaxed space-y-3 [&_ul]:space-y-1 [&_ol]:space-y-1 [&_strong]:text-ink">
                {!! $page['body'] !!}
            </div>

            <div class="mt-6 pt-5 border-t border-line text-sm text-muted">
                Still need help? Contact us at
                <a href="mailto:{{ $page['contact']['email'] }}" class="text-brand font-semibold">{{ $page['contact']['email'] }}</a>
                or <a href="tel:{{ $page['contact']['phone'] }}" class="text-brand font-semibold">{{ $page['contact']['phone'] }}</a>.
            </div>
        </div>
    </div>
</x-layout>
