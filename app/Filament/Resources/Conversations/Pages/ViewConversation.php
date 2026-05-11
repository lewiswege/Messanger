<?php

namespace App\Filament\Resources\Conversations\Pages;

use App\Filament\Resources\Conversations\ConversationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Log;

/**
 * @property \App\Models\Conversation $record
 */
class ViewConversation extends ViewRecord
{
    protected static string $resource = ConversationResource::class;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        $conversation = $this->getRecord();

        if ($conversation && $conversation->unread_count > 0) {
            $conversation->update(['unread_count' => 0]);
            $conversation->refresh();

            broadcast(new \App\Events\ConversationRead($conversation));

            Log::info('Conversation marked as read', [
                'conversation_id' => $conversation->id
            ]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
