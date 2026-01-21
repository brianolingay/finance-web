<?php

use App\Actions\Inventory\RecordInventoryPurchasePaymentAction;
use App\Actions\Ledger\RecordTransactionAction;
use App\DTOs\InventoryPurchaseData;
use App\Models\Account;
use App\Models\InventoryPurchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function Pest\Laravel\mock;

uses(TestCase::class, RefreshDatabase::class);

it('rolls back inventory purchases when transaction recording fails', function () {
    mock(RecordTransactionAction::class)
        ->shouldReceive('run')
        ->andThrow(new RuntimeException('Ledger failure.'));

    $account = Account::factory()->create();

    expect(fn () => app(RecordInventoryPurchasePaymentAction::class)->run($account, new InventoryPurchaseData(
        null,
        null,
        5400,
        'USD',
        null,
        null,
    )))->toThrow(RuntimeException::class);

    expect(InventoryPurchase::query()->count())->toBe(0);
});
