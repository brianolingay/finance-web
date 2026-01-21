<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Account $account): Response
    {
        $products = $account->products()
            ->orderBy('name')
            ->paginate(10);

        return Inertia::render('accounts/inventory/products', [
            'account' => [
                'id' => $account->id,
                'name' => $account->name,
            ],
            'products' => $products,
        ]);
    }
}
