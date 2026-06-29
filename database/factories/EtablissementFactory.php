<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class EtablissementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(), 'nom' => fake()->company(),
            'email' => fake()->unique()->companyEmail(), 'telephone' => fake()->phoneNumber(),
            'type_etablissement' => 'college', 'statut' => 'active',
        ];
    }
}
