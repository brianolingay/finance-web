<?php

use App\Models\CashierSalary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('creates a cashier salary tied to the same account as the cashier', function () {
    $salary = CashierSalary::factory()->create();

    expect($salary->cashier->account_id)->toBe($salary->account_id);
});
