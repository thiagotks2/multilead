<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Modules\Clients\Models\Client;
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
            $companies = Company::factory(2)->create();
        }

        foreach ($companies as $company) {
            // 1. Company Clients (Shared)
            Client::factory(5)->create([
                'company_id' => $company->id,
                'user_id' => null,
            ]);

            // 2. Exclusive Clients (Assigned to specific users)
            $users = $company->users;

            if ($users->isNotEmpty()) {
                foreach ($users as $user) {
                    Client::factory(3)->exclusive($user)->create([
                        'company_id' => $company->id,
                    ]);
                }
            }
        }
    }
}
