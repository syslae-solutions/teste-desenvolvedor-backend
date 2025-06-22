<?php

namespace Database\Seeders;

use App\Models\User;

// Importando os modelos
use App\Models\Vaga;
use App\Models\Veiculo;
use App\Models\Estacionamento;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Criar 10 vagas fictícias
        Vaga::factory()->count(10)->create();

        // Criar 20 veículos fictícios
        Veiculo::factory()->count(20)->create();

        // Criar 50 registros de estacionamento.
        Estacionamento::factory()->count(50)->create();
    }
}
