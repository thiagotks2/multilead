<?php

namespace App\Modules\Websites\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CategoryType: string implements HasColor, HasLabel
{
    case General = 'general';
    case Post = 'post';
    case Property = 'property';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::General => 'General',
            self::Post => 'Blog Post',
            self::Property => 'Property',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::General => 'gray',
            self::Post => 'info',
            self::Property => 'success',
        };
    }
}
