<?php

use App\Actions\Payroll\InviteCashierAction;
use App\DTOs\CashierInviteData;
use App\Models\Account;
use App\Models\AccountMember;
use App\Models\Cashier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('invites a cashier by attaching membership and cashier profile', function () {
    $account = Account::factory()->create();
    $user = User::factory()->create();

    $cashier = app(InviteCashierAction::class)->run($account, new CashierInviteData(
        $user->email,
        'Store Cashier',
    ));

    $member = AccountMember::query()
        ->where('account_id', $account->id)
        ->where('user_id', $user->id)
        ->first();

    expect($cashier)->toBeInstanceOf(Cashier::class)
        ->and($cashier->account_id)->toBe($account->id)
        ->and($cashier->user_id)->toBe($user->id)
        ->and($member)->not->toBeNull()
        ->and($member->role)->toBe('cashier');
});
