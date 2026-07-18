<?php

namespace Tests\Feature\Business;

use App\Models\Business;
use App\Models\Device;
use App\Models\PairingCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_device_receives_a_public_ulid(): void
    {
        $device = Device::factory()->create();

        $this->assertNotNull($device->public_id);
        $this->assertSame(26, strlen($device->public_id));
    }

    public function test_administrator_can_generate_secure_pairing_code(): void
    {
        $business = Business::factory()->create([
            'status' => Business::STATUS_ACTIVE,
        ]);

        $administrator = User::factory()->create([
            'business_id' => $business->id,
            'role_code' => User::ROLE_ADMINISTRATOR,
            'status' => User::STATUS_ACTIVE,
        ]);

        $result = PairingCode::issue(
            $business,
            $administrator,
            Device::TYPE_RECEIVER
        );

        $pairingCode = $result['pairing_code'];
        $plainCode = $result['plain_code'];

        $this->assertMatchesRegularExpression(
            '/^MNP-[A-Z2-9]{4}-[A-Z2-9]{4}$/',
            $plainCode
        );

        $this->assertNotSame($plainCode, $pairingCode->code_hash);

        $this->assertSame(
            hash('sha256', $plainCode),
            $pairingCode->code_hash
        );

        $this->assertTrue($pairingCode->isUsable());
    }

    public function test_expired_pairing_code_is_not_usable(): void
    {
        $pairingCode = PairingCode::factory()->create([
            'expires_at' => now()->subMinute(),
        ]);

        $this->assertFalse($pairingCode->isUsable());
    }

    public function test_revoked_pairing_code_is_not_usable(): void
    {
        $pairingCode = PairingCode::factory()->create([
            'revoked_at' => now(),
        ]);

        $this->assertFalse($pairingCode->isUsable());
    }
}