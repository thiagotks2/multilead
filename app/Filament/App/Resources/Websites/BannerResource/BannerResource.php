<?php

namespace App\Filament\App\Resources\Websites\BannerResource;

use App\Filament\App\Resources\Websites\BannerResource\Pages\ListBanners;
use App\Filament\App\Resources\Websites\BannerResource\Schemas\BannerSchema;
use App\Filament\App\Resources\Websites\BannerResource\Tables\BannerTable;
use App\Modules\Websites\Models\SiteBanner;
use BackedEnum;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BannerResource extends Resource
{
    protected static ?string $model = SiteBanner::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    // protected static ?string $tenantOwnershipRelationshipName = 'company';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
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
        return 'Banner';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Banners';
    }

    public static function getNavigationLabel(): string
    {
        return 'Banners';
    }

    public static function getBreadcrumb(): string
    {
        return 'Banners';
    }

    public static function form(Schema $schema): Schema
    {
        return BannerSchema::make($schema);
    }

    public static function table(Table $table): Table
    {
        return BannerTable::make($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                ViewEntry::make('banner_preview')
                    ->label(false)
                    ->view('app-panel.websites.components.banner-preview')
                    ->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBanners::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
