<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Cashier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CashierSalary>
 */
class CashierSalaryFactory extends Factory
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
            'salary_rule_id' => null,
            'amount_cents' => fake()->numberBetween(1000, 50000),
            'currency' => 'PHP',
            'paid_at' => now(),
            'status' => 'paid',
        ];
    }
}
