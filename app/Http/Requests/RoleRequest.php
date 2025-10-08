<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Classe de requisição para validação de papéis.
 *
 * Responsável por definir as regras de validação e mensagens de erro 
 * para operações relacionadas a papéis, como criação e edição.
 *
 * @package App\Http\Requests
 */
class RoleRequest extends FormRequest
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
        $role = $this->route('role');

        return [
            'name' => 'required|unique:roles,name,' . ($role ? $role->id : null),
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
            'name.unique' => "O papel já está cadastrado!",
        ];
    }
}
