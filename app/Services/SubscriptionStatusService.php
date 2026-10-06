<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

class SubscriptionStatusService
{
    public function synchronize(
        Subscription $subscription
    ): string {
        $subscription->loadMissing('business');

        $business = $subscription->business;

        if (
            $subscription->status ===
            Subscription::STATUS_CANCELLED
        ) {
            return $subscription->status;
        }

        $now = now();

        $graceEnd =
            $subscription->grace_ends_at
            ?? $subscription->current_period_ends_at;

        if (
            $subscription->auto_suspend &&
            $now->greaterThan($graceEnd)
        ) {
            $subscriptionStatus =
                Subscription::STATUS_SUSPENDED;

            $automaticBusinessStatus =
                Business::STATUS_SUSPENDED;

            $automaticSuspensionReason =
                Business::SUSPENSION_NONPAYMENT;
        } elseif (
            $now->greaterThan(
                $subscription->current_period_ends_at
            )
        ) {
            $subscriptionStatus =
                Subscription::STATUS_OVERDUE;

            $automaticBusinessStatus =
                Business::STATUS_OVERDUE;

            $automaticSuspensionReason = null;
        } else {
            $subscriptionStatus =
                $subscription->status ===
                Subscription::STATUS_TRIAL
                    ? Subscription::STATUS_TRIAL
                    : Subscription::STATUS_ACTIVE;

            $automaticBusinessStatus =
                $subscriptionStatus ===
                Subscription::STATUS_TRIAL
                    ? Business::STATUS_TRIAL
                    : Business::STATUS_ACTIVE;

            $automaticSuspensionReason = null;
        }

        /*
         * Si el superadministrador reactivó manualmente un negocio cuyo
         * periodo de gracia ya terminó, mantenemos el estado financiero de
         * la suscripción como suspendido, pero permitimos que el negocio siga
         * operando. Así el cron no vuelve a bloquearlo en la siguiente petición.
         */
        $manualAccessOverride =
            (bool) $business->manual_access_override;

        if (
            $manualAccessOverride &&
            $subscriptionStatus ===
                Subscription::STATUS_SUSPENDED
        ) {
            $automaticBusinessStatus =
                Business::STATUS_OVERDUE;

            $automaticSuspensionReason = null;
        }

        /*
         * Una suspensión manual nunca debe ser eliminada automáticamente.
         * También consideramos manuales las suspensiones antiguas que todavía
         * no tengan motivo registrado.
         */
        $isManuallySuspended =
            $business->status ===
                Business::STATUS_SUSPENDED
            &&
            $business->suspension_reason !==
                Business::SUSPENSION_NONPAYMENT;

        DB::transaction(
            function () use (
                $subscription,
                $business,
                $subscriptionStatus,
                $automaticBusinessStatus,
                $automaticSuspensionReason,
                $isManuallySuspended,
                $manualAccessOverride
            ): void {
                $subscriptionChanges = [];

                if (
                    $subscription->status !==
                    $subscriptionStatus
                ) {
                    $subscriptionChanges['status'] =
                        $subscriptionStatus;
                }

                if (
                    $subscriptionStatus ===
                    Subscription::STATUS_SUSPENDED
                ) {
                    if (
                        $subscription->suspended_at === null
                    ) {
                        $subscriptionChanges['suspended_at'] = now();
                    }
                } elseif (
                    $subscription->suspended_at !== null
                ) {
                    $subscriptionChanges['suspended_at'] = null;
                }

                if ($subscriptionChanges !== []) {
                    $subscription->update($subscriptionChanges);
                }

                if (
                    $business->status ===
                    Business::STATUS_CLOSED
                ) {
                    return;
                }

                if ($isManuallySuspended) {
                    return;
                }

                $businessChanges = [];

                if (
                    $business->status !==
                    $automaticBusinessStatus
                ) {
                    $businessChanges['status'] =
                        $automaticBusinessStatus;
                }

                if (
                    $business->suspension_reason !==
                    $automaticSuspensionReason
                ) {
                    $businessChanges['suspension_reason'] =
                        $automaticSuspensionReason;
                }

                if (
                    $automaticBusinessStatus ===
                    Business::STATUS_SUSPENDED
                ) {
                    if ($business->suspended_at === null) {
                        $businessChanges['suspended_at'] = now();
                    }
                } elseif ($business->suspended_at !== null) {
                    $businessChanges['suspended_at'] = null;
                }

                /*
                 * Cuando la suscripción vuelve a estar dentro de un estado
                 * operativo (por renovación/cambio de fechas), el permiso
                 * excepcional ya no es necesario y se limpia solo.
                 */
                if (
                    $manualAccessOverride &&
                    $subscriptionStatus !==
                        Subscription::STATUS_SUSPENDED
                ) {
                    $businessChanges['manual_access_override'] = false;
                }

                if ($businessChanges !== []) {
                    $business->update($businessChanges);
                }
            }
        );

        return $subscriptionStatus;
    }
}
