<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\Device;
use App\Models\Payment;
use App\Models\PaymentProvider;
use App\Models\User;
use Illuminate\Console\Command;

class SimulatePaymentCommand extends Command
{
    protected $signature = 'miorpa:simulate-payment
        {--business= : Public ID del negocio}
        {--amount=25.50 : Monto del pago}
        {--payer=Cliente de prueba : Nombre del pagador}';

    protected $description = 'Simula un pago local para verificar el sistema';

    public function handle(): int
    {
        if (! app()->environment(['local', 'testing'])) {
            $this->error(
                'Este comando solamente está disponible localmente.'
            );

            return self::FAILURE;
        }

        $businessQuery = Business::query()
            ->whereIn('status', [
                Business::STATUS_ACTIVE,
                Business::STATUS_TRIAL,
            ]);

        if ($this->option('business')) {
            $businessQuery->where(
                'public_id',
                $this->option('business')
            );
        }

        $business = $businessQuery->first();

        if (! $business) {
            $this->error('No se encontró un negocio operativo.');

            return self::FAILURE;
        }

        $provider = PaymentProvider::query()
            ->where('code', 'yape')
            ->where('status', PaymentProvider::STATUS_ACTIVE)
            ->first();

        if (! $provider) {
            $this->error(
                'Yape no está registrado. Ejecuta PaymentProviderSeeder.'
            );

            return self::FAILURE;
        }

        $administrator = User::query()
            ->where('business_id', $business->id)
            ->where('role_code', User::ROLE_ADMINISTRATOR)
            ->first();

        if (! $administrator) {
            $this->error('El negocio no tiene administrador.');

            return self::FAILURE;
        }

        $device = Device::query()
            ->where('business_id', $business->id)
            ->where('type', Device::TYPE_EMITTER)
            ->where('status', Device::STATUS_ACTIVE)
            ->first();

        if (! $device) {
            $device = Device::query()->create([
                'business_id' => $business->id,
                'authorized_by' => $administrator->id,
                'name' => 'Emisor local de pruebas',
                'type' => Device::TYPE_EMITTER,
                'platform' => Device::PLATFORM_ANDROID,
                'status' => Device::STATUS_ACTIVE,
                'installation_hash' => hash(
                    'sha256',
                    'local-simulator-' . $business->id
                ),
                'app_version' => 'simulator-1.0',
                'authorized_at' => now(),
                'last_seen_at' => now(),
            ]);
        }

        $amount = number_format(
            (float) $this->option('amount'),
            2,
            '.',
            ''
        );

        if ((float) $amount <= 0) {
            $this->error('El monto debe ser mayor que cero.');

            return self::FAILURE;
        }

        $eventId = 'simulation-' . now()->format('YmdHisv');

        $payment = Payment::query()->create([
            'business_id' => $business->id,
            'payment_provider_id' => $provider->id,
            'emitter_device_id' => $device->id,
            'source_event_hash' => hash(
                'sha256',
                'yape|' . $eventId
            ),
            'payer_name' => $this->option('payer'),
            'amount' => $amount,
            'currency' => 'PEN',
            'status' => Payment::STATUS_RECEIVED,
            'parser_version' => 'simulator-1.0',
            'occurred_at' => now(),
            'received_at' => now(),
            'raw_payload' => [
                'simulation' => true,
                'title' => 'Recibiste un pago',
                'text' => sprintf(
                    '%s te pagó S/ %s',
                    $this->option('payer'),
                    $amount
                ),
            ],
            'metadata' => [
                'environment' => 'local',
            ],
        ]);

        $this->newLine();
        $this->info('Pago simulado correctamente.');
        $this->line("Negocio: {$business->name}");
        $this->line("Cliente: {$payment->payer_name}");
        $this->line("Monto: S/ {$payment->amount}");
        $this->line("ID: {$payment->public_id}");

        return self::SUCCESS;
    }
}