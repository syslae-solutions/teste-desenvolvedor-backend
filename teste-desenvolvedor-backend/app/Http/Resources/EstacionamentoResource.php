<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstacionamentoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vaga' => new VagaResource($this->whenLoaded('vaga')), // Carrega o recurso da vaga relacionada
            'veiculo' => new VeiculoResource($this->whenLoaded('veiculo')), // Carrega o recurso do veículo relacionado
            'entrada_at' => $this->entrada_at->format('Y-m-d H:i:s'),
            'saida_at' => $this->whenNotNull($this->saida_at?->format('Y-m-d H:i:s')),
            'valor' => $this->whenNotNull(number_format($this->valor, 2, '.', '')), // Garante formato de ponto para decimal
            'tempo_estacionado_minutos' => $this->saida_at ? $this->saida_at->diffInMinutes($this->entrada_at) : null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
