<?php

namespace Database\Seeders;

use App\Models\PaymentProvider;
use Illuminate\Database\Seeder;

class PaymentProviderSeeder extends Seeder
{
    public function run(): void
    {
        PaymentProvider::query()->updateOrCreate(
            ['code' => 'yape'],
            [
                'name' => 'Yape',
                'android_packages' => [],
                'status' => PaymentProvider::STATUS_ACTIVE,
                'configuration' => [
                    'parser_enabled' => true,
                    'currency' => 'PEN',
                ],
            ]
        );
    }
}