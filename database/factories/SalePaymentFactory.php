<?php

namespace Database\Factories;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalePayment>
 */
class SalePaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sale_id' => Sale::factory(),
            'amount_cents' => fake()->numberBetween(500, 20000),
            'currency' => 'PHP',
            'method' => fake()->randomElement(['cash', 'ewallet', 'card']),
            'reference' => strtoupper(fake()->bothify('PAY-####')),
            'paid_at' => now(),
        ];
    }
}
