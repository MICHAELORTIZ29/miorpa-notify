<?php

namespace Tests\Feature\Api\V1;

use App\Models\Business;
use App\Models\Device;
use App\Models\PairingCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PairingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_device_can_redeem_valid_pairing_code(): void
    {
        $business = Business::factory()->create([
            'status' => Business::STATUS_ACTIVE,
        ]);

        $administrator = User::factory()->create([
            'business_id' => $business->id,
            'role_code' => User::ROLE_ADMINISTRATOR,
            'status' => User::STATUS_ACTIVE,
        ]);

        $issued = PairingCode::issue(
            $business,
            $administrator,
            Device::TYPE_EMITTER
        );

        $response = $this->postJson('/api/v1/pairings/redeem', [
            'code' => $issued['plain_code'],
            'name' => 'Teléfono principal',
            'installation_id' => 'installation-id-123456789',
            'platform' => 'android',
            'app_version' => '1.0.0',
            'capabilities' => [
                'notification_listener' => true,
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath(
                'data.device_type',
                Device::TYPE_EMITTER
            )
            ->assertJsonStructure([
                'message',
                'data' => [
                    'device_id',
                    'business_id',
                    'device_type',
                    'token',
                ],
            ]);

        $plainToken = $response->json('data.token');

        $this->assertDatabaseHas('devices', [
            'business_id' => $business->id,
            'name' => 'Teléfono principal',
            'token_hash' => hash('sha256', $plainToken),
        ]);

        $this->assertDatabaseMissing('devices', [
            'token_hash' => $plainToken,
        ]);
    }

    public function test_pairing_code_cannot_be_used_twice(): void
    {
        $business = Business::factory()->create([
            'status' => Business::STATUS_ACTIVE,
        ]);

        $administrator = User::factory()->create([
            'business_id' => $business->id,
            'role_code' => User::ROLE_ADMINISTRATOR,
            'status' => User::STATUS_ACTIVE,
        ]);

        $issued = PairingCode::issue(
            $business,
            $administrator,
            Device::TYPE_RECEIVER
        );

        $payload = [
            'code' => $issued['plain_code'],
            'name' => 'Caja principal',
            'installation_id' => 'receiver-installation-123456',
            'platform' => 'web',
        ];

        $this->postJson('/api/v1/pairings/redeem', $payload)
            ->assertCreated();

        $payload['installation_id'] = 'another-installation-123456';

        $this->postJson('/api/v1/pairings/redeem', $payload)
            ->assertUnprocessable();
    }

    public function test_emitter_must_use_android(): void
    {
        $business = Business::factory()->create();

        $administrator = User::factory()->create([
            'business_id' => $business->id,
            'role_code' => User::ROLE_ADMINISTRATOR,
            'status' => User::STATUS_ACTIVE,
        ]);

        $issued = PairingCode::issue(
            $business,
            $administrator,
            Device::TYPE_EMITTER
        );

        $this->postJson('/api/v1/pairings/redeem', [
            'code' => $issued['plain_code'],
            'name' => 'Emisor incorrecto',
            'installation_id' => 'installation-web-123456789',
            'platform' => 'web',
        ])->assertUnprocessable();
    }

    public function test_expired_code_is_rejected(): void
    {
        $pairingCode = PairingCode::factory()->create([
            'expires_at' => now()->subMinute(),
        ]);

        $this->postJson('/api/v1/pairings/redeem', [
            'code' => 'MNP-ABCD-EFGH',
            'name' => 'Dispositivo',
            'installation_id' => 'installation-expired-123456',
            'platform' => 'web',
        ])->assertUnprocessable();

        $this->assertDatabaseCount('devices', 0);
    }
}