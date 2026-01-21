<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cashier_id' => $this->cashier_id,
            'status' => $this->status,
            'total_cents' => $this->total_cents,
            'currency' => $this->currency,
            'occurred_at' => $this->occurred_at?->toJSON(),
        ];
    }
}
