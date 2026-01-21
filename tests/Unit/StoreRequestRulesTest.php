<?php

use App\Http\Requests\StoreCashierSalaryRequest;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\StoreIncomeRequest;
use App\Http\Requests\StoreInventoryPurchaseRequest;
use App\Http\Requests\StoreSaleRequest;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

it('returns a cashier factory for sale relationships', function () {
    $definition = Sale::factory()->definition();
    $cashierFactory = $definition['cashier_id'](['account_id' => 123]);

    expect($cashierFactory)->toBeInstanceOf(EloquentFactory::class);
});
