<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Vaga;
use App\Models\Estacionamento;

class EstacionamentoEntradaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // A autenticação será verificada via middleware 'auth:sanctum'
    }

    public function rules(): array
    {
        return [
            'vaga_id' => [
                'required',
                'exists:vagas,id',
                function ($attribute, $value, $fail) {
                    $vaga = Vaga::find($value);
                    // Regra: Não permitir entrada se a vaga estiver ocupada ou interditada
                    if ($vaga && ($vaga->status === 'ocupada' || $vaga->status === 'interditada')) {
                        $fail('A vaga selecionada está ' . $vaga->status . '.');
                    }
                },
            ],
            'veiculo_id' => [
                'required',
                'exists:veiculos,id',
                function ($attribute, $value, $fail) {
                    // Regra: Não permitir entrada se o veículo já estiver estacionado em outra vaga
                    $estacionado = Estacionamento::where('veiculo_id', $value)
                                                 ->whereNull('saida_at') // Verifica se não tem saída registrada
                                                 ->exists();
                    if ($estacionado) {
                        $fail('O veículo já está estacionado em outra vaga.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'vaga_id.required' => 'O ID da vaga é obrigatório.',
            'vaga_id.exists' => 'A vaga selecionada não existe.',
            'veiculo_id.required' => 'O ID do veículo é obrigatório.',
            'veiculo_id.exists' => 'O veículo selecionado não existe.',
            // Mensagens personalizadas da validação 'function' serão geradas dinamicamente
        ];
    }
}