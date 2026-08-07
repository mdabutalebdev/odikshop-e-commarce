<x-admin-layout title="ক্যাটাগরি সম্পাদনা">
    <a href="{{ route('admin.categories.index') }}" class="text-sm text-muted hover:text-brand mb-4 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i>ফিরে যান</a>
    @include('admin.categories._form')
</x-admin-layout>
