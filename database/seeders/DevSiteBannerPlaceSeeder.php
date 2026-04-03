<?php

namespace Database\Seeders;

use App\Modules\Websites\Models\Site;
use App\Modules\Websites\Models\SiteBannerPlace;
use Illuminate\Database\Seeder;

class DevSiteBannerPlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Global system places (not linked to any company/site)
        $globalPlaces = [
            [
                'name' => 'Main Top Banner',
                'description' => 'The large banner displayed at the very top of the homepage.',
            ],
            [
                'name' => 'Entry Popup',
                'description' => 'A floating modal/popup that appears as soon as the user enters the site.',
            ],
            [
                'name' => 'Exit Intent',
                'description' => 'A popup that is triggered only when the user moves their mouse to leave the page.',
            ],
        ];

        foreach ($globalPlaces as $place) {
            SiteBannerPlace::withTrashed()->updateOrCreate(
                ['site_id' => null, 'name' => $place['name']],
                ['description' => $place['description'], 'deleted_at' => null]
            );
        }

        // 2. Random places linked to each existing site_id
        $sites = Site::all();

        foreach ($sites as $site) {
            $randomPlaces = [
                'Sidebar Promo',
                'Footer Horizontal',
                'Internal Post Middle',
                'Top Notification Bar',
            ];

            // Select 2 random ones for diversity
            $selected = array_intersect_key($randomPlaces, array_flip((array) array_rand($randomPlaces, 2)));

            foreach ($selected as $name) {
                SiteBannerPlace::withTrashed()->updateOrCreate(
                    ['site_id' => $site->id, 'name' => $name],
                    ['description' => "Test place for site {$site->domain}: {$name}", 'deleted_at' => null]
                );
            }
        }
    }
}
