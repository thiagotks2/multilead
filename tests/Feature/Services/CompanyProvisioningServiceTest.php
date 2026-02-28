<?php

namespace Tests\Feature\Services;

use App\Models\Company;
use App\Models\Pipeline;
use App\Services\CompanyProvisioningService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyProvisioningServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_provisions_default_crm_architecture_for_company(): void
    {
        // Arrange
        $company = Company::factory()->create();
        $service = new CompanyProvisioningService;

        // Act
        $service->provision($company);

        // Assert
        // 1. Assert Pipeline was created correctly
        $this->assertDatabaseHas('pipelines', [
            'company_id' => $company->id,
            'name' => 'Sales Pipeline',
            'is_default' => true,
        ]);

        $pipeline = Pipeline::where('company_id', $company->id)->first();

        // 2. Assert Stages were created and associated with the Pipeline
        $this->assertCount(5, $pipeline->stages);
    }
}
