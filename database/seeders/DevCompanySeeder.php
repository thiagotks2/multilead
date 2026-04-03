<?php

namespace Database\Seeders;

use App\Modules\Identity\Models\Company;
use App\Modules\Identity\Services\CompanyProvisioningService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DevCompanySeeder extends Seeder
{
    public function __construct(protected CompanyProvisioningService $provisioningService)
    {
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'id' => 1,
                'name' => 'Company 1',
                'active' => true,
                'document_type' => 'CNPJ',
                'document_number' => '12.345.678/0001-90',
            ],
            [
                'id' => 2,
                'name' => 'Company 2',
                'active' => true,
                'document_type' => 'CNPJ',
                'document_number' => '98.765.432/0001-01',
            ],
            [
                'id' => 3,
                'name' => 'Company 3',
                'active' => true,
                'document_type' => 'CNPJ',
                'document_number' => '11.111.111/0001-11',
            ],
        ];

        foreach ($companies as $data) {
            DB::table('companies')->upsert(
                array_merge($data, [
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]),
                ['id'],
                ['name', 'active', 'document_type', 'document_number', 'deleted_at', 'updated_at']
            );

            // Provision the company (Pipelines, Stages, etc.)
            $company = Company::find($data['id']);
            if ($company) {
                $this->provisioningService->provision($company);
            }
        }
    }
}
