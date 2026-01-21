<?php

namespace App\Actions\POS;

use App\Models\Account;
use App\Models\User;

class NewSaleQuery
{
    /**
     * @return array{products: \Illuminate\Support\Collection<int, \App\Models\Product>, cashierId: int|null}
     */
    public function run(Account $account, User $user): array
    {
        $products = $account->products()
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'unit_price_cents']);

        $cashierId = $account->cashiers()
            ->where('user_id', $user->id)
            ->value('id');

        return [
            'products' => $products,
            'cashierId' => $cashierId,
        ];
    }
}
