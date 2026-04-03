<?php

namespace Database\Seeders;

use App\Modules\CRM\Models\LeadSource;
use App\Modules\Identity\Models\Company;
use Illuminate\Database\Seeder;

class DevLeadSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::whereIn('name', ['Company 1', 'Company 2'])->get();

        foreach ($companies as $company) {
            $devSources = [
                'Local Event SP',
                'Partner Agency K',
                'Billboard Main Av',
                'Flyer Neighborhood X',
            ];

            foreach ($devSources as $sourceName) {
                LeadSource::withTrashed()->updateOrCreate(
                    ['name' => $sourceName, 'company_id' => $company->id],
                    ['deleted_at' => null]
                );
            }
        }
    }
}
