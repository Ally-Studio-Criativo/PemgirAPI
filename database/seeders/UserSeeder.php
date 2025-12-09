<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@pemgir.com.br',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);

        // Create test user
        User::create([
            'name' => 'Usuário Teste',
            'email' => 'teste@pemgir.com.br',
            'password' => Hash::make('teste123'),
            'email_verified_at' => now(),
        ]);
    }
}
