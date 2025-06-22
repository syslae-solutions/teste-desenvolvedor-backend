<?php

namespace Database\Factories;

use App\Models\Estacionamento; // Importe o modelo Estacionamento
use App\Models\Vaga;         // Importe o modelo Vaga
use App\Models\Veiculo;      // Importe o modelo Veiculo

use Illuminate\Database\Eloquent\Factories\Factory;

use Carbon\Carbon; // Para trabalhar com datas e horas

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Estacionamento>
 */
class EstacionamentoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Estacionamento::class; // Associe a Factory ao modelo Estacionamento

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Gera uma data de entrada aleatória nos últimos 30 dias
        $entrada = $this->faker->dateTimeBetween('-30 days', 'now');

        // 50% de chance de o veículo já ter saído (saida não nula)
        $saida = null;
        $tempoTotal = null;
        $valor = null;

        if ($this->faker->boolean(50)) { // 50% de chance
            $saida = $this->faker->dateTimeBetween($entrada, 'now');
            // Calcula a diferença em minutos
            $tempoTotal = Carbon::parse($entrada)->diffInMinutes(Carbon::parse($saida));
            // Calcula o valor total sendo 2,00 por hora
            $valor = round($tempoTotal / 60 * 2, 2);
        }

        return [
            'vaga_id' => Vaga::factory(), // Cria uma nova vaga ou usa uma existente.
            'veiculo_id' => Veiculo::factory(), // Cria um novo veículo ou usa um existente.
            'entrada' => $entrada,
            'saida' => $saida,
            'tempo_total' => $tempoTotal,
            'valor' => $valor,
        ];
    }
}
