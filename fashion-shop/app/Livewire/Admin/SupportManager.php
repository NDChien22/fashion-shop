<?php

namespace App\Livewire\Admin;

use App\Events\SupportMessageSent;
use App\Models\SupportConversation;
use App\Models\SupportMessage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SupportManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    public ?int $selectedConversationId = null;

    public string $replyMessage = '';

    public int $refreshTrigger = 0;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
    ];

    public function getListeners(): array
    {
        return [
            'echo-private:support.inbox,App.Events.SupportMessageSent' => 'handleInboxRealtimeMessage',
        ];
    }

    public function handleInboxRealtimeMessage(array $payload = []): void
    {
        $conversationId = isset($payload['conversation_id']) ? (int) $payload['conversation_id'] : null;
        $this->refreshInbox($conversationId);
        $this->refreshTrigger++;
    }

    public function mount(): void
    {
        $firstConversation = $this->conversationQuery()->first();

        if ($firstConversation) {
            $this->selectConversation($firstConversation->id);
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function selectConversation(int $conversationId): void
    {
        $conversation = SupportConversation::query()
            ->with(['user', 'latestMessage'])
            ->find($conversationId);

        if (! $conversation) {
            session()->flash('error', 'Không tìm thấy cuộc trò chuyện hỗ trợ.');

            return;
        }

        $this->selectedConversationId = $conversation->id;

        SupportMessage::query()
            ->where('support_conversation_id', $conversation->id)
            ->where('sender_role', 'customer')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function refreshInbox(?int $conversationId = null): void
    {
        if ($conversationId !== null && $this->selectedConversationId !== null && $conversationId === $this->selectedConversationId) {
            SupportMessage::query()
                ->where('support_conversation_id', $conversationId)
                ->where('sender_role', 'customer')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }
    }

    public function refreshConversationData(): void
    {
        $this->refreshTrigger++;
    }

    public function updateConversationStatus(string $status): void
    {
        if ($this->selectedConversationId === null) {
            return;
        }

        if (! in_array($status, ['open', 'pending', 'closed'], true)) {
            return;
        }

        $conversation = SupportConversation::query()->find($this->selectedConversationId);

        if (! $conversation) {
            return;
        }

        $conversation->update([
            'status' => $status,
            'resolved_at' => $status === 'closed' ? now() : null,
            'admin_id' => Auth::id(),
        ]);

        session()->flash('success', 'Đã cập nhật trạng thái cuộc trò chuyện.');
    }

    public function sendReply(): void
    {
        $this->validate([
            'replyMessage' => ['required', 'string', 'max:5000'],
        ], [
            'replyMessage.required' => 'Vui lòng nhập nội dung phản hồi.',
            'replyMessage.max' => 'Phản hồi không được vượt quá 5000 ký tự.',
        ]);

        if ($this->selectedConversationId === null) {
            return;
        }

        $conversation = SupportConversation::query()->find($this->selectedConversationId);

        if (! $conversation) {
            session()->flash('error', 'Cuộc trò chuyện không còn tồn tại.');

            return;
        }

        $conversation->update([
            'status' => 'open',
            'resolved_at' => null,
            'admin_id' => Auth::id(),
            'last_message_at' => now(),
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => Auth::id(),
            'sender_role' => 'admin',
            'message' => trim($this->replyMessage),
            'read_at' => now(),
        ]);

        event(new SupportMessageSent($message));

        $this->replyMessage = '';
        $this->selectConversation($conversation->id);
        session()->flash('success', 'Đã gửi phản hồi cho khách hàng.');
    }

    private function conversationQuery(): Builder
    {
        $keyword = trim($this->search);

        return SupportConversation::query()
            ->with([
                'user',
                'latestMessage.sender',
            ])
            ->withCount([
                'messages as unread_count' => function (Builder $query): void {
                    $query->where('sender_role', 'customer')->whereNull('read_at');
                },
            ])
            ->when($this->statusFilter !== 'all', function (Builder $query): void {
                $query->where('status', $this->statusFilter);
            })
            ->when($keyword !== '', function (Builder $query) use ($keyword): void {
                $query->where(function (Builder $builder) use ($keyword): void {
                    $builder->where('subject', 'like', '%'.$keyword.'%')
                        ->orWhere('contact_name', 'like', '%'.$keyword.'%')
                        ->orWhere('contact_email', 'like', '%'.$keyword.'%')
                        ->orWhereHas('user', function (Builder $userQuery) use ($keyword): void {
                            $userQuery->where('username', 'like', '%'.$keyword.'%')
                                ->orWhere('full_name', 'like', '%'.$keyword.'%')
                                ->orWhere('email', 'like', '%'.$keyword.'%');
                        })
                        ->orWhereHas('latestMessage', function (Builder $messageQuery) use ($keyword): void {
                            $messageQuery->where('message', 'like', '%'.$keyword.'%');
                        });
                });
            })
            ->orderByDesc('last_message_at')
            ->orderByDesc('id');
    }

    public function render()
    {
        $conversations = $this->conversationQuery()->paginate(10);

        $selectedConversation = null;
        $messages = collect();

        if ($this->selectedConversationId !== null) {
            $selectedConversation = SupportConversation::query()
                ->with(['user', 'admin'])
                ->withCount([
                    'messages as unread_count' => function (Builder $query): void {
                        $query->where('sender_role', 'customer')->whereNull('read_at');
                    },
                ])
                ->find($this->selectedConversationId);

            if ($selectedConversation) {
                $messages = $selectedConversation->messages()
                    ->with('sender')
                    ->orderBy('id')
                    ->get()
                    ->map(function (SupportMessage $message): array {
                        $sender = $message->sender;

                        return [
                            'id' => $message->id,
                            'sender_role' => $message->sender_role,
                            'message' => $message->message,
                            'read_at' => $message->read_at,
                            'created_at' => $message->created_at,
                            'sender_name' => $sender?->full_name ?: $sender?->username ?: 'Khách hàng',
                        ];
                    });
            }
        }

        $summary = [
            'unread' => SupportMessage::query()->where('sender_role', 'customer')->whereNull('read_at')->count(),
            'unread_conversations' => SupportConversation::query()
                ->whereHas('messages', function (Builder $query): void {
                    $query->where('sender_role', 'customer')->whereNull('read_at');
                })
                ->count(),
        ];

        return view('livewire.admin.support-manager', [
            'conversations' => $conversations,
            'selectedConversation' => $selectedConversation,
            'messages' => $messages,
            'summary' => $summary,
        ]);
    }
}
