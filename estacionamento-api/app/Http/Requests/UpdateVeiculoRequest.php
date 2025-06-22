<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVeiculoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Determina se o usuário está autorizado a fazer esta requisição.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Permite a requisição (mesmo sem autenticação, por enquanto).
    }

    /**
     * Get the validation rules that apply to the request.
     * Obtém as regras de validação que se aplicam à requisição.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Obter o ID do veículo que está sendo atualizado a partir dos parâmetros da rota.
        $veiculoId = $this->route('veiculo');

        // Define as regras de validação para os campos que esperamos na requisição PUT/PATCH
        return [
            'placa' => [
                'sometimes', // O campo placa é opcional na atualização, só valida se estiver presente
                'string',
                'max:10',
                Rule::unique('veiculos', 'placa')->ignore($veiculoId), // Garante que a placa seja única na tabela veiculo
                // Verifica a placa 
                Rule::when($this->input('tipo') === 'carro', [
                    'regex:/^[A-Z]{3}[0-9]{1}[A-Z]{1}[0-9]{2}$/i', // Carro: AAA0A00
                ]),
                Rule::when($this->input('tipo') === 'moto', [
                    'regex:/^[A-Z]{3}[0-9]{2}[A-Z]{1}[0-9]{1}$/i', // Moto: AAA00A0
                ]),

            ],
            'cor' => [
                'sometimes', // O campo cor é opcional.
                'string',    // Deve ser do tipo string.
                'max:30'     // Deve ter no máximo 30 caracteres.
            ],
            'modelo' => [
                'sometimes', // O campo modelo é opcional.
                'string',    // Deve ser do tipo string.
                'max:50'     // Deve ter no máximo 50 caracteres.
            ],
            'tipo' => [
                'sometimes', // O campo tipo é opcional 
                'string',    // Deve ser do tipo string.
                // Restringe os valores permitidos para tipo a carro ou moto.
                Rule::in(['carro', 'moto'])
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     * Opcional: Define mensagens de erro personalizadas para as regras de validação.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'placa.unique' => 'Esta placa já está cadastrada para outro veículo.',
            'placa.max' => 'A placa não pode ter mais de :max caracteres.',
            'tipo.in' => 'O tipo de veículo deve ser "carro" ou "moto".',
        ];
    }
}