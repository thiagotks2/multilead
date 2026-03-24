<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Modules\CRM\Models\LeadSource;
use App\Modules\Identity\Models\Company;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\CRM\Models\LeadSource>
 */
class LeadSourceFactory extends Factory
{
    protected $model = LeadSource::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->word(),
        ];
    }
}
