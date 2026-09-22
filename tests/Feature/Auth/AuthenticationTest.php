<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
<<<<<<< HEAD
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\TestWith;
use Spatie\Permission\Models\Permission;
=======
>>>>>>> d5b831a0675ca0cc56a64701e194a719e3f5ebfd
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

<<<<<<< HEAD
        $response = $this->followingRedirects()->post('/login', [
=======
        $response = $this->post('/login', [
>>>>>>> d5b831a0675ca0cc56a64701e194a719e3f5ebfd
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
<<<<<<< HEAD
        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Profile/Edit'));
    }

    public function test_users_with_dashboard_permission_reach_dashboard_after_login(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::create([
            'name' => 'branches.view',
            'guard_name' => 'web',
        ]));

        $response = $this->followingRedirects()->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Dashboard'));
    }

    #[TestWith(['/register'])]
    #[TestWith(['/login'])]
    public function test_authenticated_users_without_dashboard_permission_reach_their_profile_from_guest_pages(string $path): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->followingRedirects()->get($path);

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Profile/Edit'));
    }

    #[TestWith(['/register'])]
    #[TestWith(['/login'])]
    public function test_authenticated_users_with_dashboard_permission_reach_dashboard_from_guest_pages(string $path): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::create([
            'name' => 'branches.view',
            'guard_name' => 'web',
        ]));

        $response = $this->actingAs($user)->followingRedirects()->get($path);

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Dashboard'));
    }

    public function test_users_without_dashboard_permission_cannot_access_dashboard_directly(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertForbidden();
=======
        $response->assertRedirect(route('dashboard', absolute: false));
>>>>>>> d5b831a0675ca0cc56a64701e194a719e3f5ebfd
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
