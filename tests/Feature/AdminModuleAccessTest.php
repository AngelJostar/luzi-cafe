<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminModuleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_receive_no_module_permissions(): void
    {
        $response = $this->get('/');

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('auth.user', null)
            ->where('auth.roles', [])
            ->where('auth.homeUrl', '/login')
            ->where('auth.permissions', [
                'branches.view' => false,
                'products.manage' => false,
                'inventory.view' => false,
                'orders.view' => false,
                'users.manage' => false,
                'reports.view' => false,
                'settings.manage' => false,
            ]));
    }

    public function test_users_without_roles_receive_no_module_permissions_and_a_profile_home(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Profile/Edit')
            ->where('auth.roles', [])
            ->where('auth.homeUrl', '/profile')
            ->where('auth.permissions', [
                'branches.view' => false,
                'products.manage' => false,
                'inventory.view' => false,
                'orders.view' => false,
                'users.manage' => false,
                'reports.view' => false,
                'settings.manage' => false,
            ]));
    }

    public function test_cashiers_receive_only_order_module_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('cajero');

        $response = $this->actingAs($user)->get('/profile');

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Profile/Edit')
            ->where('auth.roles', ['cajero'])
            ->where('auth.homeUrl', '/profile')
            ->where('auth.permissions', [
                'branches.view' => false,
                'products.manage' => false,
                'inventory.view' => false,
                'orders.view' => true,
                'users.manage' => false,
                'reports.view' => false,
                'settings.manage' => false,
            ]));
    }

    public function test_superadmins_receive_all_module_permissions_through_the_gate(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'superadmin', 'guard_name' => 'web']));

        $response = $this->actingAs($user)->get('/profile');

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Profile/Edit')
            ->where('auth.roles', ['superadmin'])
            ->where('auth.homeUrl', '/dashboard')
            ->where('auth.permissions', [
                'branches.view' => true,
                'products.manage' => true,
                'inventory.view' => true,
                'orders.view' => true,
                'users.manage' => true,
                'reports.view' => true,
                'settings.manage' => true,
            ]));
    }

    #[DataProvider('modules')]
    public function test_superadmins_can_open_each_module(string $routeName, string $component): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'superadmin', 'guard_name' => 'web']));
        Role::create(['name' => 'cliente', 'guard_name' => 'web']);

        $response = $this->actingAs($user)->get(route($routeName));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component($component));
    }

    #[DataProvider('modules')]
    public function test_general_administrators_can_open_each_module(string $routeName, string $component): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('administrador-general');

        $response = $this->actingAs($user)->get(route($routeName));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component($component));
    }

    #[DataProvider('moduleRoutes')]
    public function test_users_without_roles_receive_403_for_each_module(string $routeName): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route($routeName));

        $response->assertForbidden();
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function modules(): array
    {
        return [
            'dashboard' => ['dashboard', 'Dashboard'],
            'sales' => ['sales.index', 'Sales/Index'],
            'branches' => ['branches.index', 'Branches/Index'],
            'catalog' => ['catalog.index', 'Catalog/Index'],
            'modifiers' => ['modifiers.index', 'Modifiers/Index'],
            'cms' => ['cms.index', 'Cms/Index'],
            'inventory' => ['inventory.index', 'Inventory/Index'],
            'recipes' => ['recipes.index', 'Recipes/Index'],
            'orders' => ['orders.index', 'Orders/Index'],
            'customers' => ['customers.index', 'Customers/Index'],
            'notifications' => ['notifications.index', 'Notifications/Index'],
            'reports' => ['reports.index', 'Reports/Index'],
            'settings' => ['settings.index', 'Settings/Index'],
            'promotions' => ['promotions.index', 'Promotions/Index'],
            'audit' => ['audit.index', 'Audit/Index'],
            'roles' => ['roles.index', 'Roles/Index'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function moduleRoutes(): array
    {
        return array_map(fn (array $module): array => [$module[0]], self::modules());
    }
}
