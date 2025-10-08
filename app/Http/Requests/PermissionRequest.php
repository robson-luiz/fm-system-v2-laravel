<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Classe de requisição para validação de cursos.
 *
 * Responsável por definir as regras de validação e mensagens de erro 
 * para operações relacionadas a cursos, como criação e edição.
 *
 * @package App\Http\Requests
 */
class PermissionRequest extends FormRequest
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
        $permission = $this->route('permission');

        return [
            'title' => 'required|unique:permissions,title,' . ($permission ? $permission->id : null),
            'name' => 'required|unique:permissions,name,' . ($permission ? $permission->id : null),
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
            'title.required' => "Campo título é obrigatório!",
            'title.unique' => "O título já está cadastrado!",
            'name.required' => "Campo nome é obrigatório!",
            'name.unique' => "O nome já está cadastrado!",
        ];
    }
}
