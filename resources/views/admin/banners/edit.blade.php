<x-admin-layout title="ব্যানার সম্পাদনা">
    <a href="{{ route('admin.banners.index') }}" class="text-sm text-muted hover:text-brand mb-4 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i>ফিরে যান</a>
    @include('admin.banners._form')
</x-admin-layout>
