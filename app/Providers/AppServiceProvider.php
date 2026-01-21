<?php

namespace App\Providers;

use App\Models\Account;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureAuthorization();
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }

    protected function configureAuthorization(): void
    {
        Gate::define('view-account', function (User $user, Account $account): bool {
            return $user->isAccountMember($account);
        });

        Gate::define('manage-finance', function (User $user, Account $account): bool {
            return $user->hasAccountRole($account, ['owner', 'manager']);
        });

        Gate::define('manage-inventory', function (User $user, Account $account): bool {
            return $user->hasAccountRole($account, ['owner', 'manager']);
        });

        Gate::define('manage-cashiers', function (User $user, Account $account): bool {
            return $user->hasAccountRole($account, ['owner', 'manager']);
        });

        Gate::define('create-sale', function (User $user, Account $account): bool {
            return $user->hasAccountRole($account, ['owner', 'manager', 'cashier']);
        });
    }
}
