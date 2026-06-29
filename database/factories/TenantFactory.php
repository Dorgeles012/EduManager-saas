<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TenantFactory extends Factory
{
    public function definition(): array
    {
        return ['nom_entreprise' => fake()->company(), 'email' => fake()->unique()->companyEmail(), 'statut' => 'active'];
    }
}
