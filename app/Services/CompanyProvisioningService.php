<?php

namespace App\Services;

use App\Models\Company;

class CompanyProvisioningService
{
    /**
     * Provisions the default architecture (Pipelines, Stages, etc.) for a newly created Company.
     */
    public function provision(Company $company): void
    {
        $this->provisionDefaultCRM($company);
    }

    /**
     * Creates a Default Pipeline and essential Stages for the company's CRM.
     */
    protected function provisionDefaultCRM(Company $company): void
    {
        // 1. Create the default Pipeline
        $pipeline = $company->pipelines()->create([
            'name' => 'Sales Pipeline',
            'is_default' => true,
        ]);

        // 2. Create the default Stages
        $pipeline->stages()->createMany([
            [
                'name' => 'Inbox',
                'color' => '#64748b', // Slate 500
                'order_column' => 1,
                'is_default' => true,
                'is_visible' => true,
            ],
            [
                'name' => 'Contacted',
                'color' => '#3b82f6', // Blue 500
                'order_column' => 2,
                'is_default' => false,
                'is_visible' => true,
            ],
            [
                'name' => 'Proposal Sent',
                'color' => '#eab308', // Yellow 500
                'order_column' => 3,
                'is_default' => false,
                'is_visible' => true,
            ],
            [
                'name' => 'Won',
                'color' => '#22c55e', // Green 500
                'order_column' => 4,
                'is_default' => false,
                'is_visible' => true,
            ],
            [
                'name' => 'Lost',
                'color' => '#ef4444', // Red 500
                'order_column' => 5,
                'is_default' => false,
                'is_visible' => false, // Hidden by default from the primary active board
            ],
        ]);
    }
}
