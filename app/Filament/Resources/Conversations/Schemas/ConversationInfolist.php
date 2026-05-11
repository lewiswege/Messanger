<?php

namespace App\Filament\Resources\Conversations\Schemas;

use App\Livewire\Chat\ConversationChat;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Schema;

class ConversationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Livewire::make(ConversationChat::class)
                    ->lazy(false),
            ]);
    }
}
