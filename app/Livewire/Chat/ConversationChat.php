<?php

namespace App\Livewire\Chat;

use App\Events\ConversationRead;
use App\Models\Conversation;
use App\Services\OutboundMessage;
use Closure;
use Livewire\Attributes\On;
use Livewire\Component;

class ConversationChat extends Component
{
    public int $conversationId;
    public string $messageContent = '';
    public bool $isActive = false;

    public function mount(Conversation|Closure $record): void
    {
        if ($record instanceof Closure) {
            $record = $record();
        }

        $this->conversationId = $record->id;
        $this->isActive = true;

        // Mark as read on mount
        $this->markAsRead($record);
    }

    #[On('echo:conversations,.NewMessage')]
    public function onNewMessage(array $event): void
    {
        // Only mark as read if this component is actively being viewed
        if ($this->isActive && isset($event['conversation_id']) && $event['conversation_id'] === $this->conversationId) {
            $conversation = Conversation::find($this->conversationId);
            if ($conversation) {
                $this->markAsRead($conversation);
            }
        }
    }

    private function markAsRead(Conversation $conversation): void
    {
        if ($conversation->unread_count > 0) {
            $conversation->update(['unread_count' => 0]);
            broadcast(new ConversationRead($conversation))->toOthers();
        }
    }

    public function resolve(): void
    {
        $conversation = Conversation::findOrFail($this->conversationId);
        $conversation->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);
    }

    public function sendMessage(): void
    {
        $this->validate([
            'messageContent' => 'required|string|min:1',
        ]);

        $conversation = Conversation::findOrFail($this->conversationId);

        app(OutboundMessage::class)->sendReply($conversation, $this->messageContent);

        $this->messageContent = '';
    }

    public function render()
    {
        $conversation = Conversation::findOrFail($this->conversationId);

        return view('livewire.chat.conversation-chat', [
            'record' => $conversation,
            'messages' => $conversation->messages()->orderBy('created_at', 'asc')->get(),
        ]);
    }
}
