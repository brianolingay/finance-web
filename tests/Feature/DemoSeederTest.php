<?php

use App\Models\Account;
use App\Models\CashierSalary;
use App\Models\Expense;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\Income;
use App\Models\InventoryMovement;
use App\Models\InventoryPurchase;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Transaction;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('seeds demo data for manual testing', function () {
    $this->seed(DemoSeeder::class);

    $owners = [
        [
            'finance' => 'Owner One Finance',
            'pos' => 'Owner One Store',
        ],
        [
            'finance' => 'Owner Two Finance',
            'pos' => 'Owner Two Store',
        ],
    ];

    foreach ($owners as $owner) {
        $financeAccount = Account::query()
            ->where('name', $owner['finance'])
            ->where('type', 'finance')
            ->firstOrFail();

        $posAccount = Account::query()
            ->where('name', $owner['pos'])
            ->where('type', 'pos')
            ->firstOrFail();

        expect(Income::query()->where('account_id', $financeAccount->id)->count())->toBe(50)
            ->and(Expense::query()->where('account_id', $financeAccount->id)->count())->toBe(50)
            ->and(Transaction::query()->where('account_id', $financeAccount->id)->count())->toBe(100);

        expect(Product::query()->where('account_id', $posAccount->id)->count())->toBe(50)
            ->and(GoodsReceipt::query()->where('account_id', $posAccount->id)->count())->toBe(50)
            ->and(GoodsReceiptItem::query()->whereIn('goods_receipt_id', GoodsReceipt::query()->where('account_id', $posAccount->id)->select('id'))->count())->toBe(173)
            ->and(InventoryPurchase::query()->where('account_id', $posAccount->id)->count())->toBe(25)
            ->and(Sale::query()->where('account_id', $posAccount->id)->count())->toBe(50)
            ->and(SaleItem::query()->whereIn('sale_id', Sale::query()->where('account_id', $posAccount->id)->select('id'))->count())->toBe(149)
            ->and(CashierSalary::query()->where('account_id', $posAccount->id)->count())->toBe(10)
            ->and(Transaction::query()->where('account_id', $posAccount->id)->count())->toBe(85)
            ->and(InventoryMovement::query()->where('account_id', $posAccount->id)->count())->toBe(322);
    }
});
