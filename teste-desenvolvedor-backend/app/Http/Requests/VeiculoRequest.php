<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VeiculoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $veiculoId = $this->route('veiculo') ? $this->route('veiculo')->id : null;

        return [
            'placa' => [
                'required',
                'string',
                'max:8', // Placa Mercosul tem 7 caracteres + traço, por exemplo ABC1D23
                Rule::unique('veiculos')->ignore($veiculoId),
                'regex:/^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$/i', // Regex para Placa Mercosul (ex: ABC1D23, ABC4E67)
            ],
            'modelo' => 'required|string|max:255',
            'cor' => 'required|string|max:255',
            'tipo' => ['required', 'string', Rule::in(['carro', 'moto'])],
        ];
    }

    public function messages(): array
    {
        return [
            'placa.unique' => 'Esta placa já está cadastrada.',
            'placa.regex' => 'Formato de placa inválido. Use o padrão Mercosul (ex: ABC1D23).',
        ];
    }
}