<?php

namespace App\Filament\AdminPanel\Resources\Companies\RelationManagers;

use App\Enums\SiteStatus;
use App\Models\Site;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SitesRelationManager extends RelationManager
{
    protected static string $relationship = 'sites';

    public function form(Schema $schema): Schema
    {
        return \App\Filament\Schemas\SiteForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('setProduction')
                        ->label('Set Production')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Site $record) => $record->update(['status' => SiteStatus::Production]))
                        ->requiresConfirmation()
                        ->visible(fn (Site $record) => $record->status !== SiteStatus::Production),
                    Action::make('setDevelopment')
                        ->label('Set Development')
                        ->icon('heroicon-o-wrench')
                        ->color('warning')
                        ->action(fn (Site $record) => $record->update(['status' => SiteStatus::Development]))
                        ->requiresConfirmation()
                        ->visible(fn (Site $record) => $record->status !== SiteStatus::Development),
                    Action::make('setInactive')
                        ->label('Set Inactive')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn (Site $record) => $record->update(['status' => SiteStatus::Inactive]))
                        ->requiresConfirmation()
                        ->visible(fn (Site $record) => $record->status !== SiteStatus::Inactive),
                    DeleteAction::make(),
                    ForceDeleteAction::make(),
                    RestoreAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('setProductionBulk')
                        ->label('Set Production')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => $records->each->update(['status' => SiteStatus::Production]))
                        ->requiresConfirmation(),
                    BulkAction::make('setDevelopmentBulk')
                        ->label('Set Development')
                        ->icon('heroicon-o-wrench')
                        ->color('warning')
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => $records->each->update(['status' => SiteStatus::Development]))
                        ->requiresConfirmation(),
                    BulkAction::make('setInactiveBulk')
                        ->label('Set Inactive')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => $records->each->update(['status' => SiteStatus::Inactive]))
                        ->requiresConfirmation(),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }
}
