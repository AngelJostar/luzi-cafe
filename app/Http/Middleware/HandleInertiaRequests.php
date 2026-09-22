<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
<<<<<<< HEAD
        $user = $request->user();
        $permissions = collect([
            'branches.view',
            'products.manage',
            'inventory.view',
            'orders.view',
            'users.manage',
            'reports.view',
            'settings.manage',
        ])->mapWithKeys(fn (string $permission): array => [
            $permission => $user?->can($permission) ?? false,
        ])->all();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
                'permissions' => $permissions,
                'roles' => $user?->getRoleNames()->all() ?? [],
                'homeUrl' => $user
                    ? route($permissions['branches.view'] ? 'dashboard' : 'profile.edit', absolute: false)
                    : route('login', absolute: false),
=======
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
>>>>>>> d5b831a0675ca0cc56a64701e194a719e3f5ebfd
            ],
        ];
    }
}
