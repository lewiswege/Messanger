<?php

namespace App\Filament\Resources\Webhooks\Schemas;

use App\Jobs\ProcessWebhook;
use App\Models\Webhook;
use Filament\Actions\Action;
use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class WebhookInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Delivery Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('channel')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'telegram' => 'info',
                                        'whatsapp' => 'success',
                                        default => 'gray',
                                    }),
                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'success' => 'success',
                                        'failed' => 'danger',
                                        'processing' => 'warning',
                                        default => 'gray',
                                    }),
                                TextEntry::make('created_at')
                                    ->label('Received At')
                                    ->dateTime(),
                            ]),
                    ]),

                Section::make('Error Log')
                    ->description('Technical details if the processing failed.')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->iconColor('danger')
                    ->hidden(fn ($record) => $record->status !== 'failed')
                    ->headerActions([
                        Action::make('retry')
                            ->label('Retry')
                            ->icon('heroicon-o-arrow-path')
                            ->color('warning')
                            ->requiresConfirmation()
                            ->action(function (Webhook $record) {
                                $record->update([
                                    'status' => 'pending',
                                    'error_log' => null,
                                ]);
                                ProcessWebhook::dispatch($record);

                                Notification::make()
                                    ->title('Webhook queued for retry')
                                    ->success()
                                    ->send();
                            }),
                    ])
                    ->schema([
                        CodeEntry::make('error_log')
                            ->hiddenLabel(),
                    ]),

                Section::make('Raw Payload')
                    ->description('The original data received from the external service.')
                    ->schema([
                        CodeEntry::make('payload')
                            ->hiddenLabel()
                            ->jsonFlags(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                    ]),
            ]);
    }
}
