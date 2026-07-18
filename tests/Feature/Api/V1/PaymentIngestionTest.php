<?php

namespace Tests\Feature\Api\V1;

use App\Models\Business;
use App\Models\Device;
use App\Models\Payment;
use App\Models\PaymentProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentIngestionTest extends TestCase
{
    use RefreshDatabase;

    private function createEmitter(string $plainToken): Device
    {
        $business = Business::factory()->create([
            'status' => Business::STATUS_ACTIVE,
        ]);

        return Device::factory()->emitter()->create([
            'business_id' => $business->id,
            'status' => Device::STATUS_ACTIVE,
            'token_hash' => hash('sha256', $plainToken),
        ]);
    }

    private function createYapeProvider(): PaymentProvider
    {
        return PaymentProvider::query()->create([
            'code' => 'yape',
            'name' => 'Yape',
            'status' => PaymentProvider::STATUS_ACTIVE,
            'android_packages' => [],
        ]);
    }

    public function test_emitter_can_send_payment(): void
    {
        $plainToken = 'emitter-payment-token-123456';
        $device = $this->createEmitter($plainToken);
        $this->createYapeProvider();

        $response = $this
            ->withToken($plainToken)
            ->postJson('/api/v1/device/payments', [
                'provider_code' => 'yape',
                'event_id' => 'notification-123456',
                'payer_name' => 'María López',
                'amount' => '25.50',
                'currency' => 'PEN',
                'occurred_at' => now()->toIso8601String(),
                'parser_version' => 'yape-1.0.0',
                'raw_payload' => [
                    'title' => 'Recibiste un pago',
                    'text' => 'María te pagó S/ 25.50',
                ],
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.duplicate', false)
            ->assertJsonPath('data.status', Payment::STATUS_RECEIVED);

        $this->assertDatabaseHas('payments', [
            'business_id' => $device->business_id,
            'emitter_device_id' => $device->id,
            'payer_name' => 'María López',
            'amount' => '25.50',
            'currency' => 'PEN',
        ]);

        $payment = Payment::query()->firstOrFail();

        $this->assertSame(
            'Recibiste un pago',
            $payment->raw_payload['title']
        );
    }

    public function test_same_event_is_not_stored_twice(): void
    {
        $plainToken = 'duplicate-payment-token-123';
        $this->createEmitter($plainToken);
        $this->createYapeProvider();

        $payload = [
            'provider_code' => 'yape',
            'event_id' => 'same-notification-123',
            'payer_name' => 'Carlos',
            'amount' => '10.00',
            'currency' => 'PEN',
            'occurred_at' => now()->toIso8601String(),
        ];

        $this
            ->withToken($plainToken)
            ->postJson('/api/v1/device/payments', $payload)
            ->assertCreated()
            ->assertJsonPath('data.duplicate', false);

        $this
            ->withToken($plainToken)
            ->postJson('/api/v1/device/payments', $payload)
            ->assertOk()
            ->assertJsonPath('data.duplicate', true);

        $this->assertDatabaseCount('payments', 1);
    }

    public function test_receiver_cannot_send_payments(): void
    {
        $plainToken = 'receiver-payment-token-123';

        $business = Business::factory()->create([
            'status' => Business::STATUS_ACTIVE,
        ]);

        Device::factory()->create([
            'business_id' => $business->id,
            'type' => Device::TYPE_RECEIVER,
            'platform' => Device::PLATFORM_WEB,
            'token_hash' => hash('sha256', $plainToken),
        ]);

        $this->createYapeProvider();

        $this
            ->withToken($plainToken)
            ->postJson('/api/v1/device/payments', [
                'provider_code' => 'yape',
                'event_id' => 'event-receiver-123',
                'amount' => '20.00',
                'currency' => 'PEN',
                'occurred_at' => now()->toIso8601String(),
            ])
            ->assertForbidden()
            ->assertJsonPath('code', 'DEVICE_NOT_EMITTER');

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_unknown_provider_is_rejected(): void
    {
        $plainToken = 'unknown-provider-token-123';
        $this->createEmitter($plainToken);

        $this
            ->withToken($plainToken)
            ->postJson('/api/v1/device/payments', [
                'provider_code' => 'unknown',
                'event_id' => 'event-unknown-123',
                'amount' => '15.00',
                'currency' => 'PEN',
                'occurred_at' => now()->toIso8601String(),
            ])
            ->assertUnprocessable();

        $this->assertDatabaseCount('payments', 0);
    }
}