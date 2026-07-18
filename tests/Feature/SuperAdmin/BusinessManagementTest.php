<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_view_businesses(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->get('/superadmin/businesses')
            ->assertOk()
            ->assertSee('Negocios');
    }

    public function test_administrator_cannot_access_superadmin_area(): void
    {
        $administrator = User::factory()
            ->administrator()
            ->create();

        $this->actingAs($administrator)
            ->get('/superadmin/businesses')
            ->assertForbidden();
    }

    public function test_superadmin_can_create_business_and_administrator(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($superAdmin)
            ->post('/superadmin/businesses', [
                'name' => 'Tienda Piloto',
                'legal_name' => 'Tienda Piloto S.A.C.',
                'tax_id' => '20123456789',
                'contact_phone' => '999888777',
                'admin_name' => 'Administrador Piloto',
                'admin_email' => 'admin@tiendapiloto.test',
                'admin_phone' => '999111222',
                'admin_password' => 'ClaveSegura123!',
                'admin_password_confirmation' => 'ClaveSegura123!',
            ]);

        $business = Business::query()
            ->where('name', 'Tienda Piloto')
            ->firstOrFail();

        $response->assertRedirect(
            route('superadmin.businesses.show', $business)
        );

        $this->assertDatabaseHas('users', [
            'business_id' => $business->id,
            'email' => 'admin@tiendapiloto.test',
            'role_code' => User::ROLE_ADMINISTRATOR,
        ]);
    }
}