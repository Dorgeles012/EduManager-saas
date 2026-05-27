<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'tenant_id' => 1,
            'nom' => 'Admin',
            'prenom' => 'System',
            'telephone' => null,
            'image' => null,
            'email' => 'admin@test.com',
            'role' => 'SADMIN',
            'statut' => 'active',
        ]);
    }
}