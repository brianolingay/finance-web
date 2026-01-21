<?php

namespace App\Actions\Payroll;

use App\DTOs\CashierInviteData;
use App\Models\Account;
use App\Models\AccountMember;
use App\Models\Cashier;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InviteCashierAction
{
    public function run(Account $account, CashierInviteData $data): Cashier
    {
        return DB::transaction(function () use ($account, $data): Cashier {
            $user = User::query()->where('email', $data->email)->firstOrFail();

            AccountMember::query()->firstOrCreate(
                [
                    'account_id' => $account->id,
                    'user_id' => $user->id,
                ],
                [
                    'role' => 'cashier',
                ],
            );

            return Cashier::query()->firstOrCreate(
                [
                    'account_id' => $account->id,
                    'user_id' => $user->id,
                ],
                [
                    'name' => $data->name ?? $user->name,
                    'status' => 'active',
                ],
            );
        });
    }
}
