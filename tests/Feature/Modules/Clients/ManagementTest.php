<?php

namespace Tests\Feature\Modules\Clients;

use App\Filament\App\Resources\Clients\ClientResource;
use App\Filament\App\Resources\Clients\Pages\CreateClient;
use App\Filament\App\Resources\Clients\Pages\ListClients;
use App\Filament\App\Resources\Clients\Pages\ViewClient;
use App\Modules\Clients\Models\Client;
use App\Modules\Identity\Models\Company;
use App\Modules\Identity\Models\User;
use Filament\Facades\Filament;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManagementTest extends TestCase
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
    | Infrastructure & Domain Rules
    |--------------------------------------------------------------------------
    */

    /**
     * Requirement: Validation (Mandatory Tenant)
     * Ensuring client creation fails if company_id is missing.
     */
    public function test_client_creation_requires_company_id(): void
    {
        $this->expectException(QueryException::class);

        // Using create() here instead of factory() to prevent unintended company generation
        Client::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    /**
     * Requirement: Integrity (Soft Deletes)
     * Verifying that clients are soft deleted and can be restored.
     */
    public function test_clients_can_be_soft_deleted(): void
    {
        $client = Client::factory()->create(['company_id' => $this->company->id]);
        $client->delete();

        $this->assertSoftDeleted('clients', ['id' => $client->id]);

        $client->restore();
        $this->assertDatabaseHas('clients', ['id' => $client->id, 'deleted_at' => null]);
    }

    /*
    |--------------------------------------------------------------------------
    | Feature Scenarios (Filament Resource)
    |--------------------------------------------------------------------------
    */

    /**
     * Scenario: Access Control & Resource Presence
     */
    public function test_can_access_client_resource(): void
    {
        $this->get(ClientResource::getUrl('index', panel: 'app'))
            ->assertSuccessful();
    }

    /**
     * Scenario: Creation of a Tenant "Company Client" (Section 5.1)
     * Verifying a client can be created linked to a company without an assigned user.
     */
    public function test_can_create_a_tenant_company_client_via_form(): void
    {
        Livewire::test(CreateClient::class)
            ->fillForm([
                'name' => 'John Tenant',
                'email' => 'tenant@example.com',
                'phone' => '41991234567',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('clients', [
            'name' => 'John Tenant',
            'email' => 'tenant@example.com',
            'user_id' => null,
            'company_id' => $this->company->id,
        ]);

        // Requirement: Observability (BR03)
        $this->assertDatabaseHas('activity_log', [
            'subject_type' => Client::class,
            'description' => 'created',
            'company_id' => $this->company->id,
        ]);
    }

    /**
     * Scenario: Creation of an "Exclusive Client" (Section 5.2)
     * Verifying creation of an exclusive client linked to both a company and a user.
     */
    public function test_can_create_an_exclusive_client_via_form(): void
    {
        Livewire::test(CreateClient::class)
            ->fillForm([
                'name' => 'Exclusive John',
                'email' => 'exclusive@example.com',
                'phone' => '41991234567',
                'user_id' => $this->user->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('clients', [
            'name' => 'Exclusive John',
            'email' => 'exclusive@example.com',
            'phone' => '5541991234567', // Normalized in database
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Requirement: Phone Integrity (BR04)
     * Verifying that the phone input correctly validates according to the new logic.
     */
    public function test_cannot_create_client_with_invalid_phone_via_form(): void
    {
        Livewire::test(CreateClient::class)
            ->fillForm([
                'name' => 'Faulty Phone',
                'email' => 'faulty@example.com',
                'phone' => '123', // Invalid
            ])
            ->call('create')
            ->assertHasFormErrors(['phone']);
    }

    /**
     * Scenario: Constraint Failure: Scope Uniqueness (Section 5.3)
     * Preventing the creation of duplicate emails within the same company.
     */
    public function test_cannot_create_duplicate_email_in_same_company_via_form(): void
    {
        Client::factory()->create([
            'email' => 'duplicate@example.com',
            'company_id' => $this->company->id,
        ]);

        Livewire::test(CreateClient::class)
            ->fillForm([
                'name' => 'Duplicate',
                'email' => 'duplicate@example.com',
            ])
            ->call('create')
            ->assertHasFormErrors(['email']);
    }

    /**
     * Scenario: Cross-Tenant Schema Integrity (Section 5.4)
     * Confirming that the same email can exist in two different companies without conflict.
     */
    public function test_can_create_same_email_in_different_company_via_form(): void
    {
        $otherCompany = Company::factory()->create(['active' => true]);
        Client::factory()->create([
            'email' => 'shared@example.com',
            'company_id' => $otherCompany->id,
        ]);

        Livewire::test(CreateClient::class)
            ->fillForm([
                'name' => 'New User',
                'email' => 'shared@example.com',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseCount('clients', 2);
    }

    /**
     * Scenario: List View Tabs & Filters (Section 4)
     */
    public function test_can_filter_clients_by_tabs(): void
    {
        $myClient = Client::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        $otherClient = Client::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => null,
        ]);

        Livewire::test(ListClients::class)
            ->set('activeTab', 'My Clients')
            ->assertCanSeeTableRecords([$myClient])
            ->assertCanNotSeeTableRecords([$otherClient])
            ->set('activeTab', 'All')
            ->assertCanSeeTableRecords([$myClient, $otherClient]);
    }

    /**
     * Scenario: View Page Tabs (Section 4)
     * Verifying the view page layout includes mandatory Information and Timeline tabs.
     */
    public function test_view_page_has_required_tabs(): void
    {
        $client = Client::factory()->create(['company_id' => $this->company->id]);

        Livewire::test(ViewClient::class, [
            'record' => $client->id,
        ])
            ->assertSuccessful()
            ->assertSee('Details')
            ->assertSee('Timeline');
    }
}
