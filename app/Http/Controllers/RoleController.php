<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    // Listar os papéis
    public function index()
    {
        // Recuperar os registros do banco dados
        $roles = Role::orderBy('order', 'ASC')->paginate(10);

        // Salvar log
        Log::info('Listar os papéis.', ['action_user_id' => Auth::id()]);

        // Carregar a view 
        return view('roles.index', ['menu' => 'roles', 'roles' => $roles]);
    }

    // Visualizar os detalhes do papel
    public function show(Role $role)
    {
        // Salvar log
        Log::info('Visualizar o papel.', ['role_id' => $role->id, 'action_user_id' => Auth::id()]);

        // Carregar a view 
        return view('roles.show', ['menu' => 'roles', 'role' => $role]);
    }

    // Carregar o formulário cadastrar novo papel
    public function create()
    {
        // Carregar a view 
        return view('roles.create', ['menu' => 'roles']);
    }

    // Cadastrar no banco de dados o novo papel
    public function store(RoleRequest $request)
    {
        // Capturar possíveis exceções durante a execução.
        try {
            // Recuperar o último order cadastrado
            $lastRole = Role::orderBy('order', 'DESC')->first();

            // Cadastrar no banco de dados na tabela role
            $role = Role::create([
                'name' => $request->name,
                'order' => $lastRole ? $lastRole->order + 1 : 1, // Incrementar o order
            ]);

            // Permissões que serão atribuídas ao novo papel
            $permissions = [
                'dashboard',
                'show-profile',
                'edit-profile',
                'edit-password-profile',
            ];

            // Atribuir as permissões ao papel
            $role->givePermissionTo($permissions);

            // Salvar log
            Log::info('Papel cadastrado.', ['role_id' => $role->id, 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de sucesso
            return redirect()->route('roles.show', ['role' => $role->id])->with('success', 'Papel cadastrado com sucesso!');
        } catch (Exception $e) {

            // Salvar log
            Log::notice('Papel não cadastrado.', ['error' => $e->getMessage(), 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Papel não cadastrado!');
        }
    }

    // Carregar o formulário editar papel
    public function edit(Role $role)
    {
        // Carregar a view 
        return view('roles.edit', ['menu' => 'roles', 'role' => $role]);
    }

    // Editar no banco de dados o papel
    public function update(RoleRequest $request, Role $role)
    {
        // Capturar possíveis exceções durante a execução.
        try {
            // Editar as informações do registro no banco de dados
            $role->update([
                'name' => $request->name
            ]);

            // Salvar log
            Log::info('Papel editado.', ['role_id' => $role->id, 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de sucesso
            return redirect()->route('roles.show', ['role' => $role->id])->with('success', 'Papel editado com sucesso!');
        } catch (Exception $e) {

            // Salvar log
            Log::notice('Papel não editado.', ['error' => $e->getMessage()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Papel não editado!');
        }
    }
    // Alterar a ordem do papel
    public function updateOrder(Role $role)
    {
        // Bloquear a alteração da ordem do papel com order 1 (super-admin)
        if ($role->order == 1) {
            return back()->withInput()->with('error', 'A ordem do papel super-admin não pode ser alterada!');
        }

        // Capturar possíveis exceções durante a execução.
        try {
            // Recuperar os registros do banco dados com a ordem menor que o papel atual
            $minRoleOrder = Role::where('order', '<', $role->order)->orderBy('order', 'DESC')->first();

            // Verificar se existe um papel com a ordem menor
            if (!$minRoleOrder) {
                // Redirecionar o usuário, enviar a mensagem de erro
                return back()->withInput()->with('error', 'Não existe um papel com a ordem menor!');
            }

            // Bloquear a alteração da ordem do papel com order 1 (super-admin)
            if ($minRoleOrder->order == 1) {
                return back()->withInput()->with('error', 'A ordem do papel super-admin não pode ser alterada!');
            }

            // Trocar a ordem do papel atual com o papel que possui a ordem menor
            $currentOrder = $role->order;

            $role->update([
                'order' => $minRoleOrder->order,
            ]);

            $minRoleOrder->update([
                'order' => $currentOrder,
            ]);

            // Salvar log
            Log::info('Alterar a ordem dos papéis.', ['order_id' => $role->id, 'order' => $role->order, 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de sucesso
            return redirect()->route('roles.index')->with('success', 'Alterado a ordem do papel com sucesso!');
        } catch (Exception $e) {

            // Salvar log
            Log::notice('Ordem do papel não editado.', ['error' => $e->getMessage()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Ordem do papel não editado!');
        }
    }

    // Excluir o papel do banco de dados
    public function destroy(Role $role)
    {
        // Capturar possíveis exceções durante a execução.
        try {

            // Excluir o registro do banco de dados
            $role->delete();

            // Salvar log
            Log::info('Papel apagado.', ['role_id' => $role->id, 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de sucesso
            return redirect()->route('roles.index')->with('success', 'Papel apagado com sucesso!');
        } catch (Exception $e) {

            // Salvar log
            Log::notice('Papel não apagado.', ['error' => $e->getMessage()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Papel não apagado!');
        }
    }
}
