<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncomeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'amount_cents' => $this->amount_cents,
            'currency' => $this->currency,
            'occurred_at' => $this->occurred_at?->toJSON(),
        ];
    }
}
