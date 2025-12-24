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