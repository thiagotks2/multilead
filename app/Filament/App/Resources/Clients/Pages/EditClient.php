<?php

namespace App\Filament\App\Resources\Clients\Pages;

use App\Filament\App\Resources\Clients\ClientResource;
use Filament\Resources\Pages\EditRecord;

class EditClient extends EditRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
            \Filament\Actions\RestoreAction::make(),
        ];
    }
}
