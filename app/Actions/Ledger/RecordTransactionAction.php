<?php

namespace App\Actions\Ledger;

use App\Models\Transaction;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

class RecordTransactionAction
{
    public function run(
        int $accountId,
        string $direction,
        int $amountCents,
        string $currency,
        Model $source,
        CarbonInterface $occurredAt,
    ): Transaction {
        if (! in_array($direction, ['debit', 'credit'], true)) {
            throw new \InvalidArgumentException(
                "CreateTransactionListener supplied invalid transaction direction [{$direction}]. Expected 'debit' or 'credit'.",
            );
        }

        return Transaction::query()->updateOrCreate(
            [
                'source_type' => $source->getMorphClass(),
                'source_id' => $source->getKey(),
            ],
            [
                'account_id' => $accountId,
                'direction' => $direction,
                'amount_cents' => $amountCents,
                'currency' => $currency,
                'occurred_at' => $occurredAt,
            ],
        );
    }
}
