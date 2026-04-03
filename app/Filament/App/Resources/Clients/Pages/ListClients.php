<?php

namespace App\Filament\App\Resources\Clients\Pages;

use App\Filament\App\Resources\Clients\ClientResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'My Clients' => Tab::make('My Clients')
                ->modifyQueryUsing(fn ($query) => $query->where('user_id', auth()->id())),
            'All' => Tab::make('All Clients'),
        ];
    }
}
