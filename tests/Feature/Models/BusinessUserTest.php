<?php

namespace Tests\Feature\Models;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_and_user_receive_public_ulids(): void
    {
        $business = Business::factory()->create();

        $user = User::factory()
            ->for($business)
            ->administrator()
            ->create();

        $this->assertMatchesRegularExpression(
            '/^[0-9A-HJKMNP-TV-Z]{26}$/',
            $business->public_id
        );

        $this->assertMatchesRegularExpression(
            '/^[0-9A-HJKMNP-TV-Z]{26}$/',
            $user->public_id
        );

        $this->assertTrue($user->business->is($business));
        $this->assertTrue($user->isAdministrator());
        $this->assertTrue($user->isActive());
    }

    public function test_superadmin_does_not_belong_to_a_business(): void
    {
        $superAdmin = User::factory()
            ->superAdmin()
            ->create();

        $this->assertNull($superAdmin->business_id);
        $this->assertTrue($superAdmin->isSuperAdmin());
    }

    public function test_suspended_business_is_not_operational(): void
    {
        $business = Business::factory()
            ->suspended()
            ->create();

        $this->assertFalse($business->isActive());
        $this->assertNotNull($business->suspended_at);
    }
}