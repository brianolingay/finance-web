<?php

use App\Actions\Finance\CreateIncomeAction;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('requires amount cents and currency', function (array $payload) {
    $account = Account::factory()->create();

    expect(fn () => app(CreateIncomeAction::class)->run($account, $payload))
        ->toThrow(ValidationException::class);
})->with([
    'missing amount' => [[
        'currency' => 'USD',
    ]],
    'missing currency' => [[
        'amount_cents' => 1200,
    ]],
]);
