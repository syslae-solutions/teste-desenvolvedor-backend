<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVagaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'max:255', 'unique:vagas,codigo'],
            'rua' => ['required', 'string', 'max:255'], 
            'bairro' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(['livre', 'ocupada', 'interditada'])],
            'numero' => ['nullable', 'string', 'max:255'],
        ];
    }
}
