<?php

namespace App\DTOs;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Throwable;

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
        $paidAt = null;

        if (isset($data['paid_at'])) {
            try {
                $paidAt = Date::parse($data['paid_at'])->toImmutable();
            } catch (Throwable) {
                $paidAt = null;
            }
        }

        return new self(
            (int) $data['amount_cents'],
            $data['method'] ?? null,
            $data['reference'] ?? null,
            $paidAt,
        );
    }
}
