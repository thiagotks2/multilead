<?php

namespace Database\Seeders;

use App\Modules\Clients\Models\Client;
use App\Modules\Identity\Models\Company;
use App\Modules\Identity\Models\User;
use App\Support\Phone;
use Illuminate\Database\Seeder;

class DevClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::whereIn('id', [1, 2])->get();

        $companies->each(function (Company $company) {
            $suffix = $company->id === 1 ? 'c1' : 'c2';

            // 1. Shared Clients (5 total)
            for ($i = 1; $i <= 5; $i++) {
                $this->createClient($company->id, null, "Shared Client {$i} {$suffix}", "shared_{$i}_{$suffix}@example.com");
            }

            // 2. User specific clients
            if ($company->id === 1) {
                // User 1 (user@user.com): 0 clients
                // User 2 (user2@user.com): 1 client
                $user2 = User::where('email', 'user2@user.com')->first();
                if ($user2) {
                    $this->createClient($company->id, $user2->id, "User 2 Client C1", "u2_client_c1@example.com");
                }
                // User 3 (user3@user.com): 5 clients
                $user3 = User::where('email', 'user3@user.com')->first();
                if ($user3) {
                    for ($i = 1; $i <= 5; $i++) {
                        $this->createClient($company->id, $user3->id, "User 3 Client {$i} C1", "u3_client_{$i}_c1@example.com");
                    }
                }
            } elseif ($company->id === 2) {
                // User 4 (user4@user.com): 0 clients
                // User 5 (user5@user.com): 1 client
                $user5 = User::where('email', 'user5@user.com')->first();
                if ($user5) {
                    $this->createClient($company->id, $user5->id, "User 5 Client C2", "u5_client_c2@example.com");
                }
                // User 6 (user6@user.com): 5 clients
                $user6 = User::where('email', 'user6@user.com')->first();
                if ($user6) {
                    for ($i = 1; $i <= 5; $i++) {
                        $this->createClient($company->id, $user6->id, "User 6 Client {$i} C2", "u6_client_{$i}_c2@example.com");
                    }
                }
            }
        });
    }

    private function createClient(int $companyId, ?int $userId, string $name, string $email): void
    {
        $phoneRaw = '419' . rand(10000000, 99999999);
        $phone = Phone::toDatabase($phoneRaw);

        Client::withTrashed()->updateOrCreate(
            ['email' => $email],
            [
                'company_id' => $companyId,
                'user_id' => $userId,
                'name' => $name,
                'phone' => $phone,
                'deleted_at' => null,
            ]
        );
    }
}
