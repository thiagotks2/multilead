<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seeders that generate dummy data for development
        if (app()->environment('local', 'testing', 'development')) {
            $this->call([
                AdminSeeder::class,
                CompanySeeder::class,
                SiteSeeder::class,
                UserSeeder::class,
                LeadSourceSeeder::class,
                DevLeadSourceSeeder::class,
                DevPipelineSeeder::class,
                DevLeadSeeder::class,
            ]);
        }

        if (app()->environment('production')) {
            $this->call([
                AdminSeeder::class,
                LeadSourceSeeder::class,
            ]);
        }
    }
}
