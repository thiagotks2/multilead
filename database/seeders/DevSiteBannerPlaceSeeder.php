<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Models\SiteBannerPlace;
use Illuminate\Database\Seeder;

class DevSiteBannerPlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sites = Site::all();

        foreach ($sites as $site) {

            SiteBannerPlace::firstOrCreate([
                'site_id' => $site->id,
                'name' => 'Sidebar Promotion',
            ], [
                'description' => 'A smaller banner placed in the right or left sidebar of internal pages.',
            ]);
        }
    }
}
