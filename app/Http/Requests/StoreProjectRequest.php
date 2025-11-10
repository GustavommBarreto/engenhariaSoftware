<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'nome'      => ['required', 'string', 'max:150'],
            'descricao' => ['nullable', 'string', 'max:5000'],
            'status'    => ['required', 'in:Ativo,Pausado,Concluído'],
            'inicio'    => ['required', 'date'],
            'fim'       => ['nullable', 'date', 'after_or_equal:inicio'],
        ];
    }

    public function messages(): array
    {
        return [
            'fim.after_or_equal' => 'A data de fim deve ser maior ou igual à data de início.',
        ];
    }
}
