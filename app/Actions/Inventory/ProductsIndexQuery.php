<?php

namespace App\Actions\Inventory;

use App\Models\Account;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductsIndexQuery
{
    /**
     * @return array{products: LengthAwarePaginator}
     */
    public function run(Account $account): array
    {
        $products = Product::query()
            ->where('account_id', $account->id)
            ->orderBy('name')
            ->paginate(10);

        return [
            'products' => $products,
        ];
    }
}
