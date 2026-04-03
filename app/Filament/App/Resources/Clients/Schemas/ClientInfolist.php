<?php

namespace App\Filament\App\Resources\Clients\Schemas;

use App\Filament\CustomComponents\Infolists\PhoneEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ClientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Client Card')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Details')
                            ->schema([
                                Section::make('Primary Data')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextEntry::make('name')->label('Name'),
                                                TextEntry::make('email')->label('Email'),
                                                PhoneEntry::make('phone')->label('Phone'),
                                            ]),
                                        TextEntry::make('user.name')
                                            ->label('Owner (Exclusive)')
                                            ->placeholder('Company (Global)'),
                                        TextEntry::make('notes')
                                            ->label('Notes')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Address & Profile')
                                    ->columns(2)
                                    ->schema([
                                        TextEntry::make('address.full')
                                            ->label('Address')
                                            ->state(fn ($record) => ($record->address['street'] ?? '').', '.
                                                ($record->address['number'] ?? '').' - '.
                                                ($record->address['neighborhood'] ?? '').' - '.
                                                ($record->address['city'] ?? '').'/'.
                                                ($record->address['state'] ?? '')
                                            ),
                                        TextEntry::make('profile_data.purchase_intent')
                                            ->label('Purchase Intent')
                                            ->badge()
                                            ->color(fn (?string $state): string => match ($state) {
                                                'low' => 'gray',
                                                'medium' => 'warning',
                                                'high' => 'info',
                                                'immediate' => 'success',
                                                default => 'gray',
                                            })
                                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                                'low' => 'Low',
                                                'medium' => 'Medium',
                                                'high' => 'High',
                                                'immediate' => 'Immediate',
                                                default => $state,
                                            }),
                                    ]),
                            ]),

                        Tab::make('Timeline')
                            ->schema([
                                ViewEntry::make('activities')
                                    ->label('Activity History')
                                    ->getStateUsing(fn ($record) => $record->activities()->latest()->get())
                                    ->view('app-panel.clients.components.activity-timeline')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
