<?php

namespace App\Filament\App\Resources\Websites\SiteCategories\Pages;

use App\Filament\App\Resources\Websites\SiteCategories\SiteCategoryResource;
use App\Filament\App\Resources\Websites\WebsiteResource;
use App\Modules\Websites\Enums\CategoryType;
use App\Modules\Websites\Models\Site;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class ManageSiteCategories extends ManageRecords
{
    protected static string $resource = SiteCategoryResource::class;

    #[Url(keep: true)]
    public ?string $site = null;

    public function mount(): void
    {
        $site = Site::find($this->site);

        if (! $site || $site->company_id !== filament()->getTenant()->id) {
            abort(403, 'Unauthorized access to this site.');
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['site_id'] = $this->site;

                    return $data;
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Categories'),
            'general' => Tab::make('General')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', CategoryType::General)),
            'post' => Tab::make('Blog Posts')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', CategoryType::Post)),
            'property' => Tab::make('Properties')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', CategoryType::Property)),
        ];
    }

    public function getSubNavigation(): array
    {
        $site = Site::find($this->site);

        if (! $site) {
            return [];
        }

        return WebsiteResource::getSiteSubNavigation($site);
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('site_id', $this->site);
    }
}
