<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Importa a classe Rule para regras de validação avançadas

class StoreEstacionamentoRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     * Por enquanto, retorna true para permitir todas as requisições.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtém as regras de validação que se aplicam à requisição POST (criação).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // vaga_id é obrigatório e deve existir na tabela vagas como coluna id.
            // unique:estacionamentos,vaga_id impede que a mesma vaga seja usada por mais de um estacionamento
            // que ainda não tenha uma data de saída.
            'vaga_id' => [
                'required',
                'integer',
                'exists:vagas,id',
                Rule::unique('estacionamentos', 'vaga_id')->where(function ($query) {
                    return $query->whereNull('saida'); // A vaga só pode ser usada se não houver saída registrada
                }),
            ],
            // veiculo_id é obrigatório e deve existir na tabela veiculos como coluna id.
            // unique:estacionamentos,veiculo_id impede que o mesmo veículo esteja em mais de um estacionamento
            // que ainda não tenha uma data de saída.
            'veiculo_id' => [
                'required',
                'integer',
                'exists:veiculos,id',
                Rule::unique('estacionamentos', 'veiculo_id')->where(function ($query) {
                    return $query->whereNull('saida'); // O veículo só pode estar em um estacionamento sem saída registrada
                }),
            ],
            // entrada é obrigatório e deve ser uma data e hora válidas.
            'entrada' => ['required', 'date_format:Y-m-d H:i:s'],

            // saida, tempo_total e valor são nulos na criação, pois serão definidos na saída.
            // A regra nullable permite que esses campos sejam omitidos ou enviados como nulos.
            'saida' => ['nullable', 'date_format:Y-m-d H:i:s', 'after_or_equal:entrada'],
            'tempo_total' => ['nullable', 'integer', 'min:0'],
            'valor' => ['nullable', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'], // Permite até 2 casas decimais
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
            'vaga_id.required' => 'O ID da vaga é obrigatório.',
            'vaga_id.exists' => 'O ID da vaga fornecido não existe.',
            'vaga_id.unique' => 'Esta vaga já está ocupada por outro veículo.',
            'veiculo_id.required' => 'O ID do veículo é obrigatório.',
            'veiculo_id.exists' => 'O ID do veículo fornecido não existe.',
            'veiculo_id.unique' => 'Este veículo já está registrado em outro estacionamento.',
            'entrada.required' => 'A data e hora de entrada são obrigatórias.',
            'entrada.date_format' => 'O formato da data de entrada deve ser AAAA-MM-DD HH:MM:SS.',
            'saida.after_or_equal' => 'A data de saída deve ser igual ou posterior à data de entrada.',
            'valor.numeric' => 'O valor deve ser um número.',
            'valor.regex' => 'O valor deve ter no máximo 2 casas decimais.',
        ];
    }
}