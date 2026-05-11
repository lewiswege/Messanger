<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Collection;
use Telegram\Bot\Laravel\Facades\Telegram;
use Exception;

class RegisteredWebhooks extends TableWidget
{
    protected static ?string $heading = 'Registered Webhook Endpoints';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn() => null)
            ->records(fn () => $this->getWebhookEndpoints())
            ->columns([
                Tables\Columns\TextColumn::make('channel')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'telegram' => 'info',
                        'whatsapp' => 'success',
                        'simulator' => 'gray',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('url')
                    ->label('Endpoint URL')
                    ->copyable()
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Verified' => 'success',
                        'Pending' => 'warning',
                        'Error' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Verified' => 'heroicon-m-check-circle',
                        'Pending' => 'heroicon-m-clock',
                        'Error' => 'heroicon-m-x-circle',
                        default => 'heroicon-m-question-mark-circle',
                    }),

                Tables\Columns\TextColumn::make('details')
                    ->label('Provider Info')
                    ->placeholder('N/A'),
            ]);
    }

    protected function getWebhookEndpoints(): Collection
    {
        $baseUrl = config('app.url');
        
        $endpoints = collect([
            [
                'id' => 'telegram',
                'channel' => 'telegram',
                'url' => "{$baseUrl}/api/webhook/telegram",
                'status' => 'Pending',
                'details' => null,
            ],
            [
                'id' => 'whatsapp',
                'channel' => 'whatsapp',
                'url' => "{$baseUrl}/api/webhook/whatsapp",
                'status' => 'Pending Setup',
                'details' => 'Waiting for Meta configuration',
            ],
            [
                'id' => 'simulator',
                'channel' => 'simulator',
                'url' => "{$baseUrl}/api/webhook/simulator",
                'status' => 'Active',
                'details' => 'Internal Testing Tool',
            ],
        ]);

        return $endpoints->map(function ($item) {
            if ($item['channel'] === 'telegram') {
                try {
                    $webhookInfo = Telegram::getWebhookInfo();
                    
                    if (empty($webhookInfo->url)) {
                        $item['status'] = 'Not Set';
                        $item['details'] = 'No webhook registered with Telegram';
                    } elseif ($webhookInfo->url === $item['url']) {
                        $item['status'] = 'Verified';
                        $item['details'] = 'Connected! Pending updates: ' . $webhookInfo->pendingUpdateCount;
                    } else {
                        // This happens when using Ngrok/Tunnels but APP_URL is still localhost
                        $item['status'] = 'Warning';
                        $item['details'] = 'Tunnel active. Telegram is sending to: ' . $webhookInfo->url;
                    }
                } catch (Exception $e) {
                    $item['status'] = 'Error';
                    $item['details'] = 'API Error: ' . $e->getMessage();
                }
            }
            return $item;
        });
    }
}
