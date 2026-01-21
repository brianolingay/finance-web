<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
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
            'category_id' => null,
            'description' => fake()->sentence(),
            'status' => 'posted',
            'amount_cents' => fake()->numberBetween(100, 10000),
            'currency' => 'USD',
            'occurred_at' => now(),
            'paid_at' => null,
        ];
    }
}
