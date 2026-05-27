<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'SADMIN']);
        Role::create(['name' => 'CLIENT']);
        Role::create(['name' => 'PERSONNEL']);
        Role::create(['name' => 'ENSEIGNANT']);
        Role::create(['name' => 'PARENT']);
    }
}