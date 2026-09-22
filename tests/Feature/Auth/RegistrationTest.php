<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
<<<<<<< HEAD
use Inertia\Testing\AssertableInertia as Assert;
=======
>>>>>>> d5b831a0675ca0cc56a64701e194a719e3f5ebfd
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
<<<<<<< HEAD
        $response = $this->followingRedirects()->post('/register', [
=======
        $response = $this->post('/register', [
>>>>>>> d5b831a0675ca0cc56a64701e194a719e3f5ebfd
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
<<<<<<< HEAD
        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Profile/Edit'));
=======
        $response->assertRedirect(route('dashboard', absolute: false));
>>>>>>> d5b831a0675ca0cc56a64701e194a719e3f5ebfd
    }
}
