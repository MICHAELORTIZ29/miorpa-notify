<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            'pilot' => [
                'name' => 'Piloto',
                'description' =>
                    'Plan inicial para negocios en etapa de prueba.',
                'emitters' => 1,
                'receivers' => 3,
                'cashiers' => 3,
            ],

            'business' => [
                'name' => 'Negocio',
                'description' =>
                    'Plan para negocios con más dispositivos y cajeros.',
                'emitters' => 2,
                'receivers' => 10,
                'cashiers' => 10,
            ],
        ];

        foreach ($plans as $code => $data) {
            $plan = Plan::query()->updateOrCreate(
                [
                    'code' => $code,
                ],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'status' => Plan::STATUS_ACTIVE,
                    'version' => 1,
                ]
            );

            $limits = [
                Subscription::LIMIT_EMITTERS =>
                    $data['emitters'],

                Subscription::LIMIT_RECEIVERS =>
                    $data['receivers'],

                Subscription::LIMIT_CASHIERS =>
                    $data['cashiers'],
            ];

            foreach ($limits as $limitCode => $value) {
                $plan->limits()->updateOrCreate(
                    [
                        'limit_code' => $limitCode,
                    ],
                    [
                        'numeric_value' => $value,
                    ]
                );
            }
        }
    }
}