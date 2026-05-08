<?php

use App\Models\SupportConversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('support.inbox', function ($user): bool {
    return in_array((string) $user->role, ['admin', 'servicescustomer', 'staff'], true);
});

Broadcast::channel('support.conversations.{conversationId}', function ($user, int $conversationId): bool {
    $isSupportStaff = in_array((string) $user->role, ['admin', 'servicescustomer', 'staff'], true);

    if ($isSupportStaff) {
        return true;
    }

    $conversation = SupportConversation::query()->find($conversationId);

    return (bool) $conversation && (int) $conversation->user_id === (int) $user->id;
});

Broadcast::channel('support.user.{userId}', function ($user, int $userId): bool {
    return (int) $user->id === $userId;
});
