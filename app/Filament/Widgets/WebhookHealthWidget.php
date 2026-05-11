<?php

namespace App\Filament\Widgets;

use App\Models\Webhook;
use App\Jobs\ProcessWebhook;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class WebhookHealthWidget extends TableWidget
{
    protected static ?string $heading = 'Critical Webhook Failures';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Webhook::query()
                    ->where('status', 'failed')
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('channel')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'telegram' => 'info',
                        'whatsapp' => 'success',
                        'simulator' => 'gray',
                        default => 'primary',
                    }),
                
                Tables\Columns\TextColumn::make('error_log')
                    ->label('Error Message')
                    ->limit(50)
                    ->tooltip(fn ($state) => $state)
                    ->wrap(),

                Tables\Columns\TextColumn::make('attempts')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Failed At')
                    ->dateTime()
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Action::make('retry')
                    ->label('Retry')
                    ->icon('heroicon-m-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Webhook $record) {
                        $record->update([
                            'status' => 'pending',
                            'error_log' => null,
                        ]);
                        
                        ProcessWebhook::dispatch($record)->onQueue('webhook');

                        Notification::make()
                            ->title('Webhook queued for retry')
                            ->success()
                            ->send();
                    }),
            ])
            ->headerActions([
                Action::make('retry_all')
                    ->label('Retry All Failed')
                    ->icon('heroicon-m-arrow-path')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function () {
                        $failed = Webhook::where('status', 'failed')->get();
                        
                        foreach ($failed as $webhook) {
                            $webhook->update([
                                'status' => 'pending',
                                'error_log' => null,
                            ]);
                            ProcessWebhook::dispatch($webhook)->onQueue('webhook');
                        }

                        Notification::make()
                            ->title($failed->count() . ' webhooks queued for retry')
                            ->success()
                            ->send();
                    })
                    ->visible(fn () => Webhook::where('status', 'failed')->exists()),
            ]);
    }
}
