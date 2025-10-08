<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStatusRequest;
use App\Models\UserStatus;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserStatusController extends Controller
{
    // Listar os status
    public function index()
    {
        // Recuperar os registros do banco dados
        $userStatuses = UserStatus::orderBy('id', 'DESC')->paginate(10);

        // Salvar log
        Log::info('Listar os status para usuário.', ['action_user_id' => Auth::id()]);

        // Carregar a view 
        return view('user_statuses.index', ['menu' => 'user_statuses', 'userStatuses' => $userStatuses]);
    }

    // Visualizar os detalhes do status para usuário
    public function show(UserStatus $userStatus)
    {
        // Salvar log
        Log::info('Visualizar o status para usuário.', ['user_status_id' => $userStatus->id, 'action_user_id' => Auth::id()]);

        // Carregar a view 
        return view('user_statuses.show', ['menu' => 'user_statuses', 'userStatus' => $userStatus]);
    }

    // Carregar o formulário cadastrar novo status
    public function create()
    {
        // Carregar a view 
        return view('user_statuses.create', ['menu' => 'user_statuses']);
    }

    // Cadastrar no banco de dados o novo status
    public function store(UserStatusRequest $request)
    {
        // Capturar possíveis exceções durante a execução.
        try {
            // Cadastrar no banco de dados na tabela status
            $userStatus = UserStatus::create([
                'name' => $request->name
            ]);

            // Salvar log
            Log::info('Status para usuário cadastrado.', ['user_status_id' => $userStatus->id, 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de sucesso
            return redirect()->route('user_statuses.show', ['userStatus' => $userStatus->id])->with('success', 'Status para usuário cadastrado com sucesso!');
        } catch (Exception $e) {

            // Salvar log
            Log::notice('Status para usuário não cadastrado.', ['error' => $e->getMessage(), 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Status para usuário não cadastrado!');
        }
    }

    // Carregar o formulário editar status para usuário
    public function edit(UserStatus $userStatus)
    {
        // Carregar a view 
        return view('user_statuses.edit', ['menu' => 'user_statuses', 'userStatus' => $userStatus]);
    }

    // Editar no banco de dados o status para usuário
    public function update(UserStatusRequest $request, UserStatus $userStatus)
    {
        // Capturar possíveis exceções durante a execução.
        try {
            // Editar as informações do registro no banco de dados
            $userStatus->update([
                'name' => $request->name
            ]);

            // Salvar log
            Log::info('Status para usuário editado.', ['user_status_id' => $userStatus->id, 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de sucesso
            return redirect()->route('user_statuses.show', ['userStatus' => $userStatus->id])->with('success', 'Status para usuário editado com sucesso!');
        } catch (Exception $e) {

            // Salvar log
            Log::notice('Status para usuário não editado.', ['error' => $e->getMessage(), 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Status para usuário não editado!');
        }
    }

    // Excluir o curso do banco de dados
    public function destroy(UserStatus $userStatus)
    {
        // Capturar possíveis exceções durante a execução.
        try {

            // Excluir o registro do banco de dados
            $userStatus->delete();

            // Salvar log
            Log::info('Status para usuário apagado.', ['user_status_id' => $userStatus->id, 'action_user_id' => Auth::id()]);
            
            // Redirecionar o usuário, enviar a mensagem de sucesso
            return redirect()->route('user_statuses.index')->with('success', 'Status para usuário apagado com sucesso!');
        } catch (Exception $e) {

            // Salvar log
            Log::notice('Status para usuário não apagado.', ['error' => $e->getMessage(), 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Status para usuário não apagado!');
        }
    }
}
