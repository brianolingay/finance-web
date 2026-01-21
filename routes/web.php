<?php

use App\Http\Controllers\AccountDashboardController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\CashierSalaryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GoodsReceiptController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\InventoryPurchaseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

Route::middleware(['auth', 'verified', 'account.member'])
    ->prefix('accounts/{account}')
    ->group(function () {
        Route::get('dashboard', [AccountDashboardController::class, 'index'])
            ->name('accounts.dashboard');

        Route::get('expenses', [ExpenseController::class, 'index'])
            ->middleware('can:manage-finance,account')
            ->name('accounts.expenses.index');
        Route::post('expenses', [ExpenseController::class, 'store'])
            ->middleware('can:manage-finance,account')
            ->name('accounts.expenses.store');

        Route::get('incomes', [IncomeController::class, 'index'])
            ->middleware('can:manage-finance,account')
            ->name('accounts.incomes.index');
        Route::post('incomes', [IncomeController::class, 'store'])
            ->middleware('can:manage-finance,account')
            ->name('accounts.incomes.store');

        Route::get('pos/sales/new', [SaleController::class, 'create'])
            ->middleware('can:create-sale,account')
            ->name('accounts.pos.sales.create');
        Route::post('pos/sales', [SaleController::class, 'store'])
            ->middleware('can:create-sale,account')
            ->name('accounts.pos.sales.store');

        Route::get('inventory/products', [ProductController::class, 'index'])
            ->middleware('can:manage-inventory,account')
            ->name('accounts.inventory.products.index');
        Route::get('inventory/receipts', [GoodsReceiptController::class, 'create'])
            ->middleware('can:manage-inventory,account')
            ->name('accounts.inventory.receipts.create');
        Route::post('inventory/receipts', [GoodsReceiptController::class, 'store'])
            ->middleware('can:manage-inventory,account')
            ->name('accounts.inventory.receipts.store');
        Route::post('inventory/purchases', [InventoryPurchaseController::class, 'store'])
            ->middleware('can:manage-inventory,account')
            ->name('accounts.inventory.purchases.store');

        Route::get('cashiers', [CashierController::class, 'index'])
            ->middleware('can:manage-cashiers,account')
            ->name('accounts.cashiers.index');
        Route::post('cashiers', [CashierController::class, 'store'])
            ->middleware('can:manage-cashiers,account')
            ->name('accounts.cashiers.store');
        Route::post('cashier-salaries', [CashierSalaryController::class, 'store'])
            ->middleware('can:manage-cashiers,account')
            ->name('accounts.cashier-salaries.store');
    });

require __DIR__.'/settings.php';
