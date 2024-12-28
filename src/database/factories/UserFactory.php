<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'postal_code' => $this->faker->optional()->postcode(),
            'address' => $this->faker->optional()->address(),
            'building' => $this->faker->optional()->secondaryAddress,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}