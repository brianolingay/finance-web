<?php

namespace App\DTOs;

use App\Http\Requests\StoreIncomeRequest;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Throwable;

class IncomeData
{
    public function __construct(
        public readonly ?int $categoryId,
        public readonly ?string $description,
        public readonly ?string $status,
        public readonly int $amountCents,
        public readonly string $currency,
        public readonly ?CarbonImmutable $occurredAt,
        public readonly ?CarbonImmutable $paidAt,
    ) {}

    public static function fromRequest(StoreIncomeRequest $request): self
    {
        return self::fromArray($request->validated());
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $occurredAt = null;
        $paidAt = null;

        if (isset($data['occurred_at'])) {
            try {
                $occurredAt = Date::parse($data['occurred_at'])->toImmutable();
            } catch (Throwable) {
                $occurredAt = null;
            }
        }

        if (isset($data['paid_at'])) {
            try {
                $paidAt = Date::parse($data['paid_at'])->toImmutable();
            } catch (Throwable) {
                $paidAt = null;
            }
        }

        return new self(
            isset($data['category_id']) ? (int) $data['category_id'] : null,
            $data['description'] ?? null,
            $data['status'] ?? null,
            (int) $data['amount_cents'],
            (string) $data['currency'],
            $occurredAt,
            $paidAt,
        );
    }
}
