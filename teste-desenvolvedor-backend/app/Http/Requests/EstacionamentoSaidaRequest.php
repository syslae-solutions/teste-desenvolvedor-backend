<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Estacionamento;

class EstacionamentoSaidaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // A autenticação será verificada via middleware 'auth:sanctum'
    }

    public function rules(): array
    {
        return [
            'estacionamento_id' => [
                'required',
                'exists:estacionamentos,id',
                function ($attribute, $value, $fail) {
                    $estacionamento = Estacionamento::find($value);
                    // Regra: Não permitir saída se a operação já possui data de saída
                    if ($estacionamento && $estacionamento->saida_at !== null) {
                        $fail('Esta operação de estacionamento já possui uma data de saída registrada.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'estacionamento_id.required' => 'O ID da operação de estacionamento é obrigatório.',
            'estacionamento_id.exists' => 'Operação de estacionamento não encontrada.',
            // Mensagens personalizadas da validação 'function' serão geradas dinamicamente
        ];
    }
}