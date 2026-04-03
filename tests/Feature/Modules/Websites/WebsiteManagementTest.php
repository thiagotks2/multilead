<?php

namespace Tests\Feature\Modules\Websites;

use App\Filament\App\Resources\Websites\Pages\EditWebsite;
use App\Filament\App\Resources\Websites\Pages\ListWebsites;
use App\Filament\App\Resources\Websites\WebsiteResource;
use App\Modules\Identity\Models\Company;
use App\Modules\Identity\Models\User;
use App\Modules\Websites\Enums\SiteStatus;
use App\Modules\Websites\Models\Site;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WebsiteManagementTest extends TestCase
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

        $this->actingAs($this->user, 'user');

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
     * If 1 site exists, label is "Website Settings" and URL is Edit.
     */
    public function test_navigation_redirects_to_edit_when_single_site_exists(): void
    {
        $site = Site::factory()->create(['company_id' => $this->company->id]);

        $this->assertEquals('Website Settings', WebsiteResource::getNavigationLabel());
        $this->assertStringContainsString("/websites/{$site->id}/edit", WebsiteResource::getNavigationUrl());
    }

    /**
     * Requirement: Multiple Sites Navigation
     * If >1 sites exist, label is "My Websites" and URL is Index.
     */
    public function test_navigation_shows_list_when_multiple_sites_exist(): void
    {
        Site::factory()->count(2)->create(['company_id' => $this->company->id]);

        $this->assertEquals('My Websites', WebsiteResource::getNavigationLabel());
        $this->assertStringEndsWith('/websites', WebsiteResource::getNavigationUrl());
    }

    /*
    |--------------------------------------------------------------------------
    | Policy Enforcement (ADR 004 / BR01)
    |--------------------------------------------------------------------------
    */

    /**
     * Requirement: Restricted Creation (App Panel)
     */
    public function test_tenant_cannot_create_website_via_app_panel(): void
    {
        $this->get(WebsiteResource::getUrl('create', panel: 'app'))
            ->assertForbidden();
    }

    /**
     * Requirement: Restricted Deletion (App Panel)
     */
    public function test_tenant_cannot_delete_website_via_app_panel(): void
    {
        $site = Site::factory()->create(['company_id' => $this->company->id]);

        // Should not see delete action in edit page
        Livewire::test(EditWebsite::class, ['record' => $site->id])
            ->assertActionHidden('delete');

        // Should not see delete action in table
        Livewire::test(ListWebsites::class)
            ->assertTableActionDoesNotExist('delete');
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

        Livewire::test(EditWebsite::class, ['record' => $site->id])
            ->assertFormFieldExists('status')
            ->fillForm(['status' => SiteStatus::Inactive])
            ->call('save')
            ->assertHasFormErrors(['status']); // Should fail because inactive is removed from options
    }

    /**
     * Requirement: Read-only Inactive Sites
     * If site is inactive, the central form should be disabled.
     */
    public function test_inactive_website_is_read_only_for_tenants(): void
    {
        $site = Site::factory()->create([
            'company_id' => $this->company->id,
            'status' => SiteStatus::Inactive,
        ]);

        Livewire::test(EditWebsite::class, ['record' => $site->id])
            ->assertFormFieldIsDisabled('name'); // Checking a field inside the disabled form
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

        Livewire::test(EditWebsite::class, ['record' => $site->id])
            ->fillForm(['canonical_url' => 'www.example.com'])
            ->call('save')
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

        Livewire::test(EditWebsite::class, ['record' => $site->id])
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
    public function test_cannot_access_other_company_website(): void
    {
        $otherCompany = Company::factory()->create(['active' => true]);
        $otherSite = Site::factory()->create(['company_id' => $otherCompany->id]);

        $this->get(WebsiteResource::getUrl('edit', ['record' => $otherSite->id], panel: 'app'))
            ->assertStatus(404); // Scoped results usually return 404 in Filament
    }
}
