<?php

namespace App\Http\Middleware;

use App\Models\Business;
use App\Models\Device;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateDevice
{
    public function handle(
        Request $request,
        Closure $next
    ): Response|JsonResponse {
        $plainToken = $request->bearerToken();

        if (! $plainToken) {
            return response()->json([
                'message' => 'Token de dispositivo requerido.',
                'code' => 'DEVICE_TOKEN_REQUIRED',
            ], 401);
        }

        $device = Device::query()
            ->with('business')
            ->where('token_hash', hash('sha256', $plainToken))
            ->first();

        if (! $device) {
            return response()->json([
                'message' => 'Token de dispositivo no válido.',
                'code' => 'DEVICE_TOKEN_INVALID',
            ], 401);
        }

        if ($device->status !== Device::STATUS_ACTIVE) {
            return response()->json([
                'message' => 'El dispositivo no está autorizado.',
                'code' => $device->status === Device::STATUS_REVOKED
                    ? 'DEVICE_REVOKED'
                    : 'DEVICE_DISABLED',
            ], 403);
        }

        if (
            ! in_array($device->business->status, [
                Business::STATUS_ACTIVE,
                Business::STATUS_TRIAL,
            ], true)
        ) {
            return response()->json([
                'message' => 'El negocio no se encuentra operativo.',
                'code' => 'BUSINESS_NOT_OPERATIONAL',
            ], 403);
        }

        $request->attributes->set('device', $device);

        return $next($request);
    }
}