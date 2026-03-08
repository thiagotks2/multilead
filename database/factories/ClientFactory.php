<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Modules\Clients\Models\Client;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Clients\Models\Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'user_id' => null,
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'notes' => fake()->paragraph(),
            'address' => [
                'country' => 'Brasil',
                'state' => fake()->state(),
                'city' => fake()->city(),
                'neighborhood' => fake()->word(),
                'street' => fake()->streetName(),
                'number' => fake()->buildingNumber(),
                'complement' => fake()->secondaryAddress(),
                'zip_code' => fake()->postcode(),
            ],
            'profile_data' => [
                'personal_income' => fake()->numberBetween(3000, 20000),
                'family_income' => fake()->numberBetween(5000, 35000),
                'purchase_intent' => fake()->randomElement(['low', 'medium', 'high', 'immediate']),
                'preferences' => fake()->words(3),
            ],
        ];
    }

    /**
     * Indicate that the client is exclusive to a user.
     */
    public function exclusive(?User $user = null): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id ?? User::factory(),
        ]);
    }
}
