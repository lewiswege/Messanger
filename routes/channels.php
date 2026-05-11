<?php
// filepath: routes/channels.php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    return $user !== null
        && Conversation::query()->whereKey($conversationId)->exists();
});
