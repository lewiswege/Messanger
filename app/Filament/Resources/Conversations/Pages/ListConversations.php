<?php

namespace App\Filament\Resources\Conversations\Pages;

use App\Filament\Resources\Conversations\ConversationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class ListConversations extends ListRecords
{
    protected static string $resource = ConversationResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Conversations'),
            'mine' => Tab::make('My Assignments')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('assigned_to', auth()->user()?->id)),
            'open' => Tab::make('Open')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['new', 'in_progress'])),
            'waiting' => Tab::make('Waiting')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'waiting_response')),
            'resolved' => Tab::make('Resolved')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'resolved')),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'all';
    }

    public function boot(): void
    {
        $this->resetTable();
    }

    #[On('echo:conversations,.NewMessage')]
    public function handleNewMessage($event): void
    {
        // Reset the table to reload data with updated unread counts
        $this->resetTable();
    }

    #[On('echo:conversations,.ConversationRead')]
    public function handleConversationRead($event): void
    {
        // Reset the table to reload data with updated unread counts
        $this->resetTable();
    }

    #[On('echo:conversations,.ConversationAssigned')]
    public function handleConversationAssigned($event): void
    {
        // Reset the table to reload data with updated assignments
        $this->resetTable();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
