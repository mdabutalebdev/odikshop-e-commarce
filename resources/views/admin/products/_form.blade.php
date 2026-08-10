@php
    $editing = $product->exists;
    $existingVariants = old('variants_json') ?: json_encode($product->attributes ?? [], JSON_UNESCAPED_UNICODE);
@endphp

<form method="POST" action="{{ $editing ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data" class="grid lg:grid-cols-[1fr_320px] gap-6">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="space-y-5">
        {{-- Basic info --}}
        <div class="bg-white rounded-xl border border-line p-5 space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">পণ্যের নাম <span class="text-sale">*</span></label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">সংক্ষিপ্ত বিবরণ</label>
                <input type="text" name="short_description" value="{{ old('short_description', $product->short_description) }}" maxlength="500" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30" placeholder="১-২ লাইন">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">বিস্তারিত বিবরণ</label>
                <textarea name="description" id="product-description" class="rich-editor w-full rounded-lg border border-line px-3 py-2 text-sm">{{ old('description', $product->description) }}</textarea>
                <p class="text-xs text-muted mt-1">টেক্সট ফরম্যাট, ছবি, এবং YouTube ভিডিও লিংক যোগ করুন (Insert → Media)।</p>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">বিক্রয় মূল্য (BDT) <span class="text-sale">*</span></label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">পুরাতন মূল্য (কাটা দাম)</label>
                    <input type="number" step="0.01" name="old_price" value="{{ old('old_price', $product->old_price) }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">স্টক <span class="text-sale">*</span></label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">রেটিং (০-৫)</label>
                    <input type="number" step="0.1" min="0" max="5" name="rating" value="{{ old('rating', $product->rating ?? 0) }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">রিভিউ সংখ্যা</label>
                    <input type="number" name="reviews_count" value="{{ old('reviews_count', $product->reviews_count ?? 0) }}" class="w-full h-11 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                </div>
            </div>
        </div>

        {{-- Product variants --}}
        <div class="bg-white rounded-xl border border-line p-5"
             x-data="variantEditor({{ $existingVariants ? json_encode($existingVariants) : '\'[]\'' }})">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h3 class="font-semibold text-sm">পণ্যের ভেরিয়েন্ট</h3>
                    <p class="text-xs text-muted">যেমন: Size (S, M, L) বা Color (Red, Blue) — কাস্টমার এখান থেকে বেছে নিতে পারবে।</p>
                </div>
                <button type="button" @click="addGroup" class="btn-brand h-9 px-3 text-xs"><i class="fa-solid fa-plus"></i>নতুন গ্রুপ</button>
            </div>

            <template x-for="(group, gIdx) in groups" :key="gIdx">
                <div class="border border-line rounded-lg p-3 mb-3 bg-canvas/40">
                    <div class="flex gap-2 items-center mb-2">
                        <input type="text" x-model="group.name" placeholder="গ্রুপের নাম (Size / Color)" class="flex-1 h-9 rounded-lg border border-line px-3 text-sm bg-white">
                        <button type="button" @click="removeGroup(gIdx)" class="w-9 h-9 grid place-items-center rounded-lg border border-line text-sale hover:bg-red-50" aria-label="Remove group"><i class="fa-solid fa-trash-can text-xs"></i></button>
                    </div>
                    <div class="space-y-2">
                        <template x-for="(opt, oIdx) in group.options" :key="oIdx">
                            <div class="flex gap-2 items-center">
                                <input type="text" x-model="group.options[oIdx]" placeholder="অপশন (যেমন: S / Red)" class="flex-1 h-8 rounded-lg border border-line px-2 text-xs bg-white">
                                <button type="button" @click="removeOption(gIdx, oIdx)" class="w-8 h-8 grid place-items-center rounded-lg border border-line text-sale hover:bg-red-50" aria-label="Remove option"><i class="fa-solid fa-xmark text-xs"></i></button>
                            </div>
                        </template>
                        <button type="button" @click="addOption(gIdx)" class="text-xs text-brand font-semibold hover:underline"><i class="fa-solid fa-plus"></i> অপশন যোগ করুন</button>
                    </div>
                </div>
            </template>

            <p x-show="!groups.length" class="text-center text-xs text-muted py-3">কোনো ভেরিয়েন্ট যোগ করা হয়নি।</p>

            <input type="hidden" name="attributes" :value="JSON.stringify(groups)">
        </div>
    </div>

    {{-- Right column --}}
    <div class="space-y-5">
        {{-- Main image --}}
        <div class="bg-white rounded-xl border border-line p-5">
            <label class="block text-sm font-medium mb-2">প্রধান ছবি (Cover)</label>
            @if($product->main_image)
                <img src="{{ image_url($product->main_image, $product->name) }}" class="w-full aspect-square object-cover rounded-lg border border-line mb-3 bg-canvas">
            @endif
            <input type="file" name="main_image" accept="image/*" class="w-full text-sm mb-2 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-brand-light file:text-brand file:font-semibold">
            <label class="block text-xs text-muted mb-1 mt-2">অথবা ছবির URL</label>
            <input type="text" name="image_url" placeholder="https://..." class="w-full h-10 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
        </div>

        {{-- Additional images (gallery — up to 8) --}}
        <div class="bg-white rounded-xl border border-line p-5">
            <label class="block text-sm font-medium mb-2">অতিরিক্ত ছবি (Gallery) — সর্বোচ্চ ৮টি</label>

            @if($editing && $product->images->isNotEmpty())
                <div class="grid grid-cols-3 gap-2 mb-3">
                    @foreach($product->images as $img)
                        <div class="relative group">
                            <img src="{{ image_url($img->path) }}" class="w-full aspect-square object-cover rounded-lg border border-line bg-canvas">
                            <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $img]) }}"
                                  class="absolute top-1 right-1"
                                  onsubmit="return confirm('এই ছবিটি মুছে ফেলতে চান?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-7 h-7 rounded-full bg-sale text-white grid place-items-center shadow hover:bg-red-600" aria-label="Delete">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif

            <input type="file" name="images[]" accept="image/*" multiple class="w-full text-sm file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-brand-light file:text-brand file:font-semibold">
            <p class="text-xs text-muted mt-1">একসাথে একাধিক ছবি নির্বাচন করুন (Ctrl / Cmd + click)।</p>
        </div>

        {{-- Category + flags --}}
        <div class="bg-white rounded-xl border border-line p-5 space-y-3">
            <div>
                <label class="block text-sm font-medium mb-1">ক্যাটাগরি</label>
                <select name="category_id" class="w-full h-11 rounded-lg border border-line px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand/30">
                    <option value="">— নির্বাচন করুন —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id) == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <p class="text-xs font-semibold text-muted uppercase tracking-wide pt-1">Show on home in</p>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_flash_sale" value="1" @checked(old('is_flash_sale', $product->is_flash_sale)) class="accent-brand"><span class="flex items-center gap-1">⚡ Flash Sale</span></label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured)) class="accent-brand"><span class="flex items-center gap-1">⭐ Featured</span></label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_best_seller" value="1" @checked(old('is_best_seller', $product->is_best_seller)) class="accent-brand"><span class="flex items-center gap-1">🔥 Best Selling</span></label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_new_arrival" value="1" @checked(old('is_new_arrival', $product->is_new_arrival)) class="accent-brand"><span class="flex items-center gap-1">🆕 New Arrival</span></label>
            <label class="flex items-center gap-2 text-sm border-t border-line pt-3 mt-1"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $editing ? $product->is_active : true)) class="accent-brand">Active (visible on site)</label>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="btn-brand h-11 flex-1">{{ $editing ? 'আপডেট করুন' : 'সেভ করুন' }}</button>
            <a href="{{ route('admin.products.index') }}" class="h-11 px-4 rounded-lg border border-line bg-white inline-flex items-center text-sm">বাতিল</a>
        </div>
    </div>
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
<script>
    // ===== Rich text editor (TinyMCE, self-hosted via jsDelivr — no API key) =====
    tinymce.init({
        selector: 'textarea.rich-editor',
        license_key: 'gpl',
        height: 420,
        menubar: false,
        branding: false,
        promotion: false,
        plugins: 'lists link image media table code autolink wordcount',
        toolbar: 'undo redo | blocks | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table | removeformat | code',
        media_live_embeds: true,
        media_url_resolver: function (data, resolve) {
            // Handle YouTube embeds explicitly so any link (share, watch?v=, youtu.be) works.
            const yt = data.url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_\-]{6,})/);
            if (yt) {
                const id = yt[1];
                const html = `<iframe width="640" height="360" src="https://www.youtube.com/embed/${id}" frameborder="0" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>`;
                resolve({ html });
                return;
            }
            resolve({ html: '' });
        },
        images_upload_url: '{{ route('admin.editor.image') }}',
        images_upload_handler: function (blobInfo, progress) {
            return new Promise(function (resolve, reject) {
                const fd = new FormData();
                fd.append('file', blobInfo.blob(), blobInfo.filename());
                fetch('{{ route('admin.editor.image') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: fd,
                }).then(r => r.ok ? r.json() : Promise.reject('Upload failed'))
                  .then(j => resolve(j.location))
                  .catch(err => reject(String(err)));
            });
        },
        content_style: 'body { font-family: "Plus Jakarta Sans","Hind Siliguri",sans-serif; font-size: 14px; }',
    });

    // ===== Variant editor (Alpine) =====
    function variantEditor(existingJson) {
        let parsed = [];
        try {
            const decoded = typeof existingJson === 'string' ? JSON.parse(existingJson) : existingJson;
            if (Array.isArray(decoded)) parsed = decoded;
        } catch (e) { parsed = []; }
        // Normalize each group to {name, options[]}
        parsed = parsed.filter(g => g && typeof g === 'object').map(g => ({
            name: g.name || '',
            options: Array.isArray(g.options) ? g.options : [],
        }));

        return {
            groups: parsed,
            addGroup() { this.groups.push({ name: '', options: [''] }); },
            removeGroup(i) { this.groups.splice(i, 1); },
            addOption(i) { this.groups[i].options.push(''); },
            removeOption(gi, oi) { this.groups[gi].options.splice(oi, 1); },
        };
    }
</script>
@endpush
