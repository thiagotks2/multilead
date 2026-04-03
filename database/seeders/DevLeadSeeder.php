<?php

namespace Database\Seeders;

use App\Modules\CRM\Enums\LeadMedium;
use App\Modules\CRM\Models\Lead;
use App\Modules\CRM\Models\LeadSource;
use App\Modules\CRM\Models\Pipeline;
use App\Modules\Identity\Models\Company;
use App\Modules\Identity\Models\User;
use App\Support\Phone;
use Illuminate\Database\Seeder;

class DevLeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::whereIn('id', [1, 2])->get();

        foreach ($companies as $company) {
            $suffix = $company->id === 1 ? 'c1' : 'c2';
            
            // Get default pipeline and its default stage (Provisioned by CompanyProvisioningService)
            $pipeline = Pipeline::where('company_id', $company->id)->where('is_default', true)->first();
            if (!$pipeline) continue;
            
            $defaultStage = $pipeline->stages()->where('is_default', true)->first();
            if (!$defaultStage) continue;

            $users = User::where('company_id', $company->id)->get();
            $sources = LeadSource::where('company_id', $company->id)->get();

            for ($i = 1; $i <= 10; $i++) {
                $hasSource = rand(0, 1);
                $hasUser = rand(0, 1);
                $isPaid = rand(0, 1);
                
                $phoneRaw = '119' . rand(10000000, 99999999);
                $phone = Phone::toDatabase($phoneRaw);

                Lead::withTrashed()->updateOrCreate(
                    ['email' => "lead_{$i}_{$suffix}@example.com"],
                    [
                        'company_id' => $company->id,
                        'lead_source_id' => ($hasSource && $sources->isNotEmpty()) ? $sources->random()->id : null,
                        'user_id' => ($hasUser && $users->isNotEmpty()) ? $users->random()->id : null,
                        'pipeline_stage_id' => $defaultStage->id,
                        'name' => "Lead {$i} {$suffix}",
                        'phone' => $phone,
                        'medium' => $isPaid ? LeadMedium::Paid : LeadMedium::Organic,
                        'message' => "This is a test lead {$i} for {$company->name}.",
                        'deleted_at' => null,
                    ]
                );
            }
        }
    }
}
