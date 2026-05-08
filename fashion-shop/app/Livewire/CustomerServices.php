<?php

namespace App\Livewire;

use App\Events\SupportMessageSent;
use App\Models\SupportConversation;
use App\Models\SupportMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CustomerServices extends Component
{
    public bool $isOpen = false;

    public string $message = '';

    public ?int $conversationId = null;

    public array $messages = [];

    public int $unreadCount = 0;

    public function getListeners(): array
    {
        if (! Auth::check()) {
            return [];
        }

        return [
            'echo-private:support.user.'.Auth::id().',SupportMessageSent' => 'handleRealtimeMessage',
        ];
    }

    public function handleRealtimeMessage(array $payload = []): void
    {
        $this->refreshConversation();
    }

    public function pollRefresh(): void
    {
        if (! $this->isOpen || ! Auth::check()) {
            return;
        }

        $this->refreshConversation();
    }

    public function toggleWidget(): void
    {
        $this->isOpen = ! $this->isOpen;

        if ($this->isOpen) {
            $this->loadConversation();
        }
    }

    public function openWidget(): void
    {
        $this->isOpen = true;
        $this->loadConversation();
    }

    public function closeWidget(): void
    {
        $this->isOpen = false;
    }

    public function loadConversation(): void
    {
        if (! Auth::check()) {
            $this->conversationId = null;
            $this->messages = [];
            $this->unreadCount = 0;

            return;
        }

        $conversation = SupportConversation::query()
            ->where('user_id', Auth::id())
            ->latest('last_message_at')
            ->latest('id')
            ->first();

        if (! $conversation) {
            $conversation = SupportConversation::query()->create([
                'user_id' => Auth::id(),
                'subject' => 'Hỗ trợ khách hàng',
                'status' => 'open',
                'last_message_at' => now(),
            ]);
        }

        $this->conversationId = $conversation->id;
        $this->refreshConversation();
    }

    public function refreshConversation(): void
    {
        if ($this->conversationId === null) {
            return;
        }

        $conversation = SupportConversation::query()->with('user')->find($this->conversationId);

        if (! $conversation) {
            $this->conversationId = null;
            $this->messages = [];
            $this->unreadCount = 0;

            return;
        }

        $this->messages = $conversation->messages()
            ->with('sender')
            ->orderBy('id')
            ->get()
            ->map(function (SupportMessage $message): array {
                $sender = $message->sender;

                return [
                    'id' => $message->id,
                    'sender_role' => $message->sender_role,
                    'message' => $message->message,
                    'created_at' => $message->created_at,
                    'sender_name' => $sender?->full_name ?: $sender?->username ?: 'Hỗ trợ',
                ];
            })
            ->all();

        $this->unreadCount = (int) $conversation->messages()
            ->where('sender_role', 'admin')
            ->whereNull('read_at')
            ->count();

        $conversation->messages()
            ->where('sender_role', 'admin')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function sendMessage(): void
    {
        if (! Auth::check()) {
            return;
        }

        $this->validate([
            'message' => ['required', 'string', 'max:2000'],
        ], [
            'message.required' => 'Vui lòng nhập tin nhắn.',
            'message.max' => 'Tin nhắn không được vượt quá 2000 ký tự.',
        ]);

        $conversation = SupportConversation::query()
            ->where('user_id', Auth::id())
            ->latest('last_message_at')
            ->latest('id')
            ->first();

        if (! $conversation) {
            $conversation = SupportConversation::query()->create([
                'user_id' => Auth::id(),
                'subject' => 'Hỗ trợ khách hàng',
                'status' => 'open',
            ]);
        }

        $conversation->update([
            'status' => 'open',
            'last_message_at' => now(),
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => Auth::id(),
            'sender_role' => 'customer',
            'message' => trim($this->message),
            'read_at' => null,
        ]);

        event(new SupportMessageSent($message));

        $this->message = '';
        $this->conversationId = $conversation->id;
        $this->refreshConversation();
    }

    public function render()
    {
        return view('livewire.customer-services');
    }
}
