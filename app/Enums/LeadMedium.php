<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum LeadMedium: string implements HasColor, HasIcon, HasLabel
{
    case Organic = 'organic';
    case Paid = 'paid';
    case Referral = 'referral';
    case Direct = 'direct';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Organic => 'Organic',
            self::Paid => 'Paid',
            self::Referral => 'Referral',
            self::Direct => 'Direct',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Organic => 'success',
            self::Paid => 'warning',
            self::Referral => 'info',
            self::Direct => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Organic => 'heroicon-o-globe-alt',
            self::Paid => 'heroicon-o-currency-dollar',
            self::Referral => 'heroicon-o-users',
            self::Direct => 'heroicon-o-arrow-right-circle',
        };
    }
}
