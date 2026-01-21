<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $unitCost = fake()->numberBetween(100, 2000);

        return [
            'account_id' => Account::factory(),
            'name' => fake()->words(2, true),
            'sku' => strtoupper(fake()->bothify('SKU-####')),
            'description' => fake()->sentence(),
            'unit_cost_cents' => $unitCost,
            'unit_price_cents' => $unitCost + fake()->numberBetween(150, 1500),
        ];
    }
}
