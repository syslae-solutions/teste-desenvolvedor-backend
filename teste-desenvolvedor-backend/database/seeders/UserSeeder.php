<?php

namespace Database\Seeders;

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
        // Cria um usuário 'Admin' se ele ainda não existir
        User::firstOrCreate(
            ['email' => 'admin@example.com'], // Condição para encontrar um usuário existente
            [
                'name' => 'Admin User', // Nome do usuário
                'password' => Hash::make('password'), // Senha criptografada (pode ser "password" para desenvolvimento)
            ]
        );

        // Exemplo de criação de outros usuários se necessário
        // User::factory(10)->create();
    }
}
