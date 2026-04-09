<?php

namespace Database\Seeders;

use App\Modules\Websites\Enums\BannerType;
use App\Modules\Websites\Models\Site;
use App\Modules\Websites\Models\SiteBanner;
use Illuminate\Database\Seeder;

class DevSiteBannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sites = Site::all();

        if ($sites->isEmpty()) {
            return;
        }

        foreach ($sites as $site) {
            foreach (BannerType::cases() as $type) {
                // Create 1-2 test banners per type for each site
                $numBanners = rand(1, 2);

                for ($i = 1; $i <= $numBanners; $i++) {
                    SiteBanner::factory()->create([
                        'site_id' => $site->id,
                        'type' => $type,
                        'title' => "Banner {$i} - {$type->getLabel()} - {$site->name}",
                        'image_path' => fake()->uuid().'.jpg',
                    ]);
                }
            }
        }
    }
}
