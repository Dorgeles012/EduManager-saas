<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class SadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'dorgeles@mail.com'],
            [
                'tenant_id' => 1,
                'nom' => 'Admin',
                'prenom' => 'System',
                'telephone' => null,
                'image' => null,
                'password' => bcrypt('12345678'),
                'role' => 'SADMIN',
                'statut' => 'active',
                'email_verified_at' => now(),
            ]
        );
    }
}
