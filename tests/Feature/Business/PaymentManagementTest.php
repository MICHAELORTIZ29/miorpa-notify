<?php

namespace Tests\Feature\Business;

use App\Models\Business;
use App\Models\Device;
use App\Models\Payment;
use App\Models\PaymentAcknowledgement;
use App\Models\PaymentProvider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createPayment(Business $business): Payment
    {
        $provider = PaymentProvider::query()->create([
            'code' => 'yape',
            'name' => 'Yape',
            'status' => PaymentProvider::STATUS_ACTIVE,
        ]);

        $device = Device::factory()->emitter()->create([
            'business_id' => $business->id,
        ]);

        return Payment::query()->create([
            'business_id' => $business->id,
            'payment_provider_id' => $provider->id,
            'emitter_device_id' => $device->id,
            'source_event_hash' => hash('sha256', fake()->uuid()),
            'payer_name' => 'Cliente de prueba',
            'amount' => '25.00',
            'currency' => 'PEN',
            'status' => Payment::STATUS_RECEIVED,
            'occurred_at' => now(),
            'received_at' => now(),
        ]);
    }

    public function test_cashier_can_confirm_payment_from_own_business(): void
    {
        $business = Business::factory()->create([
            'status' => Business::STATUS_ACTIVE,
        ]);

        $cashier = User::factory()->create([
            'business_id' => $business->id,
            'role_code' => User::ROLE_CASHIER,
            'status' => User::STATUS_ACTIVE,
        ]);

        $payment = $this->createPayment($business);

        $this
            ->actingAs($cashier)
            ->patch(route('business.payments.confirm', $payment))
            ->assertRedirect(
                route('business.payments.show', $payment)
            );

        $this->assertDatabaseHas('payment_acknowledgements', [
            'payment_id' => $payment->id,
            'user_id' => $cashier->id,
        ]);

        $acknowledgement = PaymentAcknowledgement::query()
            ->firstOrFail();

        $this->assertNotNull($acknowledgement->confirmed_at);
    }

    public function test_administrator_can_confirm_payment(): void
    {
        $business = Business::factory()->create([
            'status' => Business::STATUS_ACTIVE,
        ]);

        $administrator = User::factory()->create([
            'business_id' => $business->id,
            'role_code' => User::ROLE_ADMINISTRATOR,
            'status' => User::STATUS_ACTIVE,
        ]);

        $payment = $this->createPayment($business);

        $this
            ->actingAs($administrator)
            ->patch(route('business.payments.confirm', $payment))
            ->assertRedirect();
    }

    public function test_user_cannot_confirm_payment_from_other_business(): void
    {
        $firstBusiness = Business::factory()->create([
            'status' => Business::STATUS_ACTIVE,
        ]);

        $secondBusiness = Business::factory()->create([
            'status' => Business::STATUS_ACTIVE,
        ]);

        $cashier = User::factory()->create([
            'business_id' => $firstBusiness->id,
            'role_code' => User::ROLE_CASHIER,
            'status' => User::STATUS_ACTIVE,
        ]);

        $foreignPayment = $this->createPayment($secondBusiness);

        $this
            ->actingAs($cashier)
            ->patch(
                route('business.payments.confirm', $foreignPayment)
            )
            ->assertNotFound();

        $this->assertDatabaseCount(
            'payment_acknowledgements',
            0
        );
    }
}