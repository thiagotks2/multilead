<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Modules\Identity\Models\Company;
use App\Modules\Websites\Models\Site;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Websites\Models\Site>
 */
class SiteFactory extends Factory
{
    protected $model = Site::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->domainName(),
            'status' => 'development',
        ];
    }
}
