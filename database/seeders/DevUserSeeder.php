<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DevUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('123');

        $c1 = DB::table('companies')->where('id', 1)->first();
        $c2 = DB::table('companies')->where('id', 2)->first();
        $c3 = DB::table('companies')->where('id', 3)->first();

        if ($c1) {
            $this->createUser($c1->id, 'User 1 C1', 'user@user.com', $password);
            $this->createUser($c1->id, 'User 2 C1', 'user2@user.com', $password);
            $this->createUser($c1->id, 'User 3 C1', 'user3@user.com', $password);
        }

        if ($c2) {
            $this->createUser($c2->id, 'User 4 C2', 'user4@user.com', $password);
            $this->createUser($c2->id, 'User 5 C2', 'user5@user.com', $password);
            $this->createUser($c2->id, 'User 6 C2', 'user6@user.com', $password);
        }

        if ($c3) {
            $this->createUser($c3->id, 'User 7 C3', 'user7@user.com', $password);
        }
    }

    private function createUser(int $companyId, string $name, string $email, string $password): void
    {
        DB::table('users')->upsert(
            [
                'name' => $name,
                'email' => $email,
                'company_id' => $companyId,
                'password' => $password,
                'active' => true,
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            ['email'],
            ['name', 'company_id', 'password', 'active', 'deleted_at', 'updated_at']
        );
    }
}
