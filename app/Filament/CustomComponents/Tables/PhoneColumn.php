<?php

namespace App\Filament\CustomComponents\Tables;

use App\Support\Phone;
use Filament\Tables\Columns\TextColumn;

class PhoneColumn extends TextColumn
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Phone')
            ->formatStateUsing(function ($state) {
                if (! $state) {
                    return null;
                }

                $human = Phone::toHuman($state);

                if (! Phone::isValid($state)) {
                    return $human.' (invalid phone)';
                }

                return $human;
            })
            ->color(fn ($state) => ! Phone::isValid($state)
                    ? 'danger'
                    : ''
            )
            ->copyable()
            ->copyMessage('Phone copied')
            ->toggleable();
    }

    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'telephone');
    }
}
