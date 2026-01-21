<?php

namespace App\Providers;

use App\Events\CashierSalaryCreated;
use App\Events\ExpenseCreated;
use App\Events\GoodsReceiptCreated;
use App\Events\IncomeCreated;
use App\Events\InventoryPurchaseRecorded;
use App\Events\SaleCompleted;
use App\Listeners\CreateInventoryMovementsListener;
use App\Listeners\CreateTransactionListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        ExpenseCreated::class => [
            CreateTransactionListener::class,
        ],
        IncomeCreated::class => [
            CreateTransactionListener::class,
        ],
        SaleCompleted::class => [
            CreateTransactionListener::class,
            CreateInventoryMovementsListener::class,
        ],
        GoodsReceiptCreated::class => [
            CreateInventoryMovementsListener::class,
        ],
        InventoryPurchaseRecorded::class => [
            CreateTransactionListener::class,
        ],
        CashierSalaryCreated::class => [
            CreateTransactionListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void {}
}
