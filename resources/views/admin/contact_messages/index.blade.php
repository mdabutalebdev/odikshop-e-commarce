<x-admin-layout title="Contact Messages">
    {{-- Inline CSS: the compiled Tailwind bundle is fixed at build time, so this view
         cannot rely on utility classes that were not already in use somewhere. --}}
    <style>
        .cm-btn { width: 2rem; height: 2rem; display: grid; place-items: center; border: 0; border-radius: 8px; background: transparent; cursor: pointer; transition: background .15s ease; }
        .cm-eye { color: var(--color-brand); }
        .cm-eye:hover { background: var(--color-brand-light); }
        .cm-del { color: var(--color-sale); }
        .cm-del:hover { background: #fee2e2; }

        .cm-preview { max-width: 22rem; color: var(--color-ink); opacity: .8; font-size: .75rem; }

        .cm-backdrop { position: fixed; inset: 0; z-index: 60; background: rgba(15, 23, 42, .55); display: grid; place-items: center; padding: 1rem; }
        .cm-modal { background: #fff; width: 100%; max-width: 34rem; border-radius: 16px; overflow: hidden; box-shadow: 0 24px 60px rgba(0, 0, 0, .28); display: flex; flex-direction: column; max-height: 85vh; }
        .cm-head { background: var(--color-brand); color: #fff; padding: 1rem 1.25rem; display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; }
        .cm-head h3 { margin: 0; font-size: 1.05rem; font-weight: 800; }
        .cm-head p { margin: .15rem 0 0; font-size: .75rem; opacity: .9; }
        .cm-close { width: 2rem; height: 2rem; flex: 0 0 auto; border: 0; border-radius: 9999px; background: rgba(255, 255, 255, .18); color: #fff; cursor: pointer; display: grid; place-items: center; transition: background .15s ease; }
        .cm-close:hover { background: rgba(255, 255, 255, .32); }

        .cm-body { padding: 1.1rem 1.25rem; overflow-y: auto; }
        .cm-field { display: flex; gap: .75rem; align-items: flex-start; padding: .7rem 0; border-top: 1px solid var(--color-line); }
        .cm-field:first-child { border-top: 0; padding-top: 0; }
        .cm-ico { width: 2rem; height: 2rem; flex: 0 0 auto; border-radius: 9999px; background: var(--color-brand-light); color: var(--color-brand); display: grid; place-items: center; font-size: .8rem; }
        .cm-key { font-size: .7rem; color: var(--color-muted); }
        .cm-value { font-weight: 600; color: var(--color-ink); word-break: break-word; }
        .cm-value a { color: inherit; text-decoration: none; }
        .cm-value a:hover { color: var(--color-brand); }
        .cm-message { white-space: pre-wrap; font-weight: 400; line-height: 1.6; }

        .cm-foot { padding: .9rem 1.25rem; border-top: 1px solid var(--color-line); display: flex; gap: .5rem; justify-content: flex-end; flex-wrap: wrap; }
        .cm-action { height: 2.5rem; padding: 0 1rem; border-radius: 8px; border: 1px solid var(--color-line); background: #fff; color: var(--color-ink); font-size: .8125rem; font-weight: 600; display: inline-flex; align-items: center; gap: .4rem; text-decoration: none; cursor: pointer; }
        .cm-action:hover { border-color: var(--color-brand); color: var(--color-brand); }
    </style>

    <div x-data="{ open: false, m: {} }" @keydown.escape.window="open = false">
        <div class="flex items-center justify-between mb-4 gap-3">
            <h1 class="text-xl font-bold">Contact Messages</h1>
        </div>

        <div class="bg-white rounded-xl border border-line overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-canvas text-muted text-left">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Name</th>
                            <th class="px-4 py-3 font-semibold">Contact Info</th>
                            <th class="px-4 py-3 font-semibold">Message</th>
                            <th class="px-4 py-3 font-semibold">Date</th>
                            <th class="px-4 py-3 font-semibold text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse($messages as $message)
                            <tr class="hover:bg-canvas">
                                <td class="px-4 py-3 font-semibold">{{ $message->name }}</td>
                                <td class="px-4 py-3">
                                    @if($message->email)<div><i class="fa-solid fa-envelope mr-1 text-muted"></i>{{ $message->email }}</div>@endif
                                    @if($message->phone)<div><i class="fa-solid fa-phone mr-1 text-muted"></i>{{ $message->phone }}</div>@endif
                                </td>
                                <td class="px-4 py-3">
                                    {{-- Truncated here; the eye button opens the full text. --}}
                                    <div class="cm-preview">{{ \Illuminate\Support\Str::limit($message->message, 70) }}</div>
                                </td>
                                <td class="px-4 py-3 text-muted text-xs">{{ $message->created_at->format('d M, Y h:i A') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" class="cm-btn cm-eye" title="View details"
                                                @click="m = {{ \Illuminate\Support\Js::from([
                                                    'name' => $message->name,
                                                    'email' => $message->email,
                                                    'phone' => $message->phone,
                                                    'message' => $message->message,
                                                    'date' => $message->created_at->format('d M, Y h:i A'),
                                                ]) }}; open = true">
                                            <i class="fa-regular fa-eye"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin.contact_messages.destroy', $message) }}" onsubmit="return confirm('Are you sure you want to delete this message?')">
                                            @csrf @method('DELETE')
                                            <button class="cm-btn cm-del" title="Delete"><i class="fa-solid fa-trash-can"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-10 text-center text-muted">No messages found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $messages->links() }}</div>

        {{-- Details modal --}}
        <div x-show="open" x-cloak class="cm-backdrop" @click.self="open = false">
            <div class="cm-modal" x-transition.opacity>
                <div class="cm-head">
                    <div>
                        <h3 x-text="m.name"></h3>
                        <p x-text="m.date"></p>
                    </div>
                    <button type="button" class="cm-close" @click="open = false" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <div class="cm-body">
                    <div class="cm-field" x-show="m.email">
                        <span class="cm-ico"><i class="fa-solid fa-envelope"></i></span>
                        <div>
                            <div class="cm-key">Email</div>
                            <div class="cm-value"><a :href="'mailto:' + m.email" x-text="m.email"></a></div>
                        </div>
                    </div>

                    <div class="cm-field" x-show="m.phone">
                        <span class="cm-ico"><i class="fa-solid fa-phone"></i></span>
                        <div>
                            <div class="cm-key">Phone</div>
                            <div class="cm-value"><a :href="'tel:' + m.phone" x-text="m.phone"></a></div>
                        </div>
                    </div>

                    <div class="cm-field">
                        <span class="cm-ico"><i class="fa-regular fa-comment-dots"></i></span>
                        <div>
                            <div class="cm-key">Message</div>
                            <div class="cm-value cm-message" x-text="m.message"></div>
                        </div>
                    </div>
                </div>

                <div class="cm-foot">
                    <a class="cm-action" x-show="m.email" :href="'mailto:' + m.email"><i class="fa-solid fa-reply"></i>Reply by Email</a>
                    <a class="cm-action" x-show="m.phone" :href="'tel:' + m.phone"><i class="fa-solid fa-phone"></i>Call</a>
                    <button type="button" class="cm-action" @click="open = false">Close</button>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
