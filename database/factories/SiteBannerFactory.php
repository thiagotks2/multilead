<?php

namespace Database\Factories;

use App\Modules\Websites\Enums\BannerType;
use App\Modules\Websites\Models\Site;
use App\Modules\Websites\Models\SiteBanner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Websites\Models\SiteBanner>
 */
class SiteBannerFactory extends Factory
{
    protected $model = SiteBanner::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'site_id' => Site::factory(),
            'type' => fake()->randomElement(BannerType::cases()),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'image_path' => fake()->uuid().'.jpg',
            'link_url' => fake()->url(),
            'action_label' => 'Learn More',
            'display_until' => null,
        ];
    }
}
