<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Cashier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'cashier_id' => function (array $attributes) {
                return Cashier::factory()->state([
                    'account_id' => $attributes['account_id'],
                ]);
            },
            'status' => 'completed',
            'total_cents' => fake()->numberBetween(500, 20000),
            'currency' => 'USD',
            'occurred_at' => now(),
        ];
    }
}
