<div class="fixed bottom-6 right-6 z-50" wire:key="customer-support-widget">
    <button id="back-to-top"
        class="fixed bottom-24 right-6 w-12 h-12 bg-white text-[#bc9c75] rounded-xl flex items-center justify-center shadow-lg border border-red-50 z-40 transition-all duration-300 opacity-0 invisible translate-y-10 hover:bg-red-50">
        <i class="ri-arrow-up-line text-2xl font-bold"></i>
    </button>

    <button type="button" wire:click="toggleWidget"
        class="w-14 h-14 bg-[#bc9c75] rounded-full flex items-center justify-center shadow-lg transition-transform hover:scale-110 active:scale-95 relative">
        <i class="ri-messenger-fill text-white text-3xl"></i>
        <span class="absolute top-0 right-0 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></span>
    </button>

    <div data-support-panel wire:poll.1s="pollRefresh" @class([
        'absolute bottom-16 right-0 w-80 md:w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden flex-col transition-all duration-300',
        'hidden' => !$isOpen,
        'flex' => $isOpen,
    ])>
        <div class="bg-[#bc9c75] p-4 flex justify-between items-center text-white">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <img src="https://ui-avatars.com/api/?name=Support&background=fff&color=bc9c75"
                        class="w-10 h-10 rounded-full border-2 border-white/50" alt="Avatar">
                    <span
                        class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-[#bc9c75] rounded-full"></span>
                </div>
                <div>
                    <p class="font-bold text-sm">Hỗ trợ Fast Fashion</p>
                    <p class="text-[10px] opacity-80">Thường trả lời trong vài phút</p>
                </div>
            </div>

            <button type="button" wire:click="closeWidget" class="hover:bg-white/20 rounded-full p-1">
                <i class="ri-close-line text-xl"></i>
            </button>
        </div>

        @auth
            <div class="h-80 overflow-y-auto p-4 bg-gray-50 flex flex-col gap-3" id="message-container"
                data-support-messages data-support-chat-conversation="{{ $conversationId }}">
                @forelse ($messages as $message)
                    <div
                        class="flex flex-col gap-1 max-w-[85%] {{ $message['sender_role'] === 'customer' ? 'self-end items-end' : '' }}">
                        <div
                            class="{{ $message['sender_role'] === 'customer' ? 'bg-[#bc9c75] text-white rounded-tr-none' : 'bg-white text-gray-700 border border-gray-100 rounded-tl-none' }} p-3 rounded-2xl shadow-sm">
                            <p class="text-sm leading-relaxed">{{ $message['message'] }}</p>
                        </div>
                        <span
                            class="text-[9px] text-gray-400 {{ $message['sender_role'] === 'customer' ? 'mr-1' : 'ml-1' }}">
                            {{ $message['created_at']?->format('H:i d/m') }}
                        </span>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-4 text-sm text-gray-500">
                        Chào bạn! Fast Fashion có thể giúp gì cho bạn hôm nay không ạ?
                    </div>
                @endforelse
            </div>

            <div class="p-3 border-t bg-white flex items-center gap-2">
                <div
                    class="flex-1 bg-gray-100 rounded-full px-4 py-2 flex items-center gap-2 border border-transparent focus-within:border-[#bc9c75]/30 focus-within:bg-white transition-all">
                    <input type="text" wire:model.live.debounce.300ms="message" wire:keydown.enter="sendMessage"
                        placeholder="Nhập tin nhắn..."
                        class="w-full bg-transparent border-none outline-none text-sm p-0 focus:ring-0">
                </div>
                <button type="button" wire:click="sendMessage"
                    class="w-10 h-10 bg-[#bc9c75] text-white rounded-full flex items-center justify-center hover:bg-[#a0805a] transition">
                    <i class="ri-send-plane-2-fill text-sm"></i>
                </button>
            </div>
        @else
            <div class="p-4 bg-gray-50 text-sm text-gray-600 space-y-3">
                <p>Đăng nhập để trò chuyện trực tiếp với đội hỗ trợ và nhận phản hồi realtime qua Reverb.</p>
                <a href="{{ route('login') }}"
                    class="inline-flex items-center justify-center rounded-xl bg-[#bc9c75] px-4 py-2 text-white text-xs font-bold uppercase tracking-[0.18em] hover:opacity-90 transition">
                    Đăng nhập
                </a>
            </div>
        @endauth
    </div>

    <script>
        (() => {
            const root = document.currentScript?.closest('[wire\\:id]');

            if (!root || root.dataset.supportAutoScrollBound === '1') {
                return;
            }

            root.dataset.supportAutoScrollBound = '1';

            const scrollToBottom = () => {
                const messagesBox = root.querySelector('[data-support-messages]');

                if (!messagesBox) {
                    return;
                }

                messagesBox.scrollTop = messagesBox.scrollHeight;
            };

            const observeMessages = () => {
                const messagesBox = root.querySelector('[data-support-messages]');

                if (!messagesBox || messagesBox.dataset.supportObserved === '1') {
                    return;
                }

                messagesBox.dataset.supportObserved = '1';

                const observer = new MutationObserver(() => {
                    requestAnimationFrame(scrollToBottom);
                });

                observer.observe(messagesBox, {
                    childList: true,
                    subtree: true,
                    characterData: true,
                });
            };

            const observePanelState = () => {
                const panel = root.querySelector('[data-support-panel]');

                if (!panel || panel.dataset.supportPanelObserved === '1') {
                    return;
                }

                panel.dataset.supportPanelObserved = '1';

                const panelObserver = new MutationObserver(() => {
                    if (!panel.classList.contains('hidden')) {
                        requestAnimationFrame(() => {
                            requestAnimationFrame(scrollToBottom);
                        });
                    }
                });

                panelObserver.observe(panel, {
                    attributes: true,
                    attributeFilter: ['class'],
                });
            };

            const rebind = () => {
                observeMessages();
                observePanelState();
                requestAnimationFrame(scrollToBottom);
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
