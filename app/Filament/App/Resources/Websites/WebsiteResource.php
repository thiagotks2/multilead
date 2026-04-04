<?php

namespace App\Filament\App\Resources\Websites;

use App\Filament\App\Resources\Websites\Pages\CreateWebsite;
use App\Filament\App\Resources\Websites\Pages\EditWebsite;
use App\Filament\App\Resources\Websites\Pages\ListWebsites;
use App\Filament\App\Resources\Websites\SiteCategories\SiteCategoryResource;
use App\Filament\App\Resources\Websites\Tables\WebsitesTable;
use App\Modules\Websites\Models\Site;
use BackedEnum;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Enums\SubNavigationPosition;
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

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    public static function getRecordSubNavigation(\Filament\Resources\Pages\Page $page): array
    {
        return static::getSiteSubNavigation($page->getRecord());
    }

    public static function getSiteSubNavigation(Site $record): array
    {
        return [
            NavigationItem::make('General Settings')
                ->icon('heroicon-o-cog-6-tooth')
                ->url(static::getUrl('edit', ['record' => $record]))
                ->isActiveWhen(fn () => request()->routeIs('*.websites.edit')),
            NavigationItem::make('Categories')
                ->icon('heroicon-o-tag')
                ->url(SiteCategoryResource::getUrl('index', ['site' => $record]))
                ->isActiveWhen(fn () => request()->routeIs('*.websites.site-categories.index')),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $tenant = filament()->getTenant();

        if (! $tenant) {
            return false;
        }

        return $tenant->sites()->count() > 0;
    }

    public static function getNavigationLabel(): string
    {
        $tenant = filament()->getTenant();

        if ($tenant && $tenant->sites()->count() === 1) {
            return 'Website';
        }

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
        $tenant = filament()->getTenant();

        if ($tenant && $tenant->sites()->count() === 1) {
            return null;
        }

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
