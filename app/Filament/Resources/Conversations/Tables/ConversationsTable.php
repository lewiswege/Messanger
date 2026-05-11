<?php

namespace App\Filament\Resources\Conversations\Tables;

use App\Models\User;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Conversation;

class ConversationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.name')
                    ->searchable(),
                TextColumn::make('latestMessage.content')
                    ->label('Message content')
                    ->limit(50)
                    ->color('gray')
                    ->description(fn (Conversation $record): string =>
                        ($record->latestMessage?->direction === 'outbound' ? 'You: ' : 'Customer: ') .
                        ($record->latestMessage?->channel ?? '')
                    )
                    ->searchable(),
                TextColumn::make('assignedAgent.name')
                    ->label('Assigned To')
                    ->sortable()
                    ->default('Unassigned'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'gray',
                        'in_progress' => 'info',
                        'waiting_response' => 'warning',
                        'resolved' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('last_message_at')
                    ->label('Last Message At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('last_inbound_channel')
                    ->label('Channel')
                    ->badge()
                    ->getStateUsing(fn (Conversation $record): ?string =>
                        $record->last_inbound_channel ?? $record->latestMessage?->channel
                    ),
                TextColumn::make('unread_count')
                    ->label('Unread')
                    ->numeric()
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'gray')
                    ->sortable(),
                TextColumn::make('resolved_at')
                    ->label('Resolved At')
                    ->getStateUsing(fn (Conversation $record): string =>
                        $record->resolved_at ? $record->resolved_at->format('M d, Y H:i') : 'Pending'
                    )
                    ->color(fn ($state) => $state === 'Pending' ? 'warning' : null)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('last_message_at', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->with(['latestMessage', 'assignedAgent'])->whereNull('archived_at'))
            ->filters([
                TernaryFilter::make('archived')
                    ->label('Show Archived')
                    ->placeholder('Active only')
                    ->trueLabel('Archived only')
                    ->falseLabel('Active only')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('archived_at'),
                        false: fn ($query) => $query->whereNull('archived_at'),
                        blank: fn ($query) => $query, // Default handled by modifyQueryUsing
                    ),
            ])
            ->recordActions([
                ViewAction::make()->label('Chat'),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('assign_to_agent')
                        ->label('Assign to Agent')
                        ->icon('heroicon-o-user-plus')
                        ->form([
                            Select::make('assigned_to')
                                ->label('Agent')
                                ->options(User::all()->pluck('name', 'id'))
                                ->required()
                                ->searchable(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each->update(['assigned_to' => $data['assigned_to']]);
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Conversations assigned successfully'),

                    BulkAction::make('mark_as_resolved')
                        ->label('Mark as Resolved')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->update([
                                'status' => 'resolved',
                                'resolved_at' => now(),
                            ]);
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Conversations marked as resolved'),

                    BulkAction::make('archive')
                        ->label('Archive')
                        ->icon('heroicon-o-archive-box')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->update(['archived_at' => now()]);
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Conversations archived'),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
