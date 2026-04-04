<?php

namespace Database\Factories;

use App\Modules\Websites\Enums\CategoryType;
use App\Modules\Websites\Models\Site;
use App\Modules\Websites\Models\SiteCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Websites\Models\SiteCategory>
 */
class SiteCategoryFactory extends Factory
{
    protected $model = SiteCategory::class;

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
            'type' => CategoryType::General,
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
        ];
    }

    public function post(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => CategoryType::Post,
        ]);
    }

    public function property(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => CategoryType::Property,
        ]);
    }
}
