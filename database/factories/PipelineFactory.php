<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Modules\CRM\Models\Pipeline;
use App\Modules\Identity\Models\Company;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\CRM\Models\Pipeline>
 */
class PipelineFactory extends Factory
{
    protected $model = Pipeline::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => 'Main Pipeline',
            'is_default' => true,
        ];
    }
}
