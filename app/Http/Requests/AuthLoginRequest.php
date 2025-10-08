<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthLoginRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     *
     * @return bool Retorna true para permitir a requisição.
     */
    public function authorize(): bool
    {
        return true;
    }

     /**
     * Retorna as regras de validação aplicáveis à requisição.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> 
     * Regras de validação.
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
        ];
    }

    /**
     * Define mensagens personalizadas para as regras de validação.
     *
     * @return array<string, string> Mensagens de erro personalizadas.
     */
    public function messages(): array
    {
        return [
            'email.required' => "Campo e-mail é obrigatório!",
            'email.email' => "Necessário enviar e-mail válido!",
            'password.required' => "Campo senha é obrigatório!",
        ];
    }
}
