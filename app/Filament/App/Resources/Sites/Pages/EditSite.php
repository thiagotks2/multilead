<?php

namespace App\Filament\App\Resources\Sites\Pages;

use App\Filament\App\Resources\Sites\SiteResource;
use Filament\Resources\Pages\EditRecord;

class EditSite extends EditRecord
{
    protected static string $resource = SiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
