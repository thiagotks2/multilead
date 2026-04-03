<?php

namespace App\Filament\App\Resources\Websites;

use App\Filament\App\Resources\Websites\Pages\CreateWebsite;
use App\Filament\App\Resources\Websites\Pages\EditWebsite;
use App\Filament\App\Resources\Websites\Pages\ListWebsites;
use App\Filament\App\Resources\Websites\Tables\WebsitesTable;
use App\Modules\Websites\Models\Site;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WebsiteResource extends Resource
{
    protected static ?string $model = Site::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        $tenant = filament()->getTenant();

        if ($tenant && $tenant->sites()->count() > 1) {
            return 'My Websites';
        }

        return 'Website Settings';
    }

    public static function getNavigationUrl(): string
    {
        $tenant = filament()->getTenant();

        if ($tenant && $tenant->sites()->count() === 1) {
            $site = $tenant->sites()->first();

            return static::getUrl('edit', ['record' => $site]);
        }

        return static::getUrl('index');
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Websites';
    }

    public static function getModelLabel(): string
    {
        return 'Website';
    }

    public static function form(Schema $schema): Schema
    {
        return \App\Filament\Schemas\WebsiteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WebsitesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWebsites::route('/'),
            'create' => CreateWebsite::route('/create'),
            'edit' => EditWebsite::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
