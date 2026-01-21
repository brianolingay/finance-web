<?php

use App\Http\Requests\StoreCashierSalaryRequest;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\StoreGoodsReceiptRequest;
use App\Http\Requests\StoreIncomeRequest;
use App\Http\Requests\StoreInventoryPurchaseRequest;
use App\Http\Requests\StoreSaleRequest;
use App\Models\Account;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('aborts when cashier salary rules are requested without an account route', function () {
    $request = StoreCashierSalaryRequest::create('/', 'POST');
    $request->setRouteResolver(fn () => null);

    expect(fn () => $request->rules())->toThrow(NotFoundHttpException::class);
});

it('aborts when expense rules are requested without an account route', function () {
    $request = StoreExpenseRequest::create('/', 'POST');
    $request->setRouteResolver(fn () => null);

    expect(fn () => $request->rules())->toThrow(NotFoundHttpException::class);
});

it('aborts when income rules are requested without an account route', function () {
    $request = StoreIncomeRequest::create('/', 'POST');
    $request->setRouteResolver(fn () => null);

    expect(fn () => $request->rules())->toThrow(NotFoundHttpException::class);
});

it('aborts when sale rules are requested without an account route', function () {
    $request = StoreSaleRequest::create('/', 'POST');
    $request->setRouteResolver(fn () => null);

    expect(fn () => $request->rules())->toThrow(NotFoundHttpException::class);
});

it('aborts when inventory purchase rules are requested without an account route', function () {
    $request = StoreInventoryPurchaseRequest::create('/', 'POST');
    $request->setRouteResolver(fn () => null);

    expect(fn () => $request->rules())->toThrow(NotFoundHttpException::class);
});

it('includes a status rule for inventory purchases', function () {
    $account = Account::factory()->create();
    $request = StoreInventoryPurchaseRequest::create('/', 'POST');
    $request->setRouteResolver(fn () => new class($account)
    {
        public function __construct(private Account $account) {}

        public function parameter(string $key): ?Account
        {
            return $key === 'account' ? $this->account : null;
        }
    });

    $rules = $request->rules();

    expect($rules)->toHaveKey('status');
});

it('aborts when goods receipt rules are requested without an account route', function () {
    $request = StoreGoodsReceiptRequest::create('/', 'POST');
    $request->setRouteResolver(fn () => null);

    expect(fn () => $request->rules())
        ->toThrow(HttpException::class, 'Account context required.');
});

it('creates a cashier attached to the same account as the sale', function () {
    $account = Account::factory()->create();
    $sale = Sale::factory()->for($account)->create();

    $sale->load('cashier');

    expect($sale->cashier)->not->toBeNull()
        ->and($sale->cashier->account_id)->toBe($account->id);
});
