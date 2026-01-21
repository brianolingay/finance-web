<?php

namespace App\Http\Controllers;

use App\Actions\Inventory\ProductsIndexQuery;
use App\Http\Resources\ProductResource;
use App\Models\Account;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Account $account, ProductsIndexQuery $query): Response
    {
        $this->authorize('manage-inventory', $account);

        $payload = $query->run($account);

        return Inertia::render('accounts/inventory/products', [
            'account' => [
                'id' => $account->id,
                'name' => $account->name,
            ],
            'products' => $payload['products']->through(
                fn ($product) => ProductResource::make($product)->resolve(),
            ),
        ]);
    }
}
