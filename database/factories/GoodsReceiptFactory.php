<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GoodsReceipt>
 */
class GoodsReceiptFactory extends Factory
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
            'status' => 'received',
            'reference' => strtoupper(fake()->bothify('GR-####')),
            'received_at' => now(),
            'notes' => fake()->sentence(),
        ];
    }
}
