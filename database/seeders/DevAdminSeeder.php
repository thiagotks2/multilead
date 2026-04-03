<?php

namespace Database\Seeders;

use App\Modules\Identity\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DevAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            Admin::updateOrCreate(
                ['email' => 'admin@admin.com'],
                [
                    'name' => 'Admin Test',
                    'password' => Hash::make('123'),
                    'email_verified_at' => now(),
                ]
            );
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            // If it already exists, that is okay for a seeder.
        }
    }
}
