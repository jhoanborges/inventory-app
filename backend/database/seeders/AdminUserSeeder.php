<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@bodega.com',
            'password' => bcrypt('password'),
        ]);

        $admin->assignRole('admin');
    }
}
