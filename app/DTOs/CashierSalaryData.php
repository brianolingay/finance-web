<?php

namespace App\DTOs;

use App\Http\Requests\StoreCashierSalaryRequest;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Throwable;

class CashierSalaryData
{
    public function __construct(
        public readonly int $cashierId,
        public readonly ?int $salaryRuleId,
        public readonly int $amountCents,
        public readonly string $currency,
        public readonly ?CarbonImmutable $paidAt,
        public readonly ?string $status,
    ) {}

    public static function fromRequest(StoreCashierSalaryRequest $request): self
    {
        return self::fromArray($request->validated());
    }

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
            (int) $data['cashier_id'],
            isset($data['salary_rule_id']) ? (int) $data['salary_rule_id'] : null,
            (int) $data['amount_cents'],
            (string) $data['currency'],
            $paidAt,
            $data['status'] ?? null,
        );
    }
}
