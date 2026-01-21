<?php

namespace App\DTOs;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

class TransactionData
{
    public function __construct(
        public readonly int $accountId,
        public readonly string $direction,
        public readonly int $amountCents,
        public readonly string $currency,
        public readonly Model $source,
        public readonly CarbonInterface $occurredAt,
    ) {}
}
