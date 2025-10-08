<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    // Listar as permissões do papel
    public function index(Request $request,Role $role)
    {

        // Verificar se o papel é super admin, não permitir visualizar as permissões
        if ($role->name == 'Super Admin') {

            // Salvar log
            Log::info('A permissão do super admin não pode ser acessada.', ['role_id' => $role->id, 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de sucesso
            return redirect()->route('roles.index')->with('error', 'A permissão do super admin não pode ser acessada!');
        }

        // Recuperar as permissões do papel
        $rolePermissions = DB::table('role_has_permissions')
            ->where('role_id', $role->id)
            ->pluck('permission_id')
            ->all();


        // Recuperar as permissões
        // $permissions = Permission::orderBy('name')->get();
        $permissions = Permission::when(
            $request->filled('title'),
            fn($query) =>
            $query->whereLike('title', '%' . $request->title .  '%')
        )
            ->when(
                $request->filled('name'),
                fn($query) =>
                $query->whereLike('name', '%' . $request->name .  '%')
            )
            ->orderBy('title', 'ASC')
            ->get();

        // Salvar log
        Log::info('Listar permissões do papel.', ['role_id' => $role->id, 'action_user_id' => Auth::id()]);

        // Carregar a view 
        return view('role_permissions.index', [
            'menu' => 'roles', 
            'title' => $request->title,
            'name' => $request->name,
            'rolePermissions' => $rolePermissions,
            'permissions' => $permissions,
            'role' => $role,
        ]);
    }

    // Editar a permissão de acesso a página para o papel
    public function update(Role $role, Permission $permission)
    {
        // Capturar possíveis exceções durante a execução.
        try {

            // Definir ação (bloquear ou liberar)
            $action = $role->permissions->contains($permission) ? 'bloquear' : 'liberar';

            // Liberar ou bloquear a permissão
            $role->{$action === 'bloquear' ? 'revokePermissionTo' : 'givePermissionTo'}($permission);
            
            // Salvar log
            Log::info(ucfirst($action). ' permissão para o papel.', [
                'role_id' => $role->id,
                'permission_id' => $permission->id, 
                'action_user_id' => Auth::id()
            ]);

            // Redirecionar o usuário, enviar a mensagem de sucesso
            return redirect()->route('role-permissions.index', ['role' => $role->id])->with('success', 'Permissão ' . ($action === 'bloquear' ? 'bloqueada' : 'liberada') . ' com sucesso!');
        } catch (Exception $e) {

            // Salvar log
            Log::notice('Permissão para o papel não editada.', ['error' => $e->getMessage(), 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Permissão para o papel não editada!');
        }
    }
}
