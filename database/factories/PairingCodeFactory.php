<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Device;
use App\Models\PairingCode;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PairingCode>
 */
class PairingCodeFactory extends Factory
{
    protected $model = PairingCode::class;

    public function definition(): array
    {
        $plainCode = 'MNP-' . strtoupper(fake()->bothify('####-????'));

        return [
            'business_id' => Business::factory(),
            'created_by' => User::factory(),
            'used_by_device_id' => null,
            'code_hash' => PairingCode::hashPlainCode(
                $plainCode . fake()->uuid()
            ),
            'code_suffix' => strtoupper(fake()->lexify('????')),
            'device_type' => Device::TYPE_RECEIVER,
            'max_uses' => 1,
            'uses_count' => 0,
            'expires_at' => now()->addMinutes(10),
            'used_at' => null,
            'revoked_at' => null,
        ];
    }
}