<?php

namespace App\Services;

use App\Models\PaymentPushOutbox;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class PaymentPushDeliveryService
{
    public function __construct(private WebPushNotificationService $push) {}

    public function deliverNext(): bool
    {
        $token = (string) Str::uuid();
        $row = DB::transaction(function () use ($token) {
            $row = PaymentPushOutbox::query()
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('state', 'pending')->where(function ($query) {
                            $query->whereNull('next_attempt_at')->orWhere('next_attempt_at', '<=', now());
                        });
                    })->orWhere(function ($query) {
                        $query->where('state', 'processing')->where('lease_until', '<=', now());
                    });
                })->oldest('id')->lockForUpdate()->first();
            if (! $row) {
                return null;
            }
            $row->update(['state' => 'processing', 'attempts' => $row->attempts + 1,
                'lease_token' => $token, 'lease_until' => now()->addMinutes(5)]);

            return $row;
        }, 3);
        if (! $row) {
            return false;
        }

        $delivered = $row->delivered_subscription_ids ?? [];
        $error = null;
        $state = 'delivered';
        try {
            $result = $this->push->sendPaymentNotification($row->payment, $delivered, function ($subscriptionId) use (&$delivered, $row, $token) {
                $delivered[] = $subscriptionId;
                PaymentPushOutbox::query()->whereKey($row->id)->where('lease_token', $token)->update([
                    'delivered_subscription_ids' => json_encode(array_values(array_unique($delivered))),
                ]);
            });
            if ($result['subscriptions_found'] === 0) {
                $state = 'no_recipients';
            }
            $failures = [];
            foreach ($result['results'] as $report) {
                if ($report['success'] || $report['expired']) {
                    if (isset($report['subscription_id'])) {
                        $delivered[] = $report['subscription_id'];
                    }
                } else {
                    $failures[] = ($report['status_code'] ?? 'error').': '.($report['reason'] ?? 'Aviso rechazado');
                }
            }
            if ($failures) {
                $error = implode('; ', $failures);
            }
        } catch (Throwable $exception) {
            // Do not expose endpoint credentials or vendor exceptions to business users.
            $error = 'No se pudo enviar el aviso: '.class_basename($exception);
            report($exception);
        }
        if ($error !== null) {
            $state = $row->attempts >= 8 ? 'failed' : 'pending';
        }
        PaymentPushOutbox::query()->whereKey($row->id)->where('lease_token', $token)->update([
            'state' => $state, 'last_error' => $error === null ? null : mb_substr($error, 0, 1000),
            'delivered_subscription_ids' => json_encode(array_values(array_unique($delivered))),
            'next_attempt_at' => $state === 'pending' ? now()->addSeconds(min(3600, 30 * (2 ** min($row->attempts - 1, 7)))) : null,
            'lease_until' => null, 'lease_token' => null,
            'delivered_at' => in_array($state, ['delivered', 'no_recipients']) ? now() : null,
        ]);

        return true;
    }
}
