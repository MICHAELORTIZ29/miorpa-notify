<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Device;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Device>
 */
class DeviceFactory extends Factory
{
    protected $model = Device::class;

    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'authorized_by' => null,
            'name' => fake()->words(2, true),
            'type' => Device::TYPE_RECEIVER,
            'platform' => Device::PLATFORM_WEB,
            'status' => Device::STATUS_ACTIVE,
            'token_hash' => hash('sha256', fake()->uuid()),
            'installation_hash' => hash('sha256', fake()->uuid()),
            'app_version' => '1.0.0',
            'authorized_at' => now(),
        ];
    }

    public function emitter(): static
    {
        return $this->state(fn () => [
            'type' => Device::TYPE_EMITTER,
            'platform' => Device::PLATFORM_ANDROID,
        ]);
    }

    public function disabled(): static
    {
        return $this->state(fn () => [
            'status' => Device::STATUS_DISABLED,
            'disabled_at' => now(),
        ]);
    }
}