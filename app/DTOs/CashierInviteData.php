<?php

namespace App\DTOs;

use App\Http\Requests\StoreCashierRequest;

class CashierInviteData
{
    public function __construct(
        public readonly string $email,
        public readonly ?string $name,
    ) {}

    public static function fromRequest(StoreCashierRequest $request): self
    {
        return self::fromArray($request->validated());
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) $data['email'],
            $data['name'] ?? null,
        );
    }
}
