<?php

namespace App\Modules\Websites\Enums;

use Filament\Support\Contracts\HasLabel;

enum SiteStatus: string implements HasLabel
{
    case Development = 'development';
    case Production = 'production';
    case Maintenance = 'maintenance';
    case Inactive = 'inactive';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Development => 'Development',
            self::Production => 'Production',
            self::Maintenance => 'Maintenance',
            self::Inactive => 'Inactive',
        };
    }
}
