<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('miorpa.superadmin.email');
        $password = config('miorpa.superadmin.password');

        if (blank($email) || blank($password)) {
            $this->command?->warn(
                'No se creó el superadministrador: faltan SUPERADMIN_EMAIL y SUPERADMIN_PASSWORD.'
            );

            return;
        }

        User::firstOrCreate(
            ['email' => mb_strtolower($email)],
            [
                'business_id' => null,
                'role_code' => User::ROLE_SUPERADMIN,
                'name' => config('miorpa.superadmin.name'),
                'password' => $password,
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
                'password_changed_at' => now(),
            ]
        );
    }
}