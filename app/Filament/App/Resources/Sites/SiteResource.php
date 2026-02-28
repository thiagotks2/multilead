<?php

namespace App\Filament\App\Resources\Sites;

use App\Filament\App\Resources\Sites\Pages\CreateSite;
use App\Filament\App\Resources\Sites\Pages\EditSite;
use App\Filament\App\Resources\Sites\Pages\ListSites;
use App\Filament\App\Resources\Sites\Schemas\SiteForm;
use App\Filament\App\Resources\Sites\Tables\SitesTable;
use App\Models\Site;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SiteResource extends Resource
{
    protected static ?string $model = Site::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        $tenant = filament()->getTenant();

        if ($tenant && $tenant->sites()->count() > 1) {
            return 'My Sites';
        }

        return 'Settings';
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
        $tenant = filament()->getTenant();
        if ($tenant && $tenant->sites()->count() > 1) {
            return 'Sites'; // Groups under "Sites" label
        }

        return 'Site'; // Groups under "Site" label
    }

    public static function getModelLabel(): string
    {
        return 'Site';
    }

    public static function form(Schema $schema): Schema
    {
        return SiteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SitesTable::configure($table);
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
            'index' => ListSites::route('/'),
            'create' => CreateSite::route('/create'),
            'edit' => EditSite::route('/{record}/edit'),
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
