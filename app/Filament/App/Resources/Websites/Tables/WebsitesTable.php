<?php

namespace App\Filament\App\Resources\Websites\Tables;

use App\Modules\Websites\Enums\SiteStatus;
use App\Modules\Websites\Models\Site;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class WebsitesTable
{
    public static function configure(Table $table): Table
    {
        return $table
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
                    Action::make('setMaintenance')
                        ->label('Set Maintenance')
                        ->icon('heroicon-o-pause-circle')
                        ->color('info')
                        ->action(fn (Site $record) => $record->update(['status' => SiteStatus::Maintenance]))
                        ->requiresConfirmation()
                        ->visible(fn (Site $record) => $record->status !== SiteStatus::Maintenance),
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
                    BulkAction::make('setMaintenanceBulk')
                        ->label('Set Maintenance')
                        ->icon('heroicon-o-pause-circle')
                        ->color('info')
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => $records->each->update(['status' => SiteStatus::Maintenance]))
                        ->requiresConfirmation(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
