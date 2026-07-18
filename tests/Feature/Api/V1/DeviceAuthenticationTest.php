<?php

namespace Tests\Feature\Api\V1;

use App\Models\Business;
use App\Models\Device;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_device_token_is_required(): void
    {
        $this->getJson('/api/v1/device/status')
            ->assertUnauthorized()
            ->assertJsonPath('code', 'DEVICE_TOKEN_REQUIRED');
    }

    public function test_invalid_device_token_is_rejected(): void
    {
        $this
            ->withToken('invalid-token')
            ->getJson('/api/v1/device/status')
            ->assertUnauthorized()
            ->assertJsonPath('code', 'DEVICE_TOKEN_INVALID');
    }

    public function test_active_device_can_check_status(): void
    {
        $plainToken = 'secure-device-token-123456789';

        $business = Business::factory()->create([
            'status' => Business::STATUS_ACTIVE,
        ]);

        $device = Device::factory()->create([
            'business_id' => $business->id,
            'status' => Device::STATUS_ACTIVE,
            'token_hash' => hash('sha256', $plainToken),
        ]);

        $this
            ->withToken($plainToken)
            ->getJson('/api/v1/device/status')
            ->assertOk()
            ->assertJsonPath('data.device_id', $device->public_id)
            ->assertJsonPath('data.status', Device::STATUS_ACTIVE);
    }

    public function test_device_can_send_heartbeat(): void
    {
        $plainToken = 'heartbeat-device-token-123456';

        $business = Business::factory()->create([
            'status' => Business::STATUS_ACTIVE,
        ]);

        $device = Device::factory()->create([
            'business_id' => $business->id,
            'token_hash' => hash('sha256', $plainToken),
            'last_seen_at' => null,
        ]);

        $this
            ->withToken($plainToken)
            ->postJson('/api/v1/device/heartbeat', [
                'app_version' => '1.1.0',
                'capabilities' => [
                    'notification_listener' => true,
                ],
            ])
            ->assertOk()
            ->assertJsonPath(
                'data.next_heartbeat_seconds',
                60
            );

        $device->refresh();

        $this->assertNotNull($device->last_seen_at);
        $this->assertSame('1.1.0', $device->app_version);
        $this->assertTrue(
            $device->capabilities['notification_listener']
        );
    }

    public function test_disabled_device_is_rejected(): void
    {
        $plainToken = 'disabled-device-token-123456';

        $business = Business::factory()->create([
            'status' => Business::STATUS_ACTIVE,
        ]);

        Device::factory()->disabled()->create([
            'business_id' => $business->id,
            'token_hash' => hash('sha256', $plainToken),
        ]);

        $this
            ->withToken($plainToken)
            ->getJson('/api/v1/device/status')
            ->assertForbidden()
            ->assertJsonPath('code', 'DEVICE_DISABLED');
    }

    public function test_device_from_suspended_business_is_rejected(): void
    {
        $plainToken = 'suspended-business-device-token';

        $business = Business::factory()->create([
            'status' => Business::STATUS_SUSPENDED,
            'suspended_at' => now(),
        ]);

        Device::factory()->create([
            'business_id' => $business->id,
            'token_hash' => hash('sha256', $plainToken),
        ]);

        $this
            ->withToken($plainToken)
            ->getJson('/api/v1/device/status')
            ->assertForbidden()
            ->assertJsonPath(
                'code',
                'BUSINESS_NOT_OPERATIONAL'
            );
    }
}