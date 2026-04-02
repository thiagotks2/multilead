<?php

namespace App\Filament\CustomComponents\Forms;

use App\Rules\ValidPhone;
use App\Support\Phone;
use Filament\Forms\Components\TextInput;

class PhoneInput extends TextInput
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Phone')
            ->rules([
                new ValidPhone,
            ])
            ->dehydrateStateUsing(fn ($state) => Phone::toDatabase($state))
            ->formatStateUsing(fn ($state) => Phone::toHuman($state))
            ->extraAttributes([
                'x-data' => '',
                'x-init' => 'applyPhoneMask($el)',
                'inputmode' => 'numeric',
                'pattern' => '[0-9]*',
            ]);
    }

    /**
     * Semantic shortcut
     */
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'telephone');
    }
}
