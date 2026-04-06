<?php

namespace Tests\Feature\Modules\Websites;

use App\Enums\BannerType;
use App\Filament\App\Resources\Websites\BannerResource\Pages\ListBanners;
use App\Modules\Identity\Models\Company;
use App\Modules\Identity\Models\User;
use App\Modules\Websites\Models\Site;
use App\Modules\Websites\Models\SiteBanner;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BannerResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Site $site;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create(['active' => true]);
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'active' => true,
        ]);

        $this->site = Site::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $this->actingAs($this->user, 'user');

        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($this->company);
    }

    #[Test]
    public function can_list_banners_only_from_the_active_site_context(): void
    {
        // Banner for current site
        $myBanner = SiteBanner::factory()->create([
            'site_id' => $this->site->id,
        ]);

        // Banner for another site in the SAME company
        $otherSiteInSameCompany = Site::factory()->create([
            'company_id' => $this->company->id,
        ]);
        $otherBannerSameCompany = SiteBanner::factory()->create([
            'site_id' => $otherSiteInSameCompany->id,
        ]);

        // Banner for another company
        $otherCompany = Company::factory()->create(['active' => true]);
        $otherSiteAlternative = Site::factory()->create([
            'company_id' => $otherCompany->id,
        ]);
        $otherBannerCrossCompany = SiteBanner::factory()->create([
            'site_id' => $otherSiteAlternative->id,
        ]);

        Livewire::test(ListBanners::class)
            ->assertCanSeeTableRecords([$myBanner])
            ->assertCanNotSeeTableRecords([$otherBannerSameCompany, $otherBannerCrossCompany]);
    }

    #[Test]
    public function can_create_banner_with_minimal_data_only_image_and_type(): void
    {
        Livewire::test(ListBanners::class)
            ->callTableAction('create', $this->site, [
                'type' => BannerType::General->value,
                'image_path' => 'minimal-banner.jpg',
                'title' => null, // Optional
                'description' => null, // Optional
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas(SiteBanner::class, [
            'site_id' => $this->site->id,
            'image_path' => 'minimal-banner.jpg',
            'type' => BannerType::General,
            'title' => null,
        ]);
    }

    #[Test]
    public function creation_fails_if_image_is_missing(): void
    {
        Livewire::test(ListBanners::class)
            ->callTableAction('create', $this->site, [
                'type' => BannerType::General->value,
                'title' => 'No Image',
                'image_path' => null, // Mandatory
            ])
            ->assertHasTableActionErrors(['image_path']);
    }

    #[Test]
    public function cannot_create_banner_with_invalid_media_format(): void
    {
        // Testing rejection of .gif which is not in the policy (only PNG/JPG)
        Livewire::test(ListBanners::class)
            ->callTableAction('create', $this->site, [
                'type' => BannerType::General->value,
                'image_path' => 'animation.gif',
            ])
            ->assertHasTableActionErrors(['image_path']);

        // Testing rejection of .webp
        Livewire::test(ListBanners::class)
            ->callTableAction('create', $this->site, [
                'type' => BannerType::General->value,
                'image_path' => 'image.webp',
            ])
            ->assertHasTableActionErrors(['image_path']);
    }

    #[Test]
    public function can_filter_banners_by_type_tabs(): void
    {
        $generalBanner = SiteBanner::factory()->create([
            'site_id' => $this->site->id,
            'type' => BannerType::General,
        ]);

        $popupBanner = SiteBanner::factory()->create([
            'site_id' => $this->site->id,
            'type' => BannerType::EntryPopup,
        ]);

        Livewire::test(ListBanners::class)
            ->assertCanSeeTableRecords([$generalBanner, $popupBanner])
            ->filterTable('type', BannerType::EntryPopup->value)
            ->assertCanSeeTableRecords([$popupBanner])
            ->assertCanNotSeeTableRecords([$generalBanner]);
    }
}
