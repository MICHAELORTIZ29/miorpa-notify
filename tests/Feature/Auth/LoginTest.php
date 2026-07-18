<?php

namespace Tests\Feature\Auth;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('MIORPA NOTIFY');
    }

    public function test_active_administrator_can_login(): void
    {
        $business = Business::factory()->active()->create();

        $user = User::factory()
            ->for($business)
            ->administrator()
            ->create([
                'email' => 'admin@example.com',
                'password' => 'Secret12345!',
            ]);

        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'Secret12345!',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        $user = User::factory()->superAdmin()->create([
            'email' => 'superadmin@example.com',
            'password' => 'Secret12345!',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'incorrecta',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_disabled_user_cannot_login(): void
    {
        $user = User::factory()
            ->superAdmin()
            ->disabled()
            ->create([
                'password' => 'Secret12345!',
            ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'Secret12345!',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_user_from_suspended_business_cannot_login(): void
    {
        $business = Business::factory()->suspended()->create();

        $user = User::factory()
            ->for($business)
            ->administrator()
            ->create([
                'password' => 'Secret12345!',
            ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'Secret12345!',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->superAdmin()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
    }
}