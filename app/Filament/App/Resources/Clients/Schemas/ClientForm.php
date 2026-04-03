<?php

namespace App\Filament\App\Resources\Clients\Schemas;

use App\Filament\CustomComponents\Forms\PhoneInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Client Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule) => $rule->where('company_id', auth()->user()->company_id)),
                                PhoneInput::make('phone')
                                    ->label('Phone')
                                    ->maxLength(255),
                            ]),
                        Select::make('user_id')
                            ->label('Owner')
                            ->relationship('user', 'name', fn (Builder $query) => $query->where('company_id', auth()->user()->company_id))
                            ->searchable()
                            ->placeholder('Select a user to make this client exclusive')
                            ->helperText('Leave blank for shared company clients'),
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Address')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextInput::make('address.zip_code')->label('ZIP Code'),
                                TextInput::make('address.street')->label('Street')->columnSpan(2),
                                TextInput::make('address.number')->label('Number'),
                                TextInput::make('address.complement')->label('Complement'),
                                TextInput::make('address.neighborhood')->label('Neighborhood'),
                                TextInput::make('address.city')->label('City'),
                                TextInput::make('address.state')->label('State'),
                            ]),
                    ]),

                Section::make('Profile Data')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('profile_data.personal_income')
                                    ->label('Personal Income')
                                    ->numeric()
                                    ->prefix('R$'),
                                TextInput::make('profile_data.family_income')
                                    ->label('Family Income')
                                    ->numeric()
                                    ->prefix('R$'),
                                Select::make('profile_data.purchase_intent')
                                    ->label('Purchase Intent')
                                    ->options([
                                        'low' => 'Low',
                                        'medium' => 'Medium',
                                        'high' => 'High',
                                        'immediate' => 'Immediate',
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
