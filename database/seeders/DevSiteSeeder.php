<?php

namespace Database\Seeders;

use App\Modules\Identity\Models\Company;
use App\Modules\Websites\Enums\SiteStatus;
use App\Modules\Websites\Models\Site;
use Illuminate\Database\Seeder;

class DevSiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $c1 = Company::where('id', 1)->first();
        $c2 = Company::where('id', 2)->first();

        if ($c1) {
            $this->createSite($c1->id, 'Main Website C1', 'company1.com');
        }

        if ($c2) {
            $this->createSite($c2->id, 'Main Website C2', 'company2.com');
            $this->createSite($c2->id, 'Secondary Website C2', 'secondary.company2.com');
            $this->createSite($c2->id, 'Landing Page C2', 'landing.company2.com');
        }
    }

    private function createSite(int $companyId, string $name, string $canonicalUrl): void
    {
        Site::withTrashed()->updateOrCreate(
            [
                'name' => $name,
                'company_id' => $companyId,
                'canonical_url' => $canonicalUrl,
                'status' => SiteStatus::Production,
                'deleted_at' => null,
            ]
        );
    }
}
