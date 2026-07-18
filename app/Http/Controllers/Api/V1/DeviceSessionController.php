<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\DeviceHeartbeatRequest;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceSessionController extends Controller
{
    public function status(Request $request): JsonResponse
    {
        /** @var Device $device */
        $device = $request->attributes->get('device');

        return response()->json([
            'message' => 'Dispositivo autorizado.',
            'data' => [
                'device_id' => $device->public_id,
                'name' => $device->name,
                'type' => $device->type,
                'platform' => $device->platform,
                'status' => $device->status,
                'business' => [
                    'id' => $device->business->public_id,
                    'name' => $device->business->name,
                    'status' => $device->business->status,
                ],
                'server_time' => now()->toIso8601String(),
                'last_seen_at' => $device->last_seen_at?->toIso8601String(),
            ],
        ]);
    }

    public function heartbeat(
        DeviceHeartbeatRequest $request
    ): JsonResponse {
        /** @var Device $device */
        $device = $request->attributes->get('device');

        $validated = $request->validated();

        $updates = [
            'last_seen_at' => now(),
            'last_ip' => $request->ip(),
            'user_agent' => substr(
                (string) $request->userAgent(),
                0,
                500
            ),
        ];

        if (array_key_exists('app_version', $validated)) {
            $updates['app_version'] = $validated['app_version'];
        }

        if (array_key_exists('capabilities', $validated)) {
            $updates['capabilities'] = $validated['capabilities'];
        }

        $device->update($updates);

        return response()->json([
            'message' => 'Sincronización actualizada.',
            'data' => [
                'device_id' => $device->public_id,
                'status' => $device->status,
                'server_time' => now()->toIso8601String(),
                'next_heartbeat_seconds' => 60,
            ],
        ]);
    }
}