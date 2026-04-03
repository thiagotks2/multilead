<?php

namespace Database\Seeders;

use App\Modules\Websites\Models\SiteBanner;
use App\Modules\Websites\Models\SiteBannerPlace;
use Illuminate\Database\Seeder;

class DevSiteBannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $places = SiteBannerPlace::all();

        foreach ($places as $place) {
            // Create 1-3 test banners per place for diversity
            $numBanners = rand(1, 3);

            for ($i = 1; $i <= $numBanners; $i++) {
                $status = ($i === 1) ? 'Active' : (($i === 2) ? 'Backup' : 'Expired');
                $isActive = ($status !== 'Expired');

                SiteBanner::withTrashed()->updateOrCreate(
                    [
                        'site_banner_place_id' => $place->id,
                        'title' => "Banner {$i} - {$place->name} ({$status})",
                    ],
                    [
                        'description' => "Test dummy banner for place: {$place->name}",
                        'image_path' => "banners/placeholder-{$place->slug}-{$i}.jpg",
                        'link_url' => 'https://multilead.com',
                        'action_label' => 'Click Here',
                        'display_until' => $isActive ? null : now()->subDays(rand(1, 10)),
                        'deleted_at' => null,
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
