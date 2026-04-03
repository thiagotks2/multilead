<?php

namespace Database\Seeders;

use App\Modules\Websites\Models\SiteBannerPlace;
use Illuminate\Database\Seeder;

class SiteBannerPlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Global system places (not linked to any company/site)
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
    }
}
