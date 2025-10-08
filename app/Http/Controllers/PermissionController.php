<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    // Listar as permissões ou páginas
    public function index(Request $request)
    {
        // Recuperar os registros do banco dados
        //$permissions = Permission::orderBy('title', 'ASC')->paginate(10);         
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
            ->orderBy('name', 'DESC')
            ->paginate(10)
            ->withQueryString();

        // Salvar log
        Log::info('Listar as permissões.', ['action_user_id' => Auth::id()]);

        // Carregar a view 
        return view('permissions.index', [
            'menu' => 'permissions',
            'title' => $request->title,
            'name' => $request->name,
            'permissions' => $permissions
        ]);
    }

    // Visualizar os detalhes da permissão ou página
    public function show(Permission $permission)
    {
        // Salvar log
        Log::info('Visualizar a permissão.', ['permission_id' => $permission->id, 'action_user_id' => Auth::id()]);

        // Carregar a view 
        return view('permissions.show', ['menu' => 'permissions', 'permission' => $permission]);
    }

    // Carregar o formulário cadastrar nova permissão ou página
    public function create()
    {
        // Carregar a view 
        return view('permissions.create', ['menu' => 'permissions']);
    }

    // Cadastrar no banco de dados o nova permissão ou página
    public function store(PermissionRequest $request)
    {
        // Capturar possíveis exceções durante a execução.
        try {
            // Cadastrar no banco de dados na tabela permissão
            $permission = Permission::create([
                'title' => $request->title,
                'name' => $request->name,
            ]);

            // Salvar log
            Log::info('Permissão cadastrada.', ['permission_id' => $permission->id, 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de sucesso
            return redirect()->route('permissions.show', ['permission' => $permission->id])->with('success', 'Permissão cadastrada com sucesso!');
        } catch (Exception $e) {

            // Salvar log
            Log::notice('Permissão não cadastrada.', ['error' => $e->getMessage(), 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Permissão não cadastrada!');
        }
    }

    // Carregar o formulário editar permissão ou página
    public function edit(Permission $permission)
    {
        // Carregar a view 
        return view('permissions.edit', ['menu' => 'permissions', 'permission' => $permission]);
    }

    // Editar no banco de dados a permissão ou página
    public function update(PermissionRequest $request, Permission $permission)
    {
        // Capturar possíveis exceções durante a execução.
        try {
            // Editar as informações do registro no banco de dados
            $permission->update([
                'title' => $request->title,
                'name' => $request->name,
            ]);

            // Salvar log
            Log::info('Permissão editada.', ['permission_id' => $permission->id, 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de sucesso
            return redirect()->route('permissions.show', ['permission' => $permission->id])->with('success', 'Permissão editada com sucesso!');
        } catch (Exception $e) {

            // Salvar log
            Log::notice('Permissão não editada.', ['error' => $e->getMessage()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Permissão não editada!');
        }
    }

    // Excluir a permissão ou página do banco de dados
    public function destroy(Permission $permission)
    {
        // Capturar possíveis exceções durante a execução.
        try {

            // Excluir o registro do banco de dados
            $permission->delete();

            // Salvar log
            Log::info('Permissão apagada.', ['permission_id' => $permission->id, 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de sucesso
            return redirect()->route('permissions.index')->with('success', 'Permissão apagada com sucesso!');
        } catch (Exception $e) {

            // Salvar log
            Log::notice('Permissão não apagada.', ['error' => $e->getMessage()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Permissão não apagada!');
        }
    }
}
