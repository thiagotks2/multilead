<?php

namespace App\Filament\App\Resources\Websites\BannerResource\Tables;

use App\Modules\Websites\Models\Site;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class BannerTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Banner')
                    ->disk('public')
                    // Correcting path to company/site/...
                    // This will be resolved by the dynamic attribute eventually
                    // or we tell Filament where to find it.
                    ->width(100)
                    ->height(50),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->description(fn ($record) => $record->description),

                TextColumn::make('type')
                    ->badge()
                    ->sortable(),

                TextColumn::make('display_until')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    ViewAction::make()
                        ->modalFooterActions(fn ($record) => [
                            Action::make('edit')
                                ->label('Edit Banner')
                                ->button()
                                ->color('primary')
                                ->action(function ($record, $livewire) {
                                    $livewire->unmountTableAction();
                                    $livewire->mountTableAction('edit', $record->getKey());
                                }),
                        ]),
                    DeleteAction::make(),
                    ForceDeleteAction::make(),
                    RestoreAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
