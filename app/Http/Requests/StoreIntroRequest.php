<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreIntroRequest extends FormRequest
{
    /**
     * Garante que apenas usuários logados usem essa request
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Regras de validação
     */
    public function rules(): array
    {
        return [
            'estado' => ['required', 'string', 'size:2'],
            'ano_letivo_inicio' => ['required', 'date'],
            'ano_letivo_fim' => ['required', 'date', 'after:ano_letivo_inicio'],
        ];
    }

    /**
     * Mensagens de erro personalizadas
     */
    public function messages(): array
    {
        return [
            'estado.required' => 'Selecione o estado.',
            'estado.size' => 'O estado deve ter 2 letras.',
            'ano_letivo_inicio.required' => 'A data de início é obrigatória.',
            'ano_letivo_fim.required' => 'A data de término é obrigatória.',
            'ano_letivo_fim.after' => 'A data de término deve ser depois do início.',
        ];
    }

    /**
     * Normalização dos dados ANTES da validação
     * Aqui aplicamos a sugestão de limpar os inputs
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'estado' => Str::upper($this->estado), // Transforma 'sp' em 'SP'
        ]);
    }
}