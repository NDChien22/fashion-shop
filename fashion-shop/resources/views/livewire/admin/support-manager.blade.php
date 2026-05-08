<div class="space-y-6" data-support-admin-root>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-5">
            <p class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-bold">Chưa trả lời</p>
            <h3 class="mt-2 text-2xl font-black text-red-500">{{ number_format($summary['unread_conversations']) }}</h3>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-5 md:col-span-3">
            <p class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-bold">Tin nhắn chưa đọc</p>
            <h3 class="mt-2 text-2xl font-black text-amber-600">{{ number_format($summary['unread']) }}</h3>
        </div>
    </div>

    <div
        class="grid grid-cols-1 xl:grid-cols-[380px_minmax(0,1fr)] gap-6 xl:h-[calc(100vh-230px)] min-h-136 xl:min-h-170">
        <div @class([
            'bg-white rounded-3xl shadow-sm border border-gray-50 flex flex-col overflow-hidden min-h-0 xl:h-full',
            'hidden xl:flex' => $selectedConversationId !== null,
        ])>
            <div class="p-5 border-b border-gray-50 space-y-4">
                <div>
                    <h2 class="text-xs font-black uppercase tracking-[0.2em] text-gray-800">Danh sách hội thoại</h2>
                    <p class="text-[10px] text-gray-400 mt-1">Tất cả tin nhắn user và admin theo thời gian thực.</p>
                </div>

                <div class="space-y-3">
                    <div class="relative">
                        <i
                            class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px]"></i>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Tìm theo tên, email, nội dung..."
                            class="w-full rounded-2xl border border-gray-200 pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:border-[#bc9c75]">
                    </div>

                </div>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar divide-y divide-gray-50">
                @forelse ($conversations as $conversation)
                    @php
                        $customer = $conversation->user;
                        $displayName =
                            $customer?->full_name ?:
                            $customer?->username ?:
                            $conversation->contact_name ?:
                            'Khách hàng';
                        $displayEmail = $customer?->email ?: $conversation->contact_email ?: 'Chưa có email';
                        $hasUnread = (int) $conversation->unread_count > 0;
                    @endphp

                    <button type="button" wire:click="selectConversation({{ $conversation->id }})"
                        class="w-full text-left p-4 transition {{ $selectedConversationId === $conversation->id ? 'bg-[#fcfaf8] border-l-4 border-[#bc9c75]' : 'hover:bg-gray-50' }}">
                        <div class="flex items-start gap-3">
                            <div
                                class="w-11 h-11 rounded-2xl bg-[#bc9c75]/10 flex items-center justify-center text-[#bc9c75] font-black text-xs shrink-0">
                                {{ \Illuminate\Support\Str::of($displayName)->explode(' ')->filter()->take(2)->map(fn($part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))->implode('') ?: 'KH' }}
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3
                                            class="text-[12px] font-black uppercase tracking-tight text-gray-800 truncate">
                                            {{ $displayName }}</h3>
                                        <p class="text-[10px] text-gray-400 truncate">{{ $displayEmail }}</p>
                                    </div>

                                    @if ($hasUnread)
                                        <span
                                            class="shrink-0 inline-flex items-center gap-1 rounded-full bg-red-500 px-2.5 py-1 text-[10px] font-black text-white">
                                            <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                                            Chưa trả lời
                                        </span>
                                    @endif
                                </div>

                                <div class="mt-2 flex items-center justify-between gap-3">
                                    <p class="text-[11px] text-gray-500 truncate">
                                        {{ $conversation->latestMessage?->message ?: 'Chưa có tin nhắn.' }}
                                    </p>
                                    @if ((int) $conversation->unread_count > 0)
                                        <span
                                            class="px-2 py-0.5 rounded-full bg-red-500 text-white text-[10px] font-black">
                                            {{ (int) $conversation->unread_count }}
                                        </span>
                                    @endif
                                </div>

                                <div class="mt-2 text-[10px] text-gray-400 flex items-center justify-between">
                                    <span>{{ $conversation->subject }}</span>
                                    <span>{{ $conversation->last_message_at?->format('d/m H:i') ?? $conversation->created_at?->format('d/m H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="p-6 text-center text-gray-400 text-sm">Chưa có cuộc trò chuyện nào.</div>
                @endforelse
            </div>

            <div class="px-5 py-4 border-t border-gray-50">
                {{ $conversations->links() }}
            </div>
        </div>

        <div @class([
            'bg-white rounded-3xl shadow-sm border border-gray-50 flex-col overflow-hidden min-h-0 h-[calc(100vh-10.5rem)] xl:h-full',
            'flex' => $selectedConversationId !== null,
            'hidden xl:flex' => $selectedConversationId === null,
        ])>
            @if ($selectedConversation)
                @php
                    $customer = $selectedConversation->user;
                    $customerName =
                        $customer?->full_name ?:
                        $customer?->username ?:
                        $selectedConversation->contact_name ?:
                        'Khách hàng';
                    $customerEmail = $customer?->email ?: $selectedConversation->contact_email ?: 'Chưa có email';
                    $statusClass = match ($selectedConversation->status) {
                        'open' => 'bg-emerald-50 text-emerald-600',
                        'pending' => 'bg-amber-50 text-amber-600',
                        'closed' => 'bg-slate-100 text-slate-600',
                        default => 'bg-slate-100 text-slate-600',
                    };
                @endphp

                <div class="p-5 border-b border-gray-50 bg-[#fcfaf8]/40 flex items-start justify-between gap-4">
                    <div>
                        <button type="button" wire:click="$set('selectedConversationId', null)"
                            class="xl:hidden mb-3 inline-flex items-center gap-2 rounded-xl border border-gray-200 px-3 py-1.5 text-[11px] font-black uppercase tracking-[0.12em] text-gray-600 hover:border-[#bc9c75] hover:text-[#bc9c75] transition">
                            <i class="fa-solid fa-arrow-left text-[10px]"></i>
                            Danh sách
                        </button>
                        <h2 class="text-lg font-black text-gray-800 uppercase tracking-tight">{{ $customerName }}</h2>
                        <p class="text-xs text-gray-400 mt-1">{{ $customerEmail }} ·
                            {{ $selectedConversation->subject }}</p>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap justify-end">
                        @if ((int) $selectedConversation->unread_count > 0)
                            <span
                                class="inline-flex items-center gap-1 rounded-full bg-red-500 px-3 py-1.5 text-[10px] font-black uppercase text-white">
                                <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                                Chưa trả lời
                            </span>
                        @endif
                    </div>
                </div>

                <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar p-5 sm:p-6 space-y-4 bg-[#fcfaf8]/30"
                    data-admin-support-messages data-admin-conversation-id="{{ $selectedConversationId }}">
                    @forelse ($messages as $message)
                        <div class="flex {{ $message['sender_role'] === 'admin' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[85%] lg:max-w-[70%]">
                                <div
                                    class="mb-1 text-[10px] font-bold uppercase tracking-[0.18em] text-gray-400 {{ $message['sender_role'] === 'admin' ? 'text-right' : '' }}">
                                    {{ $message['sender_role'] === 'admin' ? 'Admin' : ($message['sender_name'] ?: 'Khách hàng') }}
                                </div>
                                <div
                                    class="rounded-3xl px-4 py-3 text-[13px] leading-relaxed shadow-sm {{ $message['sender_role'] === 'admin' ? 'bg-[#bc9c75] text-white rounded-br-none' : 'bg-white text-gray-700 border border-gray-100 rounded-bl-none' }}">
                                    {{ $message['message'] }}
                                </div>
                                <div
                                    class="mt-1 text-[10px] text-gray-400 {{ $message['sender_role'] === 'admin' ? 'text-right' : '' }}">
                                    {{ $message['created_at']?->format('H:i d/m/Y') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex items-center justify-center text-gray-400 text-sm">
                            Cuộc trò chuyện này chưa có nội dung.
                        </div>
                    @endforelse
                </div>

                <div class="p-5 border-t border-gray-50 bg-white">
                    <div class="space-y-3">
                        <textarea wire:model.live="replyMessage" wire:keydown.ctrl.enter.prevent="sendReply" rows="4"
                            placeholder="Nhập phản hồi cho khách hàng..."
                            class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#bc9c75] resize-none"></textarea>

                        <div class="flex items-center justify-between gap-3 flex-wrap">
                            <p class="text-[11px] text-gray-400">Phản hồi sẽ được gửi realtime qua Reverb.</p>
                            <button type="button" wire:click="sendReply" wire:loading.attr="disabled"
                                wire:target="sendReply"
                                class="inline-flex items-center gap-2 rounded-2xl bg-[#bc9c75] text-white px-5 py-3 text-xs font-black uppercase tracking-[0.18em] shadow-md shadow-[#bc9c75]/20 hover:opacity-90 transition">
                                <i class="fa-solid fa-paper-plane text-[10px]"></i>
                                <span wire:loading.remove wire:target="sendReply">Gửi phản hồi</span>
                                <span wire:loading wire:target="sendReply">Đang gửi...</span>
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex-1 flex items-center justify-center p-8 text-center text-gray-400">
                    Chọn một cuộc trò chuyện để xem nội dung.
                </div>
            @endif
        </div>
    </div>

    <script>
        (() => {
            const root = document.currentScript?.closest('[wire\\:id]');

            if (!root || root.dataset.adminSupportAutoScrollBound === '1') {
                return;
            }

            root.dataset.adminSupportAutoScrollBound = '1';

            const getMessagesBox = () => root.querySelector('[data-admin-support-messages]');

            const isNearBottom = (box) => {
                const remain = box.scrollHeight - box.scrollTop - box.clientHeight;

                return remain <= 80;
            };

            const scrollToBottom = () => {
                const messagesBox = getMessagesBox();

                if (!messagesBox) {
                    return;
                }

                messagesBox.scrollTop = messagesBox.scrollHeight;
            };

            const observeMessages = () => {
                const messagesBox = getMessagesBox();

                if (!messagesBox || messagesBox.dataset.adminSupportObserved === '1') {
                    return;
                }

                messagesBox.dataset.adminSupportObserved = '1';
                let shouldStickToBottom = true;

                messagesBox.addEventListener('scroll', () => {
                    shouldStickToBottom = isNearBottom(messagesBox);
                }, {
                    passive: true
                });

                const observer = new MutationObserver(() => {
                    if (!shouldStickToBottom) {
                        return;
                    }

                    requestAnimationFrame(scrollToBottom);
                });

                observer.observe(messagesBox, {
                    childList: true,
                    subtree: true,
                    characterData: true,
                });
            };

            const rebind = () => {
                observeMessages();

                const messagesBox = getMessagesBox();
                if (messagesBox && (messagesBox.dataset.initialScrollDone ?? '0') !== '1') {
                    messagesBox.dataset.initialScrollDone = '1';

                    requestAnimationFrame(() => {
                        requestAnimationFrame(scrollToBottom);
                    });
                }
            };

            rebind();

            if (window.Livewire?.hook) {
                window.Livewire.hook('morph.updated', ({
                    el
                }) => {
                    if (!root.contains(el)) {
                        return;
                    }

                    rebind();
                });
            }
        })();
    </script>
</div>
