<?php

namespace App\Events;

use App\Models\SupportMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SupportMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public SupportMessage $message)
    {
        $this->message->loadMissing(['conversation.user', 'sender']);
    }

    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('support.conversations.'.$this->message->support_conversation_id),
            new PrivateChannel('support.inbox'),
        ];

        $userId = (int) ($this->message->conversation?->user_id ?? 0);

        if ($userId > 0) {
            $channels[] = new PrivateChannel('support.user.'.$userId);
        }

        return $channels;
    }

    public function broadcastWith(): array
    {
        $conversation = $this->message->conversation;
        $sender = $this->message->sender;

        return [
            'conversation_id' => $this->message->support_conversation_id,
            'message' => [
                'id' => $this->message->id,
                'sender_role' => $this->message->sender_role,
                'sender_id' => $this->message->sender_id,
                'message' => $this->message->message,
                'created_at' => $this->message->created_at?->toDateTimeString(),
                'read_at' => $this->message->read_at?->toDateTimeString(),
                'sender_name' => $sender?->full_name ?: $sender?->username ?: 'Hệ thống hỗ trợ',
            ],
            'conversation' => [
                'id' => $conversation?->id,
                'status' => $conversation?->status,
                'subject' => $conversation?->subject,
                'user_name' => $conversation?->user?->full_name ?: $conversation?->user?->username,
                'last_message_at' => $conversation?->last_message_at?->toDateTimeString(),
            ],
        ];
    }
}
