<?php

namespace App\Filament\CustomComponents\Infolists;

use App\Support\Phone;
use Filament\Infolists\Components\TextEntry;

class PhoneEntry extends TextEntry
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Phone')
            ->formatStateUsing(fn ($state) => Phone::toHuman($state))
            ->suffix(fn ($state) => $state && ! Phone::isValid($state)
                    ? ' (invalid phone)'
                    : null
            )
            ->color(fn ($state) => $state && ! Phone::isValid($state)
                    ? 'danger'
                    : null);
    }

    /**
     * Semantic shortcut
     */
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'telephone');
    }
}
