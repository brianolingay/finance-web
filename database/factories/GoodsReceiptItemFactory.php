<?php

namespace Database\Factories;

use App\Models\GoodsReceipt;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GoodsReceiptItem>
 */
class GoodsReceiptItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 10);
        $unitCost = fake()->numberBetween(100, 1500);

        return [
            'goods_receipt_id' => GoodsReceipt::factory(),
            'product_id' => Product::factory(),
            'quantity' => $quantity,
            'unit_cost_cents' => $unitCost,
            'line_total_cents' => $quantity * $unitCost,
        ];
    }
}
