<?php

namespace Database\Factories;

use App\Modules\Websites\Models\Site;
use App\Modules\Websites\Models\SitePostCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Websites\Models\SitePostCategory>
 */
class SitePostCategoryFactory extends Factory
{
    protected $model = SitePostCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'site_id' => Site::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
        ];
    }
}
