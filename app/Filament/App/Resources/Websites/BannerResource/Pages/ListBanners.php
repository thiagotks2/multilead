<?php

namespace App\Filament\App\Resources\Websites\BannerResource\Pages;

use App\Filament\App\Resources\Websites\BannerResource\BannerResource;
use App\Filament\App\Resources\Websites\WebsiteResource;
use App\Modules\Websites\Enums\BannerType;
use App\Modules\Websites\Models\Site;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class ListBanners extends ManageRecords
{
    protected static string $resource = BannerResource::class;

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
        $tabs = [
            'all' => Tab::make('All Banners'),
        ];

        foreach (BannerType::cases() as $type) {
            $tabs[$type->value] = Tab::make($type->getLabel())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', $type))
                ->excludeQueryWhenResolvingRecord();
        }

        return $tabs;
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
