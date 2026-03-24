<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Modules\CRM\Models\Pipeline;
use App\Modules\CRM\Models\PipelineStage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\CRM\Models\PipelineStage>
 */
class PipelineStageFactory extends Factory
{
    protected $model = PipelineStage::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pipeline_id' => Pipeline::factory(),
            'name' => fake()->word(),
            'color' => fake()->hexColor(),
            'order_column' => 0,
        ];
    }
}
