<x-layout title="My Account">
    <div class="container-x py-6">
        <div class="flex items-center justify-between mb-5">
            <h1 class="text-2xl font-extrabold">My Account</h1>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="h-10 px-4 rounded-lg border border-line bg-white text-sm font-semibold hover:bg-canvas"><i class="fa-solid fa-right-from-bracket mr-1"></i>Logout</button>
            </form>
        </div>

        @if(session('success'))
            <div class="bg-brand-light border border-brand/20 text-brand rounded-lg p-3 mb-4 text-sm"><i class="fa-solid fa-circle-check mr-1"></i>{{ session('success') }}</div>
        @endif

        <div class="grid lg:grid-cols-[320px_1fr] gap-6">
            {{-- Profile --}}
            <div class="bg-white rounded-xl border border-line p-5 h-fit">
                <div class="flex items-center gap-3 mb-4">
                    <span class="w-14 h-14 rounded-full bg-brand text-white grid place-items-center text-xl font-bold">{{ mb_substr($user->name, 0, 1) }}</span>
                    <div class="min-w-0">
                        <div class="font-bold truncate">{{ $user->name }}</div>
                        <div class="text-xs text-muted truncate">{{ $user->email }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('account.update') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium mb-1 text-muted">Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full h-10 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1 text-muted">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full h-10 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1 text-muted">City</label>
                        <input type="text" name="city" value="{{ old('city', $user->city) }}" class="w-full h-10 rounded-lg border border-line px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1 text-muted">Address</label>
                        <textarea name="address" rows="2" class="w-full rounded-lg border border-line px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30">{{ old('address', $user->address) }}</textarea>
                    </div>
                    <button class="btn-brand w-full h-10 text-sm">Update Profile</button>
                </form>
            </div>

            {{-- Orders --}}
            <div class="bg-white rounded-xl border border-line p-5">
                <h2 class="font-bold mb-4 flex items-center gap-2"><i class="fa-solid fa-bag-shopping text-brand"></i>My Orders</h2>
                @if($orders->isEmpty())
                    <div class="text-center text-muted py-10">
                        <i class="fa-solid fa-box-open text-4xl mb-2 opacity-30"></i>
                        <p>You have no orders yet.</p>
                        <a href="{{ route('shop') }}" class="text-brand font-semibold text-sm mt-2 inline-block">Start Shopping →</a>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($orders as $order)
                            <div class="border border-line rounded-lg p-3 flex items-center justify-between flex-wrap gap-2">
                                <div>
                                    <div class="font-semibold text-sm">{{ $order->order_number }}</div>
                                    <div class="text-xs text-muted">{{ $order->created_at->format('d M Y') }} · {{ $order->items_count ?? $order->items->count() }} items</div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-brand-light text-brand">{{ \App\Models\Order::STATUS_LABELS[$order->status] ?? $order->status }}</span>
                                    <span class="font-bold text-brand">{{ bdt($order->total) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>
