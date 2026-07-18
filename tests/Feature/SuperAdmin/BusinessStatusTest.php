<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessStatusTest extends TestCase
{
    use RefreshDatabase;

   public function test_superadmin_can_edit_a_business(): void
{
    $superAdmin = User::factory()->superAdmin()->create();
    $business = Business::factory()->create();

    $administrator = User::factory()->create([
        'business_id' => $business->id,
        'name' => 'Administrador original',
        'email' => 'original@example.com',
        'role_code' => User::ROLE_ADMINISTRATOR,
        'status' => User::STATUS_ACTIVE,
    ]);

    $response = $this
        ->actingAs($superAdmin)
        ->put(route('superadmin.businesses.update', $business), [
            'name' => 'Negocio actualizado',
            'legal_name' => 'Negocio Actualizado S.A.C.',
            'tax_id' => '20123456789',
            'contact_phone' => '987654321',
            'timezone' => 'America/Lima',
            'admin_name' => 'Administrador actualizado',
            'admin_email' => 'actualizado@example.com',
            'admin_password' => 'NuevaClave123!',
            'admin_password_confirmation' => 'NuevaClave123!',
        ]);

    $response->assertRedirect(
        route('superadmin.businesses.show', $business)
    );

    $this->assertDatabaseHas('businesses', [
        'id' => $business->id,
        'name' => 'Negocio actualizado',
        'tax_id' => '20123456789',
        'contact_phone' => '987654321',
    ]);

    $this->assertDatabaseHas('users', [
        'id' => $administrator->id,
        'name' => 'Administrador actualizado',
        'email' => 'actualizado@example.com',
    ]);
}

    public function test_superadmin_can_suspend_a_business(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $business = Business::factory()->create([
            'status' => Business::STATUS_ACTIVE,
            'suspended_at' => null,
        ]);

        $response = $this
            ->actingAs($superAdmin)
            ->patch(route('superadmin.businesses.suspend', $business));

        $response->assertRedirect(
            route('superadmin.businesses.show', $business)
        );

        $business->refresh();

        $this->assertSame(
            Business::STATUS_SUSPENDED,
            $business->status
        );

        $this->assertNotNull($business->suspended_at);
    }

    public function test_superadmin_can_activate_a_suspended_business(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $business = Business::factory()->create([
            'status' => Business::STATUS_SUSPENDED,
            'suspended_at' => now(),
        ]);

        $response = $this
            ->actingAs($superAdmin)
            ->patch(route('superadmin.businesses.activate', $business));

        $response->assertRedirect(
            route('superadmin.businesses.show', $business)
        );

        $business->refresh();

        $this->assertSame(
            Business::STATUS_ACTIVE,
            $business->status
        );

        $this->assertNull($business->suspended_at);
    }

    public function test_administrator_cannot_change_business_status(): void
    {
        $business = Business::factory()->create([
            'status' => Business::STATUS_ACTIVE,
        ]);

        $administrator = User::factory()->create([
            'business_id' => $business->id,
            'role_code' => User::ROLE_ADMINISTRATOR,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this
            ->actingAs($administrator)
            ->patch(route('superadmin.businesses.suspend', $business))
            ->assertForbidden();

        $this->assertDatabaseHas('businesses', [
            'id' => $business->id,
            'status' => Business::STATUS_ACTIVE,
        ]);
    }
}