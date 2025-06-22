<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstacionamentoResource extends JsonResource
{
    /**
     * Transforma o recurso (um objeto Estacionamento) em um array para a resposta JSON.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vaga_id' => $this->vaga_id,
            'veiculo_id' => $this->veiculo_id,
            'entrada' => $this->entrada ? $this->entrada->format('Y-m-d H:i:s') : null, // Formata data de entrada
            'saida' => $this->saida ? $this->saida->format('Y-m-d H:i:s') : null,     // Formata data de saída (pode ser nula)
            'tempo_total' => $this->tempo_total, // Tempo total em minutos (pode ser nulo)
            'valor' => $this->valor ? number_format($this->valor, 2, ',', '.') : null, // Valor formatado (pode ser nulo)

            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),

            // Inclui os recursos relacionados (Vaga e Veiculo) APENAS se eles foram carregados.
            'vaga' => new VagaResource($this->whenLoaded('vaga')),
            'veiculo' => new VeiculoResource($this->whenLoaded('veiculo')),
        ];
    }
}