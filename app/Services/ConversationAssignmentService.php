<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ConversationAssignmentService
{
    /**
     * Find the agent with the least number of active conversations.
     */
    public function findAvailableAgent(): ?User
    {
        return User::query()
            ->withCount(['conversations' => function ($query) {
                $query->whereNotIn('status', ['resolved', 'archived']);
            }])
            ->orderBy('conversations_count', 'asc')
            ->first();
    }

    //Assign a conversation to the given agent, or auto-select one with least workload.
    public function assignConversation(Conversation $conversation, ?User $agent = null): bool
    {
        $agent = $agent ?? $this->findAvailableAgent();

        if (!$agent) {
            Log::warning('No agents available for auto-assignment', [
                'conversation_id' => $conversation->id,
            ]);
            return false;
        }

        try {
            $conversation->update(['assigned_to' => $agent->id]);
        } catch (\Exception $e) {
            Log::error('Failed to assign conversation', [
                'conversation_id' => $conversation->id,
                'agent_id' => $agent->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }

        Log::info('Conversation auto-assigned', [
            'conversation_id' => $conversation->id,
            'agent_id' => $agent->id,
            'agent_name' => $agent->name,
        ]);

        return true;
    }
}
