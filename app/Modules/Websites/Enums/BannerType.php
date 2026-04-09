<?php

namespace App\Modules\Websites\Enums;

use Filament\Support\Contracts\HasLabel;

enum BannerType: string implements HasLabel
{
    case General = 'general';
    case EntryPopup = 'entry_popup';
    case ExitIntent = 'exit_intent';

    public function getLabel(): string
    {
        return match ($this) {
            self::General => 'General',
            self::EntryPopup => 'Entry Popup',
            self::ExitIntent => 'Exit Intent',
        };
    }
}
