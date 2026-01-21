<?php

use App\Actions\Inventory\CreateGoodsReceiptAction;
use App\Actions\POS\CompleteSaleAction;
use App\DTOs\GoodsReceiptData;
use App\DTOs\GoodsReceiptItemData;
use App\DTOs\SaleData;
use App\DTOs\SaleItemData;
use App\Models\Account;
use App\Models\Cashier;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('creates inventory movements for completed sales', function () {
    $account = Account::factory()->create();
    $user = User::factory()->create();
    $cashier = Cashier::factory()->create([
        'account_id' => $account->id,
        'user_id' => $user->id,
    ]);
    $product = Product::factory()->create(['account_id' => $account->id]);

    app(CompleteSaleAction::class)->run($account, new SaleData(
        $cashier->id,
        'USD',
        null,
        [new SaleItemData($product->id, 2, 500)],
        null,
    ));

    $movement = InventoryMovement::query()
        ->where('account_id', $account->id)
        ->where('product_id', $product->id)
        ->where('movement_type', 'sale')
        ->first();

    expect($movement)->not->toBeNull()
        ->and($movement->quantity_delta)->toBe(-2);
});

it('rejects completed sales without items', function () {
    $account = Account::factory()->create();
    $user = User::factory()->create();
    $cashier = Cashier::factory()->create([
        'account_id' => $account->id,
        'user_id' => $user->id,
    ]);

    expect(fn () => app(CompleteSaleAction::class)->run($account, new SaleData(
        $cashier->id,
        'USD',
        null,
        [],
        null,
    )))->toThrow(ValidationException::class);
});

it('creates inventory movements for goods receipts', function () {
    $account = Account::factory()->create();
    $product = Product::factory()->create(['account_id' => $account->id]);

    app(CreateGoodsReceiptAction::class)->run($account, new GoodsReceiptData(
        null,
        null,
        null,
        null,
        [new GoodsReceiptItemData($product->id, 5, 250)],
        null,
    ));

    $movement = InventoryMovement::query()
        ->where('account_id', $account->id)
        ->where('product_id', $product->id)
        ->where('movement_type', 'purchase_receipt')
        ->first();

    expect($movement)->not->toBeNull()
        ->and($movement->quantity_delta)->toBe(5)
        ->and($movement->unit_cost_cents)->toBe(250);
});

it('rejects goods receipts without items', function () {
    $account = Account::factory()->create();

    expect(fn () => app(CreateGoodsReceiptAction::class)->run($account, new GoodsReceiptData(
        null,
        null,
        null,
        null,
        [],
        null,
    )))->toThrow(ValidationException::class);
});
