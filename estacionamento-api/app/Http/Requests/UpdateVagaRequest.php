<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVagaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Pega o ID da vaga da rota
        $vagaId = $this->route('vaga');

        return [
            'codigo' => ['sometimes', 'string', 'max:255', Rule::unique('vagas', 'codigo')->ignore($vagaId)],
            'rua' => ['sometimes', 'string', 'max:255'], 
            'numero' => ['nullable', 'string', 'max:255'],
            'bairro' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'string', Rule::in(['livre', 'ocupada', 'interditada'])],
        ];
    }
}