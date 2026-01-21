<?php

namespace App\DTOs;

use App\Http\Requests\StoreExpenseRequest;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;

class ExpenseData
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

    public static function fromRequest(StoreExpenseRequest $request): self
    {
        return self::fromArray($request->validated());
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['category_id']) ? (int) $data['category_id'] : null,
            $data['description'] ?? null,
            $data['status'] ?? null,
            (int) $data['amount_cents'],
            (string) $data['currency'],
            isset($data['occurred_at']) ? Date::parse($data['occurred_at'])->toImmutable() : null,
            isset($data['paid_at']) ? Date::parse($data['paid_at'])->toImmutable() : null,
        );
    }
}
