<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryPurchase>
 */
class InventoryPurchaseFactory extends Factory
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
            'supplier_id' => null,
            'goods_receipt_id' => null,
            'status' => 'paid',
            'total_cents' => fake()->numberBetween(500, 50000),
            'currency' => 'USD',
            'paid_at' => now(),
        ];
    }
}
