<?php

namespace Database\Factories;

use App\Models\Vaga;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vaga>
 */
class VagaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vaga::class; // Associe a Factory ao modelo Vaga

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'codigo' => $this->faker->unique()->lexify('VAGA-????'), // O código da Vaga é VAGA-abcd
            'rua' => $this->faker->streetName(), 
            'numero' => $this->faker->buildingNumber(),
            'bairro' => $this->faker->city(),
            'status' => $this->faker->randomElement(['livre', 'ocupada', 'interditada']),
        ];
    }
}
