<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRegisterUserRequest extends FormRequest
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
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'max:50',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#%+:$@&])[A-Za-z\d#%+:$@&]{8,50}$/'
            ],
            'accept_terms' => 'accepted',
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
            'name.required' => "Campo nome é obrigatório!",
            'email.required' => "Campo e-mail é obrigatório!",
            'email.email' => "Necessário enviar e-mail válido!",
            'email.unique' => "O e-mail já está cadastrado!",
            'password.required' => "Campo senha é obrigatório!",
            'password.confirmed' => 'A confirmação da senha não corresponde!',
            'password.min' => "Senha com no mínimo :min caracteres!",
            'password.max' => "Senha com no máximo :max caracteres!",
            'password.regex' => 'A senha deve conter entre 8 e 50 caracteres, com pelo menos uma letra maiúscula, uma minúscula, um número e um dos símbolos permitidos: # % + : $ @ &',
            'accept_terms.accepted' => 'Você deve aceitar os Termos de Uso e Política de Privacidade.',
        ];
    }
}
