<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Modules\CRM\Models\Lead;
use App\Modules\Identity\Models\Company;
use App\Modules\Identity\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\CRM\Models\Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'medium' => 'direct',
        ];
    }
}
