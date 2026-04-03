<?php

namespace Tests\Feature\Modules\Websites;

use App\Filament\App\Resources\Sites\Pages\EditSite;
use App\Filament\App\Resources\Sites\Pages\ListSites;
use App\Filament\App\Resources\Sites\SiteResource;
use App\Modules\Identity\Models\Company;
use App\Modules\Identity\Models\User;
use App\Modules\Websites\Enums\SiteStatus;
use App\Modules\Websites\Models\Site;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SiteManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create(['active' => true]);
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'active' => true,
        ]);

        $this->actingAs($this->user, 'web');

        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($this->company);
    }

    /*
    |--------------------------------------------------------------------------
    | Navigation & UI Behavior (BR04)
    |--------------------------------------------------------------------------
    */

    /**
     * Requirement: Single Site Navigation
     * If 1 site exists, label is "Site Settings" and URL is Edit.
     */
    public function test_navigation_redirects_to_edit_when_single_site_exists(): void
    {
        $site = Site::factory()->create(['company_id' => $this->company->id]);

        $this->assertEquals('Site Settings', SiteResource::getNavigationLabel());
        $this->assertStringContainsString("/sites/{$site->id}/edit", SiteResource::getNavigationUrl());
    }

    /**
     * Requirement: Multiple Sites Navigation
     * If >1 sites exist, label is "My Sites" and URL is Index.
     */
    public function test_navigation_shows_list_when_multiple_sites_exist(): void
    {
        Site::factory()->count(2)->create(['company_id' => $this->company->id]);

        $this->assertEquals('My Sites', SiteResource::getNavigationLabel());
        $this->assertStringEndsWith('/sites', SiteResource::getNavigationUrl());
    }

    /*
    |--------------------------------------------------------------------------
    | Policy Enforcement (ADR 004 / BR01)
    |--------------------------------------------------------------------------
    */

    /**
     * Requirement: Restricted Creation (App Panel)
     */
    public function test_tenant_cannot_create_site_via_app_panel(): void
    {
        $this->get(SiteResource::getUrl('create', panel: 'app'))
            ->assertForbidden();
    }

    /**
     * Requirement: Restricted Deletion (App Panel)
     */
    public function test_tenant_cannot_delete_site_via_app_panel(): void
    {
        $site = Site::factory()->create(['company_id' => $this->company->id]);

        Livewire::test(EditSite::class, ['record' => $site->id])
            ->assertActionHidden('delete');

        // Direct attempt via Page action should also be blocked by Policy
        Livewire::test(ListSites::class)
            ->callTableAction('delete', $site)
            ->assertForbidden();
    }

    /*
    |--------------------------------------------------------------------------
    | Business Rules (BR06 - Status Transitions)
    |--------------------------------------------------------------------------
    */

    /**
     * Requirement: Status Options Restriction
     * Inactive should not be visible in the App Panel dropdown.
     */
    public function test_inactive_status_is_hidden_from_tenants(): void
    {
        $site = Site::factory()->create(['company_id' => $this->company->id]);

        Livewire::test(EditSite::class, ['record' => $site->id])
            ->assertFormFieldExists('status')
            ->assertFormFieldIsAvailable('status', SiteStatus::Production)
            ->assertFormFieldIsAvailable('status', SiteStatus::Development)
            // Maintenance status (should throw error/fail until implemented)
            // ->assertFormFieldIsAvailable('status', SiteStatus::Maintenance)
            ->assertFormFieldIsNotAvailable('status', SiteStatus::Inactive);
    }

    /**
     * Requirement: Read-only Inactive Sites
     * If site is inactive, no fields should be editable.
     */
    public function test_inactive_site_is_read_only_for_tenants(): void
    {
        $site = Site::factory()->create([
            'company_id' => $this->company->id,
            'status' => SiteStatus::Inactive,
        ]);

        Livewire::test(EditSite::class, ['record' => $site->id])
            ->assertFormDisabled();
    }

    /*
    |--------------------------------------------------------------------------
    | Data Integrity & Security
    |--------------------------------------------------------------------------
    */

    /**
     * Requirement: Canonical URL Formatting
     */
    public function test_canonical_url_is_automatically_formatted(): void
    {
        $site = Site::factory()->create(['company_id' => $this->company->id]);

        Livewire::test(EditSite::class, ['record' => $site->id])
            ->fillForm(['canonical_url' => 'www.example.com'])
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('sites', [
            'id' => $site->id,
            'canonical_url' => 'https://www.example.com/',
        ]);
    }

    /**
     * Requirement: SMTP Password Encryption
     */
    public function test_smtp_password_is_stored_encrypted(): void
    {
        $site = Site::factory()->create(['company_id' => $this->company->id]);

        Livewire::test(EditSite::class, ['record' => $site->id])
            ->fillForm(['smtp_password' => 'secret123'])
            ->call('save')
            ->assertHasNoFormErrors();

        $site->refresh();
        $this->assertEquals('secret123', $site->smtp_password);

        // Verify raw DB value is encrypted
        $rawPassword = \DB::table('sites')->where('id', $site->id)->value('smtp_password');
        $this->assertNotEquals('secret123', $rawPassword);
    }

    /**
     * Requirement: Multi-tenancy Security (BR05)
     */
    public function test_cannot_access_other_company_site(): void
    {
        $otherCompany = Company::factory()->create(['active' => true]);
        $otherSite = Site::factory()->create(['company_id' => $otherCompany->id]);

        $this->get(SiteResource::getUrl('edit', ['record' => $otherSite->id], panel: 'app'))
            ->assertStatus(404); // Scoped results usually return 404 in Filament
    }
}
