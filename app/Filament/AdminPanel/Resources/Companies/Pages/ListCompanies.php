<?php

namespace App\Filament\AdminPanel\Resources\Companies\Pages;

use App\Filament\AdminPanel\Resources\Companies\CompaniesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCompanies extends ListRecords
{
    protected static string $resource = CompaniesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'active' => Tab::make('Actives')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('active', true)),
            'inactive' => Tab::make('Inactives')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('active', false)),
            'all' => Tab::make('All'),
        ];
    }
}
