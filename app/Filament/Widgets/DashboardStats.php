<?php

namespace App\Filament\Widgets;

use App\Models\Conversation;
use App\Models\Webhook;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class DashboardStats extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        // 1. Unassigned Conversations
        $unassignedCount = Conversation::whereNull('assigned_to')->count();

        // 2. Failed Webhooks (Last 24 Hours)
        $failedWebhooks = Webhook::where('status', 'failed')
            ->where('created_at', '>=', now()->subDay())
            ->count();

        // 3. Pending Queue Jobs (from the database jobs table)
        $pendingJobs = DB::table('jobs')->count();

        return [
            Stat::make('Unassigned Conversations', $unassignedCount)
                ->description('Conversations waiting for an agent')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color($unassignedCount > 0 ? 'warning' : 'success'),

            Stat::make('Webhook Failures (24h)', $failedWebhooks)
                ->description('Critical delivery errors')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($failedWebhooks > 0 ? 'danger' : 'success'),

            Stat::make('Pending Queue Jobs', $pendingJobs)
                ->description('Tasks waiting to be processed')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color($pendingJobs > 50 ? 'warning' : 'primary'),
        ];
    }
}
