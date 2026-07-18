<?php

namespace Tests\Feature\Business;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_view_cashiers_from_own_business(): void
    {
        $business = Business::factory()->create([
            'status' => Business::STATUS_ACTIVE,
        ]);

        $administrator = User::factory()->create([
            'business_id' => $business->id,
            'role_code' => User::ROLE_ADMINISTRATOR,
            'status' => User::STATUS_ACTIVE,
        ]);

        $cashier = User::factory()->create([
            'business_id' => $business->id,
            'name' => 'Cajero de prueba',
            'role_code' => User::ROLE_CASHIER,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this
            ->actingAs($administrator)
            ->get(route('business.users.index'))
            ->assertOk()
            ->assertSee($cashier->name);
    }

    public function test_administrator_can_create_a_cashier(): void
    {
        $business = Business::factory()->create([
            'status' => Business::STATUS_ACTIVE,
        ]);

        $administrator = User::factory()->create([
            'business_id' => $business->id,
            'role_code' => User::ROLE_ADMINISTRATOR,
            'status' => User::STATUS_ACTIVE,
        ]);

        $response = $this
            ->actingAs($administrator)
            ->post(route('business.users.store'), [
                'name' => 'Nuevo cajero',
                'email' => 'cajero@example.com',
                'password' => 'ClaveSegura123!',
                'password_confirmation' => 'ClaveSegura123!',
                'role_code' => User::ROLE_CASHIER,
            ]);

        $response->assertRedirect(route('business.users.index'));

        $this->assertDatabaseHas('users', [
            'business_id' => $business->id,
            'name' => 'Nuevo cajero',
            'email' => 'cajero@example.com',
            'role_code' => User::ROLE_CASHIER,
            'status' => User::STATUS_ACTIVE,
        ]);
    }

    public function test_administrator_can_deactivate_own_cashier(): void
    {
        $business = Business::factory()->create([
            'status' => Business::STATUS_ACTIVE,
        ]);

        $administrator = User::factory()->create([
            'business_id' => $business->id,
            'role_code' => User::ROLE_ADMINISTRATOR,
            'status' => User::STATUS_ACTIVE,
        ]);

        $cashier = User::factory()->create([
            'business_id' => $business->id,
            'role_code' => User::ROLE_CASHIER,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this
            ->actingAs($administrator)
            ->patch(route('business.users.deactivate', $cashier))
            ->assertRedirect();

        $cashier->refresh();

        $this->assertSame(User::STATUS_DISABLED, $cashier->status);
        $this->assertNotNull($cashier->disabled_at);
    }

    public function test_administrator_cannot_manage_cashier_from_another_business(): void
    {
        $firstBusiness = Business::factory()->create([
            'status' => Business::STATUS_ACTIVE,
        ]);

        $secondBusiness = Business::factory()->create([
            'status' => Business::STATUS_ACTIVE,
        ]);

        $administrator = User::factory()->create([
            'business_id' => $firstBusiness->id,
            'role_code' => User::ROLE_ADMINISTRATOR,
            'status' => User::STATUS_ACTIVE,
        ]);

        $foreignCashier = User::factory()->create([
            'business_id' => $secondBusiness->id,
            'role_code' => User::ROLE_CASHIER,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this
            ->actingAs($administrator)
            ->get(route('business.users.edit', $foreignCashier))
            ->assertNotFound();

        $this
            ->actingAs($administrator)
            ->patch(route('business.users.deactivate', $foreignCashier))
            ->assertNotFound();
    }

    public function test_cashier_cannot_access_user_management(): void
    {
        $business = Business::factory()->create([
            'status' => Business::STATUS_ACTIVE,
        ]);

        $cashier = User::factory()->create([
            'business_id' => $business->id,
            'role_code' => User::ROLE_CASHIER,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this
            ->actingAs($cashier)
            ->get(route('business.users.index'))
            ->assertForbidden();
    }
}