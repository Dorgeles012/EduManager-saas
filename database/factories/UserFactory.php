<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_id' => 1,

            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),

            'email' => fake()->unique()->safeEmail(),
            'telephone' => fake()->phoneNumber(),

            'password' => Hash::make('password'),

            'image' => null,

            'role' => 'SADMIN',
            'statut' => 'active',
        ];
    }
}