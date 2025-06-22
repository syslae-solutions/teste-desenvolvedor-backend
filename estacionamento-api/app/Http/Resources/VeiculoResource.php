<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VeiculoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Transforma o recurso (neste caso, um objeto Veiculo) em um array.
     * Este array será serializado para JSON e enviado como resposta da API.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, // O ID único do veículo.
            'placa' => $this->placa, // A placa do veículo.
            'cor' => $this->cor,     // A cor do veículo.
            'modelo' => $this->modelo, // O modelo do veículo.
            'tipo' => $this->tipo,   // O tipo do veículo (carro ou moto).

            // 'created_at' e 'updated_at' são colunas de timestamp gerenciadas pelo Laravel.
            // format garante um formato de data e hora consistente e legível no JSON.
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}