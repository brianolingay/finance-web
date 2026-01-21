<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('creates the financial tracker tables', function () {
    expect(Schema::hasTable('accounts'))->toBeTrue()
        ->and(Schema::hasTable('account_members'))->toBeTrue()
        ->and(Schema::hasTable('transactions'))->toBeTrue()
        ->and(Schema::hasTable('categories'))->toBeTrue()
        ->and(Schema::hasTable('expenses'))->toBeTrue()
        ->and(Schema::hasTable('incomes'))->toBeTrue()
        ->and(Schema::hasTable('budgets'))->toBeTrue()
        ->and(Schema::hasTable('cashiers'))->toBeTrue()
        ->and(Schema::hasTable('salary_rules'))->toBeTrue()
        ->and(Schema::hasTable('cashier_salaries'))->toBeTrue()
        ->and(Schema::hasTable('sales'))->toBeTrue()
        ->and(Schema::hasTable('sale_items'))->toBeTrue()
        ->and(Schema::hasTable('sale_payments'))->toBeTrue()
        ->and(Schema::hasTable('products'))->toBeTrue()
        ->and(Schema::hasTable('inventory_movements'))->toBeTrue()
        ->and(Schema::hasTable('suppliers'))->toBeTrue()
        ->and(Schema::hasTable('goods_receipts'))->toBeTrue()
        ->and(Schema::hasTable('goods_receipt_items'))->toBeTrue()
        ->and(Schema::hasTable('inventory_purchases'))->toBeTrue();
});
