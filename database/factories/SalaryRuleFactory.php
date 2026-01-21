<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalaryRule>
 */
class SalaryRuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'account_id' => Account::factory(),
            'name' => fake()->words(2, true),
            'type' => 'fixed',
            'fixed_cents' => fake()->numberBetween(1000, 50000),
            'commission_bps' => null,
            'currency' => 'PHP',
            'is_active' => true,
        ];
    }
}
