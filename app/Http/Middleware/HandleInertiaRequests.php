<?php

namespace App\Http\Middleware;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $account = $request->route('account');
        $accountData = null;
        $abilities = null;

        if ($account instanceof Account && $request->user()) {
            $accountData = [
                'id' => $account->id,
                'name' => $account->name,
                'type' => $account->type,
            ];

            $abilities = [
                'manageFinance' => Gate::allows('manage-finance', $account),
                'manageInventory' => Gate::allows('manage-inventory', $account),
                'manageCashiers' => Gate::allows('manage-cashiers', $account),
                'createSale' => Gate::allows('create-sale', $account),
            ];
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'account' => $accountData,
            'abilities' => $abilities,
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
