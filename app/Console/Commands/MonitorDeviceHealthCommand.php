<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Services\DeviceIncidentService;
use Illuminate\Console\Command;

class MonitorDeviceHealthCommand extends Command
{
    protected $signature = 'devices:monitor-health';

    protected $description = 'Registra desconexiones, pagos retenidos y recuperaciones de los lectores';

    public function handle(DeviceIncidentService $service): int
    {
        Device::where('type', Device::TYPE_EMITTER)->chunkById(100, function ($devices) use ($service) {
            foreach ($devices as $device) {
                $service->refresh($device);
            }
        });

        return self::SUCCESS;
    }
}
