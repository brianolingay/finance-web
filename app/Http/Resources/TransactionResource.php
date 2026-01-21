<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'direction' => $this->direction,
            'amount_cents' => $this->amount_cents,
            'currency' => $this->currency,
            'occurred_at' => $this->occurred_at?->toJSON(),
            'source_type' => $this->source_type,
        ];
    }
}
