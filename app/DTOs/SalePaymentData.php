<?php

namespace App\DTOs;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;

class SalePaymentData
{
    public function __construct(
        public readonly int $amountCents,
        public readonly ?string $method,
        public readonly ?string $reference,
        public readonly ?CarbonImmutable $paidAt,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['amount_cents'],
            $data['method'] ?? null,
            $data['reference'] ?? null,
            isset($data['paid_at']) ? Date::parse($data['paid_at'])->toImmutable() : null,
        );
    }
}
