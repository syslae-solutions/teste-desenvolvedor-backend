<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            // Se você criou seeders para Vagas, Veículos, etc., adicione-os aqui:
            // VagaSeeder::class,
            // VeiculoSeeder::class,
            // EstacionamentoSeeder::class,
        ]);
    }
}