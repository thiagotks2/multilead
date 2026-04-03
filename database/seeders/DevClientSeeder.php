<?php

namespace Database\Seeders;

use App\Modules\Clients\Models\Client;
use App\Modules\Identity\Models\Company;
use App\Modules\Identity\Models\User;
use Illuminate\Database\Seeder;

class DevClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        if ($companies->isEmpty()) {
            $companies = collect([
                Company::factory()->create(['name' => 'Default Company', 'active' => true]),
            ]);
        }

        $companies->each(function (Company $company) {
            // Shared/Company clients (Section 5.1)
            Client::factory(5)->create([
                'company_id' => $company->id,
                'user_id' => null,
            ]);

            // Exclusive clients for each user in the company (Section 5.2)
            $company->users->each(function (User $user) use ($company) {
                Client::factory(3)->create([
                    'company_id' => $company->id,
                    'user_id' => $user->id,
                ]);
            });
        });
    }
}
