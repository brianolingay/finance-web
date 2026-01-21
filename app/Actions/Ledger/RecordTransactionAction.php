<?php

namespace App\Actions\Ledger;

use App\DTOs\TransactionData;
use App\Models\Transaction;

class RecordTransactionAction
{
    public function run(TransactionData $data): Transaction
    {
        if (! in_array($data->direction, ['debit', 'credit'], true)) {
            throw new \InvalidArgumentException(
                "CreateTransactionListener supplied invalid transaction direction [{$data->direction}]. Expected 'debit' or 'credit'.",
            );
        }

        return Transaction::query()->firstOrCreate(
            [
                'source_type' => $data->source->getMorphClass(),
                'source_id' => $data->source->getKey(),
            ],
            [
                'account_id' => $data->accountId,
                'direction' => $data->direction,
                'amount_cents' => $data->amountCents,
                'currency' => $data->currency,
                'occurred_at' => $data->occurredAt,
            ],
        );
    }
}
