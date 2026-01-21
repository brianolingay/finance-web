<?php

namespace App\DTOs;

use App\Http\Requests\StoreSaleRequest;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Throwable;

class SaleData
{
    /**
     * @param  list<SaleItemData>  $items
     */
    public function __construct(
        public readonly ?int $cashierId,
        public readonly string $currency,
        public readonly ?CarbonImmutable $occurredAt,
        public readonly array $items,
        public readonly ?SalePaymentData $payment,
    ) {}

    public static function fromRequest(StoreSaleRequest $request): self
    {
        return self::fromArray($request->validated());
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $items = array_map(
            fn (array $item): SaleItemData => SaleItemData::fromArray($item),
            $data['items'] ?? [],
        );

        $payment = null;

        if (! empty($data['payment']) && is_array($data['payment'])) {
            $payment = SalePaymentData::fromArray($data['payment']);
        }

        $occurredAt = null;

        if (isset($data['occurred_at'])) {
            try {
                $occurredAt = Date::parse($data['occurred_at'])->toImmutable();
            } catch (Throwable) {
                $occurredAt = null;
            }
        }

        return new self(
            isset($data['cashier_id']) ? (int) $data['cashier_id'] : null,
            isset($data['currency']) ? (string) $data['currency'] : '',
            $occurredAt,
            $items,
            $payment,
        );
    }
}
