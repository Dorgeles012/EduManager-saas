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
        User::create([
            'nom' => 'Admin',
            'email' => 'dorgeles@mail.com',
            'password' => bcrypt('password'),
        ])->assignRole('SADMIN');
    }
}