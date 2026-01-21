<?php

use App\Events\ExpenseCreated;
use App\Events\SaleCompleted;
use App\Models\Expense;
use App\Models\Sale;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a single transaction per domain source', function (callable $factory, string $eventClass) {
    $model = $factory();

    event(new $eventClass($model));
    event(new $eventClass($model));

    $transactionCount = Transaction::query()
        ->where('source_type', $model->getMorphClass())
        ->where('source_id', $model->getKey())
        ->count();

    expect($transactionCount)->toBe(1);
})->with([
    'expense' => [fn () => Expense::factory()->create(), ExpenseCreated::class],
    'sale' => [fn () => Sale::factory()->create(), SaleCompleted::class],
]);
