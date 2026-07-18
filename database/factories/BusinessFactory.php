<?php

namespace Database\Factories;

use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Business>
 */
class BusinessFactory extends Factory
{
    protected $model = Business::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'legal_name' => fake()->company(),
            'tax_id' => fake()->unique()->numerify('20#########'),
            'timezone' => 'America/Lima',
            'status' => Business::STATUS_TRIAL,
            'contact_name' => fake()->name(),
            'contact_email' => fake()->companyEmail(),
            'contact_phone' => fake()->numerify('9########'),
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => Business::STATUS_ACTIVE,
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn () => [
            'status' => Business::STATUS_SUSPENDED,
            'suspended_at' => now(),
        ]);
    }
}