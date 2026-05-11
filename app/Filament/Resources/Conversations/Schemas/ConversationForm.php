<?php

namespace App\Filament\Resources\Conversations\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ConversationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('ulid')
                    ->required(),
                Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->required(),
                Select::make('status')
                    ->options(['open' => 'Open', 'pending' => 'Pending', 'resolved' => 'Resolved'])
                    ->default('open')
                    ->required(),
                DateTimePicker::make('last_message_at'),
                Select::make('last_inbound_channel')
                    ->options(['whatsapp' => 'Whatsapp', 'telegram' => 'Telegram', 'sms' => 'Sms']),
                TextInput::make('unread_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('resolved_at'),
            ]);
    }
}
