<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Classe de requisição para validação de usuários.
 *
 * Responsável por definir as regras de validação e mensagens de erro 
 * para operações relacionadas a usuários, como criação e edição.
 *
 * @package App\Http\Requests
 */
class UserRequest extends FormRequest
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
        $user = $this->route('user');

        if ($this->has('cpf')) {
            $this->merge([
                'cpf' => preg_replace('/[^0-9]/', '', $this->cpf),
            ]);
        }

        return [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . ($user ? $user->id : null),
            'cpf' => [
                'nullable',               // Só valida se tiver valor
                'digits:11',              // Garante 11 dígitos exatos
                'unique:users,cpf,' . ($user ? $user->id : 'null'),
            ],
            'alias' => 'max:255',
            'password' => 'required_if:password,!=null|confirmed|min:6',
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

            'cpf.max' => "O campo CPF não pode ser superior a :max caracteres.",
            'cpf.unique' => "O CPF já está cadastrado!",

            'alias.max' => "O campo alias não pode ser superior a :max caracteres.",

            'password.required' => "Campo senha é obrigatório!",
            'password.confirmed' => 'A confirmação da senha não corresponde!',
            'password.min' => "Senha com no mínimo :min caracteres!",
        ];
    }
}
