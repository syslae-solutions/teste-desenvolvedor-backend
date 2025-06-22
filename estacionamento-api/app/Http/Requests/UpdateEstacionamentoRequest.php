<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Importa a classe Rule
use Carbon\Carbon; // Importa Carbon para manipulação de datas

class UpdateEstacionamentoRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtém as regras de validação que se aplicam à requisição PUT/PATCH (atualização).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Pega o ID do registro de estacionamento da rota
        $estacionamentoId = $this->route('estacionamento');

        return [
            // A regra unique aqui é mais complexa: a vaga_id deve ser única APENAS para registros
            // de estacionamento que não tenham uma saida definida, e ignora o registro atual
            // que está sendo atualizado. Isso permite mudar a vaga para um estacionamento ativo.
            'vaga_id' => [
                'sometimes', // Só valida se presente na requisição.
                'integer',
                'exists:vagas,id',
                Rule::unique('estacionamentos', 'vaga_id')->where(function ($query) {
                    return $query->whereNull('saida');
                })->ignore($estacionamentoId),
            ],

            'veiculo_id' => [
                'sometimes',
                'integer',
                'exists:veiculos,id',
                Rule::unique('estacionamentos', 'veiculo_id')->where(function ($query) {
                    return $query->whereNull('saida');
                })->ignore($estacionamentoId),
            ],
            
            'entrada' => ['sometimes', 'date_format:Y-m-d H:i:s'],

            // saida deve ser uma data/hora válida e deve ser IGUAL OU POSTERIOR à data de entrada.
            // 'after_or_equal:entrada' compara com o campo 'entrada' da própria requisição.
            'saida' => ['sometimes', 'nullable', 'date_format:Y-m-d H:i:s', 'after_or_equal:entrada'],

            // tempo_total e valor são calculados no controlador quando saida é definida,
            'tempo_total' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'valor' => ['sometimes', 'nullable', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
        ];
    }

    /**
     * Define mensagens de erro personalizadas para as regras de validação.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'vaga_id.exists' => 'O ID da vaga fornecido não existe.',
            'vaga_id.unique' => 'Esta vaga já está ocupada por outro veículo ativo.',
            'veiculo_id.exists' => 'O ID do veículo fornecido não existe.',
            'veiculo_id.unique' => 'Este veículo já está em outro estacionamento ativo.',
            'saida.date_format' => 'O formato da data de saída deve ser AAAA-MM-DD HH:MM:SS.',
            'saida.after_or_equal' => 'A data de saída deve ser igual ou posterior à data de entrada.',
            'valor.numeric' => 'O valor deve ser um número.',
            'valor.regex' => 'O valor deve ter no máximo 2 casas decimais.',
        ];
    }
}