<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'role_code' => User::ROLE_CASHIER,
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('9########'),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'status' => User::STATUS_ACTIVE,
            'remember_token' => Str::random(10),
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(fn () => [
            'business_id' => null,
            'role_code' => User::ROLE_SUPERADMIN,
        ]);
    }

    public function administrator(): static
    {
        return $this->state(fn () => [
            'role_code' => User::ROLE_ADMINISTRATOR,
        ]);
    }

    public function cashier(): static
    {
        return $this->state(fn () => [
            'role_code' => User::ROLE_CASHIER,
        ]);
    }

    public function disabled(): static
    {
        return $this->state(fn () => [
            'status' => User::STATUS_DISABLED,
            'disabled_at' => now(),
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn () => [
            'email_verified_at' => null,
        ]);
    }
}