<?php

namespace App\Filament\Resources\Webhooks;

use App\Filament\Resources\Webhooks\Pages\ListWebhooks;
use App\Filament\Resources\Webhooks\Pages\ViewWebhook;
use App\Filament\Resources\Webhooks\Schemas\WebhookInfolist;
use App\Filament\Resources\Webhooks\Tables\WebhooksTable;
use App\Models\Webhook;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WebhookResource extends Resource
{
    protected static ?string $model = Webhook::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCommandLine;

    protected static ?string $navigationLabel = 'Webhook Logs';

    public static function infolist(Schema $schema): Schema
    {
        return WebhookInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WebhooksTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWebhooks::route('/'),
            'view' => ViewWebhook::route('/{record}'),
        ];
    }
}
