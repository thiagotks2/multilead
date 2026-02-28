<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SiteStatus: string implements HasLabel
{
    case Development = 'development';
    case Production = 'production';
    case Inactive = 'inactive';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Development => 'Development',
            self::Production => 'Production',
            self::Inactive => 'Inactive',
        };
    }
}
