<?php

namespace App\Console\Commands;

use App\Services\PaymentPushDeliveryService;
use Illuminate\Console\Command;

class DeliverPaymentNotificationsCommand extends Command
{
    protected $signature = 'payments:deliver-notifications {--max=100}';

    protected $description = 'Procesa avisos persistentes sin bloquear la recepción de pagos.';

    public function handle(PaymentPushDeliveryService $delivery): int
    {
        $deadline = microtime(true) + 45;
        $count = 0;
        $max = max(1, min(100, (int) $this->option('max')));
        while ($count < $max && microtime(true) < $deadline && $delivery->deliverNext()) {
            $count++;
        }
        $this->info("Avisos procesados: {$count}");

        return self::SUCCESS;
    }
}
