<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            ['active' => true, 'name' => 'User 1', 'company_id' => 1, 'email' => 'user@user.com', 'password' => Hash::make('123')],
            ['active' => true, 'name' => 'User 2', 'company_id' => 2, 'email' => 'user2@user.com', 'password' => Hash::make('123')],
        ]);
    }
}
