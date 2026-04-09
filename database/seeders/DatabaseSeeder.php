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
        if (app()->environment('local', 'testing', 'development')) {
            $this->call([
                DevAdminSeeder::class,
                DevCompanySeeder::class,
                DevUserSeeder::class,
                DevSiteSeeder::class,
                DevSiteBannerSeeder::class,
                DevLeadSourceSeeder::class,
                DevClientSeeder::class,
                DevLeadSeeder::class,
                DevBlogSeeder::class,
            ]);
        } else {
            $this->call([
                AdminSeeder::class,
            ]);
        }
    }
}
