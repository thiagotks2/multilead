<?php

namespace App\Filament\AdminPanel\Resources\Companies\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Users')
                    ->schema([
                        Select::make('company_id')
                            ->label('Company')
                            ->relationship('company', 'name')
                            ->searchable()
                            ->required(),
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email')
                            ->label('Login email')
                            ->email()
                            ->required(),
                        TextInput::make('password')
                            ->password()
                            ->required()
                            ->hiddenOn('edit'),
                        TextInput::make('phone')
                            ->tel(),
                        Toggle::make('active')
                            ->required()->default(true),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->heading('Users')
            ->description('Manage users associated to this company')
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                IconColumn::make('active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make()
                    ->modalHeading('New User'),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('toggle_user_active')
                        ->label(fn ($record) => $record->active ? 'Deny access' : 'Allow access')
                        ->icon(fn ($record) => $record->active ? 'heroicon-m-x-circle' : 'heroicon-m-check-circle')
                        ->color(fn ($record) => $record->active ? 'gray' : 'success')
                        ->action(fn ($record) => $record->update(['active' => ! $record->active])
                        )
                        ->requiresConfirmation(),

                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('activate')
                        ->label('Allow access')
                        ->icon('heroicon-m-play')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update(['active' => true])
                        )
                        ->requiresConfirmation(),

                    BulkAction::make('deactivate')
                        ->label('Deny access')
                        ->icon('heroicon-m-stop')
                        ->color('gray')
                        ->action(fn (Collection $records) => $records->each->update(['active' => false])
                        )
                        ->requiresConfirmation(),

                    BulkAction::make('delete')
                        ->label('Delete')
                        ->icon('heroicon-m-trash')
                        ->color('danger')
                        ->action(fn (Collection $records) => $records->each->delete()
                        )
                        ->requiresConfirmation(),
                ]),
            ]);
    }
}
