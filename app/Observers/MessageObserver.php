<?php

namespace App\Observers;

use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Support\Facades\Log;

class MessageObserver
{
    /**
     * Handle the Message "created" event.
     */
    public function created(Message $message): void
    {
        $conversation = $message->conversation;

        if (!$conversation) {
            Log::warning('MessageObserver: No conversation found for message', ['message_id' => $message->id]);
            return;
        }

        $oldStatus = $conversation->status;
        $updates = [
            'last_message_at' => now(),
            'archived_at' => null,
            'last_inbound_channel' => $message->channel,
        ];

        // 1. Handle Status Transitions
        if ($message->direction === 'inbound') {
            $updates['status'] = 'in_progress';
            $updates['unread_count'] = $conversation->unread_count + 1;
        } 
        else if ($message->direction === 'outbound') {
            $updates['status'] = 'waiting_response';
            $updates['unread_count'] = 0;
        }

        // 2. Perform the update
        $conversation->update($updates);

        // 3. Log the transition for debugging
        Log::info('Conversation Status Transition', [
            'conversation_id' => $conversation->id,
            'direction' => $message->direction,
            'from_status' => $oldStatus,
            'to_status' => $updates['status'] ?? $oldStatus,
            'unread_count' => $updates['unread_count'] ?? $conversation->unread_count,
        ]);
    }
}
