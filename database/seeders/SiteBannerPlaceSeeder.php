<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Models\SiteBannerPlace;
use Illuminate\Database\Seeder;

class SiteBannerPlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Global system places (not linked to any company/site)
        SiteBannerPlace::firstOrCreate([
            'site_id' => null,
            'name' => 'Main Top Banner',
        ], [
            'description' => 'The large banner displayed at the very top of the homepage.',
        ]);

        SiteBannerPlace::firstOrCreate([
            'site_id' => null,
            'name' => 'Entry Popup',
        ], [
            'description' => 'A floating modal/popup that appears as soon as the user enters the site.',
        ]);

        SiteBannerPlace::firstOrCreate([
            'site_id' => null,
            'name' => 'Exit Intent',
        ], [
            'description' => 'A popup that is triggered only when the user moves their mouse to leave the page.',
        ]);
    }
}
