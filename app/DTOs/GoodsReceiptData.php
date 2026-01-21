<?php

namespace App\DTOs;

use App\Http\Requests\StoreGoodsReceiptRequest;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Throwable;

class GoodsReceiptData
{
    /**
     * @param  list<GoodsReceiptItemData>  $items
     */
    public function __construct(
        public readonly ?int $supplierId,
        public readonly ?string $reference,
        public readonly ?string $notes,
        public readonly ?CarbonImmutable $receivedAt,
        public readonly array $items,
        public readonly ?string $status,
    ) {}

    public static function fromRequest(StoreGoodsReceiptRequest $request): self
    {
        return self::fromArray($request->validated());
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $items = array_map(
            fn (array $item): GoodsReceiptItemData => GoodsReceiptItemData::fromArray($item),
            $data['items'] ?? [],
        );

        $receivedAt = null;

        if (isset($data['received_at'])) {
            try {
                $receivedAt = Date::parse($data['received_at'])->toImmutable();
            } catch (Throwable) {
                $receivedAt = null;
            }
        }

        return new self(
            isset($data['supplier_id']) ? (int) $data['supplier_id'] : null,
            $data['reference'] ?? null,
            $data['notes'] ?? null,
            $receivedAt,
            $items,
            $data['status'] ?? null,
        );
    }
}
