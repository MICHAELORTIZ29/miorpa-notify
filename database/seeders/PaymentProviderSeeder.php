<?php

namespace Database\Seeders;

use App\Models\PaymentProvider;
use Illuminate\Database\Seeder;

class PaymentProviderSeeder extends Seeder
{
    public function run(): void
    {
        PaymentProvider::query()->updateOrCreate(
            [
                'code' => 'yape',
            ],
            [
                'name' => 'Yape',

                'android_packages' => [
                    'com.bcp.innovacxion.yapeapp',
                ],

                'status' =>
                    PaymentProvider::STATUS_ACTIVE,

                'configuration' => [
                    'parser_enabled' => true,
                    'currency' => 'PEN',
                    'parser_version' =>
                        'yape-1.0.0',
                ],
            ]
        );

        PaymentProvider::query()->updateOrCreate(
            [
                'code' => 'plin',
            ],
            [
                'name' => 'Plin',

                'android_packages' => [
                    'pe.com.interbank.mobilebanking',
                ],

                'status' =>
                    PaymentProvider::STATUS_ACTIVE,

                'configuration' => [
                    'parser_enabled' => true,
                    'currency' => 'PEN',

                    'supported_sources' => [
                        'interbank',
                    ],

                    'parser_versions' => [
                        'interbank' =>
                            'plin-interbank-1.0.0',
                    ],
                ],
            ]
        );

        PaymentProvider::query()->updateOrCreate(
            [
                'code' => 'bim',
            ],
            [
                'name' => 'BIM',
                'android_packages' => [],

                'status' =>
                    PaymentProvider::STATUS_DISABLED,

                'configuration' => [
                    'parser_enabled' => false,
                    'currency' => 'PEN',
                ],
            ]
        );
    }
}