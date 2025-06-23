<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VagaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // A autenticação real será verificada via middleware 'auth:sanctum'
    }

    public function rules(): array
    {
        $vagaId = $this->route('vaga') ? $this->route('vaga')->id : null;

        return [
            'codigo' => [
                'required',
                'string',
                'max:255',
                Rule::unique('vagas')->ignore($vagaId), // O código da vaga deve ser único
            ],
            'localizacao' => 'required|string|max:255', // O frontend envia a localização como uma string
            'status' => ['required', 'string', Rule::in(['livre', 'ocupada', 'interditada'])],
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.unique' => 'O código da vaga já está em uso. Escolha um diferente.',
            'localizacao.required' => 'O campo localização é obrigatório.',
            'status.in' => 'O status da vaga deve ser "livre", "ocupada" ou "interditada".',
        ];
    }
}
