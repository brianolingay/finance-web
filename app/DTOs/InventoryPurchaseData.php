<?php

namespace App\DTOs;

use App\Http\Requests\StoreInventoryPurchaseRequest;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;

class InventoryPurchaseData
{
    public function __construct(
        public readonly ?int $supplierId,
        public readonly ?int $goodsReceiptId,
        public readonly int $totalCents,
        public readonly string $currency,
        public readonly ?CarbonImmutable $paidAt,
        public readonly ?string $status,
    ) {}

    public static function fromRequest(StoreInventoryPurchaseRequest $request): self
    {
        return self::fromArray($request->validated());
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['supplier_id']) ? (int) $data['supplier_id'] : null,
            isset($data['goods_receipt_id']) ? (int) $data['goods_receipt_id'] : null,
            (int) $data['total_cents'],
            (string) $data['currency'],
            isset($data['paid_at']) ? Date::parse($data['paid_at'])->toImmutable() : null,
            $data['status'] ?? null,
        );
    }
}
