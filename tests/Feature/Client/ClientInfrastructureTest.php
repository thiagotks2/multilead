<?php

namespace Tests\Feature\Client;

use App\Models\Client;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientInfrastructureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Requirement: Creation (Tenant)
     * Verify a client can be created when linked to a company without an assigned user.
     */
    public function test_client_can_be_created_linked_to_company_without_user(): void
    {
        $company = Company::factory()->create();

        $client = Client::factory()->create([
            'name' => 'John Doe',
            'company_id' => $company->id,
            'user_id' => null,
            'address' => ['city' => 'New York'],
        ]);

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'John Doe',
            'company_id' => $company->id,
            'user_id' => null,
        ]);
    }

    /**
     * Requirement: Validation (Mandatory Tenant)
     * Ensure client creation fails if company_id is missing.
     */
    public function test_client_creation_requires_company_id(): void
    {
        $this->expectException(QueryException::class);

        // Using create() instead of factory() to prevent unintended company generation
        Client::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    /**
     * Requirement: Creation (Exclusive)
     * Verify creation of an exclusive client linked to both a company and a user.
     */
    public function test_exclusive_client_can_be_created_linked_to_company_and_user(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);

        $client = Client::factory()->exclusive($user)->create([
            'company_id' => $company->id,
        ]);

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'company_id' => $company->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Requirement: Integrity (Scoped Uniqueness)
     * Prevent the creation of duplicate emails within the same company.
     */
    public function test_client_email_must_be_unique_within_the_same_company(): void
    {
        $company = Company::factory()->create();

        Client::factory()->create([
            'email' => 'duplicate@example.com',
            'company_id' => $company->id,
        ]);

        $this->expectException(QueryException::class);

        Client::factory()->create([
            'email' => 'duplicate@example.com',
            'company_id' => $company->id,
        ]);
    }

    /**
     * Requirement: Integrity (Cross-Tenant)
     * Confirm that the same email can exist in two different companies without conflict.
     */
    public function test_client_email_can_be_duplicated_across_different_companies(): void
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        $client1 = Client::factory()->create([
            'email' => 'same@example.com',
            'company_id' => $company1->id,
        ]);

        $client2 = Client::factory()->create([
            'email' => 'same@example.com',
            'company_id' => $company2->id,
        ]);

        $this->assertDatabaseHas('clients', [
            'id' => $client1->id,
            'company_id' => $company1->id,
        ]);

        $this->assertDatabaseHas('clients', [
            'id' => $client2->id,
            'company_id' => $company2->id,
        ]);
    }

    /**
     * Requirement: Observability
     * Verify that an entry is recorded in the activity log upon every create event.
     */
    public function test_client_actions_are_logged_in_activity_log(): void
    {
        $company = Company::factory()->create();

        $client = Client::factory()->create([
            'company_id' => $company->id,
        ]);

        $this->assertDatabaseHas('activity_log', [
            'subject_id' => $client->id,
            'subject_type' => Client::class,
            'description' => 'created',
            'company_id' => $company->id,
        ]);
    }

    /**
     * Requirement: Integrity (Soft Deletes)
     * Verifying that clients are soft deleted and can be restored.
     */
    public function test_clients_can_be_soft_deleted(): void
    {
        $company = Company::factory()->create();
        $client = Client::factory()->create(['company_id' => $company->id]);

        $client->delete();

        $this->assertSoftDeleted('clients', ['id' => $client->id]);

        $client->restore();

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'deleted_at' => null,
        ]);
    }
}
