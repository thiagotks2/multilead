<?php

namespace Database\Seeders;

use App\Enums\SiteStatus;
use App\Models\Company;
use App\Models\Site;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company1 = Company::find(1);
        if ($company1) {
            Site::firstOrCreate(
                ['name' => 'Main Website C1', 'company_id' => $company1->id],
                ['canonical_url' => 'company1.com', 'status' => SiteStatus::Production]
            );
        }

        $company2 = Company::find(2);
        if ($company2) {
            Site::firstOrCreate(
                ['name' => 'Main Website C2', 'company_id' => $company2->id],
                ['canonical_url' => 'company2.com', 'status' => SiteStatus::Production]
            );
        }
    }
}
