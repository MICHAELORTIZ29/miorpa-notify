<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Services\SubscriptionStatusService;
use Illuminate\Console\Command;

class SynchronizeSubscriptionsCommand extends Command
{
    protected $signature =
        'subscriptions:sync-status';

    protected $description =
        'Actualiza vencimientos y suspensiones de suscripciones';

    public function handle(
        SubscriptionStatusService $service
    ): int {
        $reviewed = 0;

        Subscription::query()
            ->with('business')
            ->whereNull('ended_at')
            ->chunkById(
                100,
                function ($subscriptions) use (
                    $service,
                    &$reviewed
                ): void {
                    foreach ($subscriptions as $subscription) {
                        $service->synchronize($subscription);
                        $reviewed++;
                    }
                }
            );

        $this->info(
            "Suscripciones revisadas: {$reviewed}"
        );

        return self::SUCCESS;
    }
}