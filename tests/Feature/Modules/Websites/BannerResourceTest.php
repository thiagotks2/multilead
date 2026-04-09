<?php

namespace Tests\Feature\Modules\Websites;

use App\Filament\App\Resources\Websites\BannerResource\Pages\ListBanners;
use App\Modules\Identity\Models\Company;
use App\Modules\Identity\Models\User;
use App\Modules\Websites\Enums\BannerType;
use App\Modules\Websites\Models\Site;
use App\Modules\Websites\Models\SiteBanner;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        $myBanner = SiteBanner::factory()->create([
            'site_id' => $this->site->id,
        ]);

        $otherSite = Site::factory()->create(['company_id' => $this->company->id]);
        $otherBanner = SiteBanner::factory()->create(['site_id' => $otherSite->id]);

        Livewire::test(ListBanners::class, ['site' => $this->site->id])
            ->assertCanSeeTableRecords([$myBanner])
            ->assertCanNotSeeTableRecords([$otherBanner]);
    }

    #[Test]
    public function can_create_banner_with_minimal_data_only_image_and_type(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('banner.jpg', 100, 'image/jpeg');

        Livewire::test(ListBanners::class, ['site' => $this->site->id])
            ->callAction('create', [
                'type' => BannerType::General->value,
                'image_path' => $file,
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas(SiteBanner::class, [
            'site_id' => $this->site->id,
            'type' => BannerType::General,
        ]);

        $banner = SiteBanner::firstWhere('site_id', $this->site->id);

        // Assert file exists on disk with dynamic path: {tenant}/{site}/banners/{filename}
        Storage::disk('public')->assertExists($banner->image_path);
        $this->assertStringContainsString("{$this->company->id}/{$this->site->id}/banners/", $banner->image_path);
    }

    #[Test]
    public function creation_fails_if_image_is_missing(): void
    {
        Livewire::test(ListBanners::class, ['site' => $this->site->id])
            ->callAction('create', [
                'type' => BannerType::General->value,
                'image_path' => null,
            ])
            ->assertHasActionErrors(['image_path' => 'required']);
    }

    #[Test]
    public function cannot_create_banner_with_invalid_media_format(): void
    {
        // GIF is not allowed (Policy: PNG/JPG)
        $gif = UploadedFile::fake()->create('animation.gif', 100, 'image/gif');

        Livewire::test(ListBanners::class, ['site' => $this->site->id])
            ->callAction('create', [
                'type' => BannerType::General->value,
                'image_path' => $gif,
            ])
            ->assertHasActionErrors(['image_path']);

        // WebP is not allowed
        $webp = UploadedFile::fake()->create('image.webp', 100, 'image/webp');

        Livewire::test(ListBanners::class, ['site' => $this->site->id])
            ->callAction('create', [
                'type' => BannerType::General->value,
                'image_path' => $webp,
            ])
            ->assertHasActionErrors(['image_path']);
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

        Livewire::test(ListBanners::class, ['site' => $this->site->id])
            ->assertCanSeeTableRecords([$generalBanner, $popupBanner])
            ->set('activeTab', BannerType::EntryPopup->value)
            ->assertCanSeeTableRecords([$popupBanner])
            ->assertCanNotSeeTableRecords([$generalBanner]);
    }
}
