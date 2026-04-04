<?php

namespace Tests\Feature\Modules\Websites;

use App\Filament\App\Resources\Websites\SiteCategories\Pages\ManageSiteCategories;
use App\Filament\App\Resources\Websites\SiteCategories\SiteCategoryResource;
use App\Modules\Identity\Models\Company;
use App\Modules\Identity\Models\User;
use App\Modules\Websites\Enums\CategoryType;
use App\Modules\Websites\Models\Site;
use App\Modules\Websites\Models\SiteCategory;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class SiteCategoryTest extends TestCase
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

        $this->site = Site::factory()->create(['company_id' => $this->company->id]);

        $this->actingAs($this->user, 'user');

        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($this->company);
    }

    /**
     * Requirement: Manage Categories page loads with site context.
     */
    public function test_can_list_site_categories(): void
    {
        $categories = SiteCategory::factory()->count(3)->create([
            'site_id' => $this->site->id,
        ]);

        Livewire::test(ManageSiteCategories::class, [
            'site' => $this->site->id,
        ])
            ->assertCanSeeTableRecords($categories)
            ->assertTableColumnExists('name')
            ->assertTableColumnExists('type')
            ->assertTableColumnExists('slug');
    }

    /**
     * Requirement: Sub-navigation is present and correct.
     */
    public function test_can_see_site_sub_navigation(): void
    {
        Livewire::test(ManageSiteCategories::class, [
            'site' => $this->site->id,
        ])
            ->assertSee('General Settings')
            ->assertSee('Categories');
    }

    /**
     * Requirement: Create Category via Modal (BR01).
     */
    public function test_can_create_category_via_modal(): void
    {
        Livewire::test(ManageSiteCategories::class, [
            'site' => $this->site->id,
        ])
            ->assertActionVisible('create')
            ->callAction('create', data: [
                'name' => 'News Category',
                'type' => CategoryType::Post,
                'slug' => 'news-category',
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('site_categories', [
            'site_id' => $this->site->id,
            'name' => 'News Category',
            'type' => CategoryType::Post->value,
        ]);
    }

    /**
     * Requirement: Edit Category via ActionGroup.
     */
    public function test_can_edit_category(): void
    {
        $category = SiteCategory::factory()->create(['site_id' => $this->site->id]);

        Livewire::test(ManageSiteCategories::class, [
            'site' => $this->site->id,
        ])
            ->callTableAction('edit', $category, data: [
                'name' => 'Updated Name',
            ])
            ->assertHasNoTableActionErrors();

        $this->assertEquals('Updated Name', $category->refresh()->name);
    }

    /**
     * Requirement: Delete Category (Soft Delete).
     */
    public function test_can_delete_category(): void
    {
        $category = SiteCategory::factory()->create(['site_id' => $this->site->id]);

        Livewire::test(ManageSiteCategories::class, [
            'site' => $this->site->id,
        ])
            ->callTableAction('delete', $category);

        $this->assertSoftDeleted($category);
    }

    /**
     * Requirement: Tab Filtering.
     */
    public function test_can_filter_categories_by_tabs(): void
    {
        $postCategory = SiteCategory::factory()->create([
            'site_id' => $this->site->id,
            'type' => CategoryType::Post,
        ]);

        $propertyCategory = SiteCategory::factory()->create([
            'site_id' => $this->site->id,
            'type' => CategoryType::Property,
        ]);

        Livewire::test(ManageSiteCategories::class, [
            'site' => $this->site->id,
        ])
            ->set('activeTab', 'post')
            ->assertCanSeeTableRecords([$postCategory])
            ->assertCanNotSeeTableRecords([$propertyCategory]);
    }

    /**
     * Requirement: Site Isolation.
     * User cannot see categories from another site of the SAME company in THIS site's page.
     */
    public function test_site_isolation_within_company(): void
    {
        $otherSite = Site::factory()->create(['company_id' => $this->company->id]);
        $otherCategory = SiteCategory::factory()->create(['site_id' => $otherSite->id]);

        Livewire::test(ManageSiteCategories::class, [
            'site' => $this->site->id,
        ])
            ->assertCanNotSeeTableRecords([$otherCategory]);
    }

    /**
     * Requirement: Company Isolation.
     * User cannot access categories from another company.
     */
    public function test_company_isolation(): void
    {
        $otherCompany = Company::factory()->create(['active' => true]);
        $otherSite = Site::factory()->create(['company_id' => $otherCompany->id]);

        // Attempting to access the page with another company's site ID
        $this->get(SiteCategoryResource::getUrl('index', ['site' => $otherSite->id], panel: 'app'))
            ->assertStatus(403);
    }

    /**
     * Requirement: Activity Logging (BR06).
     */
    public function test_category_crud_is_logged_to_tenant(): void
    {
        Livewire::test(ManageSiteCategories::class, [
            'site' => $this->site->id,
        ])
            ->callAction('create', data: [
                'name' => 'Loggable Category',
                'type' => CategoryType::General,
                'slug' => 'loggable-category',
            ]);

        $this->assertDatabaseHas('activity_log', [
            'company_id' => $this->company->id,
            'log_name' => 'site_category',
            'event' => 'created',
        ]);
    }
}
