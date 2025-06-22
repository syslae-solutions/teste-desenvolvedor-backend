<?php

namespace Database\Factories;

// importar o Modelo do veículo para adicionar os dados fictícios
use App\Models\Veiculo;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Veiculo>
 */

class VeiculoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Veiculo::class; // Associe a Factory ao modelo Veiculo

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {   
        // Gerar dados fictícios para as colunas da tabela veiculos
        return [
            'placa' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{1}[A-Z]{1}[0-9]{2}'), // Padrão de placa Mercosul
            'cor' => $this->faker->colorName(),
            'modelo' => $this->faker->word().' '.$this->faker->word(), // O modelo é composto por Carro Modelo
            'tipo' => $this->faker->randomElement(['carro', 'moto']),
        ];
    }
}