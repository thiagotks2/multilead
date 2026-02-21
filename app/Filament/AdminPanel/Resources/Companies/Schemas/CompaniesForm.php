<?php

namespace App\Filament\AdminPanel\Resources\Companies\Schemas;

use App\Enums\DocumentType;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;

class CompaniesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identificação do ClienteA')
                    ->afterHeader([
                        Toggle::make('active')
                        ->default(true)
                    ])
                    ->description('Principais informaçoes')
                    ->schema([
                        TextInput::make('name')
                            ->columnSpanFull()
                            ->required(),
                        Select::make('document_type')
                            ->options(DocumentType::class)
                            ->getOptionLabelsUsing(fn (string $value): ?string => DocumentType::tryFrom($value)?->getLabel())
                            ->required(),
                        TextInput::make('document_number')
                            ->required(),
                    ])->columns(2),
                Section::make('Informaçoes de Contato')
                    ->schema([
                        TextInput::make('email')
                            ->required()
                            ->email(),
                        TextInput::make('phone')
                            ->required()
                            ->tel(),
                    ])->columns(2),
                Section::make('Address')
                    ->schema([
                        TextInput::make('country'),
                        TextInput::make('zip_code'),
                        TextInput::make('address'),
                        TextInput::make('city'),
                        TextInput::make('state'),
                    ])->columns(3),
            ])
            ->columns(1);
    }
}
