<?php

namespace App\Listeners;

use App\Actions\Ledger\RecordTransactionAction;
use App\Events\CashierSalaryCreated;
use App\Events\ExpenseCreated;
use App\Events\IncomeCreated;
use App\Events\InventoryPurchaseRecorded;
use App\Events\SaleCompleted;

class CreateTransactionListener
{
    /**
     * Create the event listener.
     */
    public function __construct(private RecordTransactionAction $recordTransactionAction) {}

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        if ($event instanceof ExpenseCreated) {
            $expense = $event->expense;

            $this->recordTransactionAction->run(
                $expense->account_id,
                'debit',
                $expense->amount_cents,
                $expense->currency,
                $expense,
                $expense->occurred_at,
            );

            return;
        }

        if ($event instanceof IncomeCreated) {
            $income = $event->income;

            $this->recordTransactionAction->run(
                $income->account_id,
                'credit',
                $income->amount_cents,
                $income->currency,
                $income,
                $income->occurred_at,
            );

            return;
        }

        if ($event instanceof SaleCompleted) {
            $sale = $event->sale;

            $this->recordTransactionAction->run(
                $sale->account_id,
                'credit',
                $sale->total_cents,
                $sale->currency,
                $sale,
                $sale->occurred_at,
            );

            return;
        }

        if ($event instanceof InventoryPurchaseRecorded) {
            $purchase = $event->inventoryPurchase;

            $this->recordTransactionAction->run(
                $purchase->account_id,
                'debit',
                $purchase->total_cents,
                $purchase->currency,
                $purchase,
                $purchase->paid_at ?? $purchase->created_at,
            );

            return;
        }

        if ($event instanceof CashierSalaryCreated) {
            $salary = $event->cashierSalary;

            $this->recordTransactionAction->run(
                $salary->account_id,
                'debit',
                $salary->amount_cents,
                $salary->currency,
                $salary,
                $salary->paid_at,
            );
        }
    }
}
