<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Importa a classe Rule para usar Rule::unique

class StoreVeiculoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Determina se o usuário está autorizado a fazer esta requisição.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Por enquanto, retorna 'true' para permitir que qualquer requisição (mesmo sem autenticação)
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * Obtém as regras de validação que se aplicam à requisição.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Define as regras de validação para os campos que esperamos na requisição POST
        return [
            'placa' => [
                'required', // O campo placa é obrigatório.
                'string',   // Deve ser do tipo string.
                'max:10',   // Deve ter no máximo 10 caracteres.
                'unique:veiculos,placa', // garante que o valor da placa seja único na coluna placa
                // Verificação da placa
                Rule::when($this->input('tipo') === 'carro', [
                    'regex:/^[A-Z]{3}[0-9]{1}[A-Z]{1}[0-9]{2}$/i', // Carro: AAA0A00
                ]),
                Rule::when($this->input('tipo') === 'moto', [
                    'regex:/^[A-Z]{3}[0-9]{2}[A-Z]{1}[0-9]{1}$/i', // Moto: AAA00A0
                ]),
            ],
            'cor' => [
                'required', // O campo cor é obrigatório.
                'string',   // Deve ser do tipo string.
                'max:30'    // Deve ter no máximo 30 caracteres.
            ],
            'modelo' => [
                'required', // O campo modelo é obrigatório.
                'string',   // Deve ser do tipo string.
                'max:50'    // Deve ter no máximo 50 caracteres.
            ],
            'tipo' => [
                'required', // O campo tipo é obrigatório.
                'string',   // Deve ser do tipo string.
                Rule::in(['carro', 'moto']) // restringe os valores permitidos para tipo
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     * Opcional: Define mensagens de erro personalizadas para as regras de validação.
     * Você pode adicionar este método se quiser mensagens mais amigáveis.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'placa.required' => 'A placa do veículo é obrigatória.',
            'placa.unique' => 'Esta placa já está cadastrada.',
            'placa.max' => 'A placa não pode ter mais de :max caracteres.',
            'cor.required' => 'A cor do veículo é obrigatória.',
            'modelo.required' => 'O modelo do veículo é obrigatório.',
            'tipo.required' => 'O tipo do veículo (carro ou moto) é obrigatório.',
            'tipo.in' => 'O tipo de veículo deve ser "carro" ou "moto".',
        ];
    }
}