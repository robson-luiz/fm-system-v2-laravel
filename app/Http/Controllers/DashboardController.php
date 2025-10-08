<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    // Página inicial do administrativo
    public function index()
    {

        // Capturar possíveis exceções durante a execução.
        try {            

            // Salvar log
            Log::notice('Dashboard.', ['action_user_id' => Auth::id()]);

            // Carregar a VIEW
            return view('dashboard.index', ['menu' => 'dashboard-adm']);
        } catch (Exception $e) {

            // Salvar log
            Log::notice('Dados para o dashboard não gerado.', ['error' => $e->getMessage(), 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Dados para o dashboard não gerado!');
        }
    }
}
