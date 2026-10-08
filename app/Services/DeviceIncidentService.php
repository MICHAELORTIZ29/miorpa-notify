<?php

namespace App\Services;

use App\Models\Device;
use App\Models\DeviceIncident;
use App\Models\NotificationCapture;
use Illuminate\Support\Facades\DB;

class DeviceIncidentService
{
    public function refresh(Device $device): void
    {
        // Device lock serializes heartbeat and scheduler transitions: one open incident per kind.
        DB::transaction(function () use ($device) {
            $device = Device::whereKey($device->id)->lockForUpdate()->firstOrFail();
            $conditions = [];
            $d = $device->diagnostics ?? [];
            if ($device->isActive() && $device->type === Device::TYPE_EMITTER) {
                $fresh = $device->diagnostics_received_at?->greaterThan(now()->subMinutes(3)) ?? false;
                if (! $fresh) {
                    $conditions['offline'] = 'El teléfono no ha enviado diagnóstico durante más de 3 minutos.';
                }
                // Stale diagnostic values cannot prove a recovery or a new reader failure.
                if ($fresh) {
                    if (array_key_exists('notification_permission', $d) && ! $d['notification_permission']) {
                        $conditions['permission'] = 'El teléfono perdió el permiso para leer notificaciones.';
                    } elseif (array_key_exists('listener_connected', $d) && ! $d['listener_connected']) {
                        $conditions['reader'] = 'El lector está desconectado de Android.';
                    }
                    if (($d['blocked_payments'] ?? 0) > 0) {
                        $conditions['blocked'] = 'Hay pagos bloqueados que requieren revisión.';
                    }
                    if (($d['pending_payments'] ?? 0) > 0 && ($d['oldest_pending_at'] ?? 0) > 0 && $d['oldest_pending_at'] < now()->subMinutes(3)->getTimestampMs()) {
                        $conditions['backlog'] = 'Hay pagos guardados que llevan más de 3 minutos sin confirmación del servidor.';
                    }
                    if (! empty($d['capture_error'])) {
                        $conditions['capture_error'] = 'Error del lector: '.mb_substr($d['capture_error'], 0, 450);
                    }
                }
                if (NotificationCapture::where('device_id', $device->id)->whereIn('state', ['unrecognized', 'parse_error'])->whereNull('reviewed_at')->exists()) {
                    $conditions['unrecognized'] = 'Hay notificaciones de billetera sin pago identificado pendientes de revisión.';
                }
            }
            $open = DeviceIncident::where('device_id', $device->id)->whereNull('resolved_at')->get()->keyBy('kind');
            foreach ($conditions as $kind => $message) {
                if (isset($open[$kind])) {
                    $open[$kind]->update(['last_observed_at' => now(), 'message' => $message]);
                } else {
                    DeviceIncident::create(['business_id' => $device->business_id, 'device_id' => $device->id, 'kind' => $kind, 'message' => $message, 'opened_at' => now(), 'last_observed_at' => now()]);
                }
            }
            foreach ($open as $kind => $incident) {
                if (! isset($conditions[$kind])) {
                    if (isset($conditions['offline']) && in_array($kind, ['reader', 'permission', 'blocked', 'backlog', 'capture_error'])) {
                        continue;
                    }
                    $incident->update(['resolved_at' => now()]);
                }
            }
        }, 3);
    }
}
