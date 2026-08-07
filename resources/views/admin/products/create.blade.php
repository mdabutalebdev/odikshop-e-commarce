<x-admin-layout title="নতুন পণ্য">
    <a href="{{ route('admin.products.index') }}" class="text-sm text-muted hover:text-brand mb-4 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i>পণ্য তালিকায় ফিরুন</a>
    @include('admin.products._form')
</x-admin-layout>
