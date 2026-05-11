<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Livewire\Customer\CustomerContextSidebar;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Schema;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Livewire::make(CustomerContextSidebar::class)
                    ->lazy(false)
                    ->columnSpanFull(),
            ]);
    }
}
