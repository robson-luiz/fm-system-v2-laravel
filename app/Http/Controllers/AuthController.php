<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterUserRequest;
use App\Models\Alert;
use App\Models\SentEmail;
use App\Models\User;
use App\Models\UserTermAcceptance;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    // Login
    public function index()
    {
        // Carregar a VIEW
        return view('auth.login');
    }

    // Validar os dados do usuário no login
    public function loginProcess(AuthLoginRequest $request)
    {
        // Capturar possíveis exceções durante a execução.
        try {

            /************** Inicio só validade o usuário e senha e redirecionar o usuário ****************/
            // Validar o usuário e a senha com as informações do banco de dados
            $authenticated = Auth::attempt(['email' => $request->email, 'password' => $request->password]);

            // Verificar se o usuário foi autenticado
            if (!$authenticated) {
                // Salvar log
                Log::notice('E-mail ou senha inválido!', ['email' => $request->email]);

                // Redirecionar o usuário, enviar a mensagem de erro
                return back()->withInput()->with('error', 'E-mail ou senha inválido!');
            }

            // Salvar log
            Log::info('Login', ['action_user_id' => Auth::id()]);

            // Redirecionar o usuário
            return redirect()->route('dashboard.index');

            /************** Fim só validade o usuário e senha e redirecionar o usuário ****************/

            /************** Inicio enviar e-mail para atualizar a senha se a mesma já faz mais de um ano que foi atualizada ****************/
            // Recuperar o usuário com o e-mail informado
            // $user = User::where('email', $request->email)->first();
            // $status = false;

            // // Verificar se o usuário foi encontrado
            // if (!$user) {
            //     // Salvar log
            //     Log::notice('E-mail não encontrado durante o login.', ['email' => $request->email]);

            //     // Redirecionar o usuário, enviar a mensagem de erro
            //     return back()->withInput()->with('error', 'E-mail ou senha inválido!');
            // }

            // // Recuper o papel aluno
            // $alunoRole = Role::where('name', 'Aluno')->first();

            // // Se o usuário não tem nenhum papel e existe o papel aluno, atribui o papel aluno para o usuário
            // if ($user->roles->isEmpty() && $alunoRole) {
            //     $user->assignRole($alunoRole);
            // }

            // Verifica se a senha nunca foi atualizada ou se foi há mais de 1 ano
            // if (
            //     is_null($user->password_updated_at) ||
            //     Carbon::parse($user->password_updated_at)->lt(now()->subYear())
            // ) {

            //     /*********** Início sem limite de tentativas ************/
            //     // Salvar o token recuperar senha e enviar e-mail
            //     // $status = Password::sendResetLink(
            //     //     // Retorna um array associativo contendo apenas o campo "email" da requisição.
            //     //     $request->only('email')
            //     // );
            //     // Salvar log
            //     // Log::info('Senha desatualizada. Enviar e-mail para atualizar a senha.', ['status' => $status, 'email' => $request->email]);
            //     /*********** Fim sem limite de tentativas ************/

            //     /*********** Início limitar 3 tentativas a cada 20 minutos ************/

            //     $email = $request->email;
            //     $cacheKey = 'reset_attempts:' . md5($email);
            //     $attempts = Cache::get($cacheKey, 0);

            //     if ($attempts >= 5) {
            //         Log::warning('Tentativas de redefinição de senha excedidas.', ['email' => $email]);

            //         return back()->with('error', 'Você excedeu o limite de 5 tentativas. Tente novamente em 10 minutos.');
            //     }

            //     // Verifica se já enviou 10 e-mails para este usuário no dia
            //     $sentEmailsCount = SentEmail::where('user_id', $user->id)->count();
            //     $limitEmails = 8;

            //     // Limitar o envio diário a 10 e-mails por usuário
            //     if ($sentEmailsCount < $limitEmails) {

            //         // Salvar o token recuperar senha e enviar e-mail
            //         $status = Password::sendResetLink(
            //             $request->only('email')
            //         );

            //         // Salvar log de envio na tabela sent_emails
            //         SentEmail::create([
            //             'subject'         => 'Dados para recuperar a senha',
            //             'body'            => 'Enviado dados para recuperar a senha.',
            //             'recipient_email' => $user->email,
            //             'sent_at'         => now(),
            //             'user_id'         => $user->id,
            //         ]);
            //     } else {
            //         Log::warning("Não foi enviado o e-mail recuperar senha porque excedeu a cota de $limitEmails e-mails diários.", ['user_id' => $user->id]);

            //         // Criar um alerta para o administrador, excedeu o limite de e-mails enviados para este usuário
            //         Alert::create([
            //             'description' => "Não foi enviado o e-mail recuperar senha porque excedeu a cota de $limitEmails e-mails diários. Id do usuário: " . $user->id . ', E-mail: ' . $user->email,
            //             'user_id'     => $user->id,
            //         ]);

            //         // Notificar o usuário do limite diário
            //         return back()->with('error', 'É necessário entrar em contato com ' . env('MAIL_FROM_ADDRESS') . ' para mais detalhes sobre o acesso.');
            //         // return back()->with('atualizarSenha', 'É necessário entrar em contato com ' . env('MAIL_FROM_ADDRESS') . ' para mais detalhes.');
            //     }

            //     // Incrementar a tentativa
            //     Cache::put($cacheKey, $attempts + 1, now()->addMinutes(10));

            //     // Log do envio
            //     Log::info('Senha desatualizada. Enviar e-mail para atualizar a senha.', ['status' => $status, 'email' => $email]);

            //     /*********** Fim limitar 3 tentativas a cada 20 minutos ************/

            //     // Notificar o usuário verificiar o e-mail
            //     return back()->with('atualizarSenha', 'É necessário atualizar sua senha. Verifique sua caixa de entrada e a pasta de spam para as instruções.');
            // }


            // // Validar o usuário e a senha com as informações do banco de dados
            // $authenticated = Auth::attempt(['email' => $request->email, 'password' => $request->password]);

            // // Verificar se o usuário foi autenticado
            // if (!$authenticated) {

            //     // Salvar log
            //     Log::notice('E-mail ou senha inválido!', ['email' => $request->email]);

            //     // Redirecionar o usuário, enviar a mensagem de erro
            //     return back()->withInput()->with('error', 'E-mail ou senha inválido!');
            // }

            // // Salvar log
            // Log::info('Login', ['action_user_id' => Auth::id()]);

            // // Verificar se o usuário atualizou o nome através da coluna name_updated_at
            // if (!$user->name_updated_at) {

            //     /************* Remover a parte do nome com caracteres especiais *************/
            //     // Separar o nome em partes
            //     $arrayName = explode(' ', $user->name);

            //     $validPartsName = [];

            //     foreach ($arrayName as $parte) {
            //         // Remove espaços extras
            //         $parte = trim($parte);

            //         // Verifica se a parte contém SOMENTE letras sem acento (A-Z e a-z)
            //         if (preg_match('/^[a-zA-Z]+$/', $parte)) {
            //             $validPartsName[] = $parte;
            //         }
            //     }

            //     // Se encontrou partes válidas do nome, unifica elas
            //     if (!empty($validPartsName)) {
            //         $validName = implode(' ', $validPartsName);
            //     } else {
            //         // Se não encontrou partes válidas, usa o e-mail os 5 primeiros caracteres e o ID do usuário
            //         $validName = substr($user->email, 0, 5) . $user->id;
            //     }

            //     // Editar as informações do registro no banco de dados
            //     $user->update([
            //         'name' => $validName,
            //     ]);

            //     /************* Fim da remoção da parte do nome com caracteres especiais *************/

            //     // Salvar log
            //     Log::info('Usuário precisa atualizar o nome na v8.', ['email' => $request->email]);

            //     // Redirecionar o usuário para editar o nome
            //     return redirect()->route('profile.edit_name');
            // } else {

            //     // Redirecionar o usuário para o dashboard
            //     return redirect()->route('dashboard.index');
            // }

            /************** Fim enviar e-mail para atualizar a senha se a mesma já faz mais de um ano que foi atualizada ****************/
        } catch (Exception $e) {

            // Salvar log
            Log::notice('Dados do login incorreto.', ['error' => $e->getMessage()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'E-mail ou senha inválido1!');
        }
    }

    // Deslogar o usuário
    public function logout()
    {
        // Salvar log
        Log::notice('Logout.', ['action_user_id' => Auth::id()]);

        // Deslogar o usuário
        Auth::logout();

        // Redirecionar o usuário, enviar a mensagem de sucesso
        return redirect()->route('login')->with('success', 'Deslogado com sucesso!');
    }

    // Formulário cadastrar novo usuário
    public function create()
    {
        // Carregar a VIEW
        return view('auth.register');
    }

    // Cadastrar no banco de dados o novo usuário
    // public function store(AuthRegisterUserRequest $request)
    public function store(AuthRegisterUserRequest $request)
    {

        // Bloqueado o cadastro de novos usuários, para desbloquear apagar a linha abaixo e retornar a validação na função - AuthRegisterUserRequest $request
        // return back()->withInput()->with('error', 'O cadastro de novos usuários está bloqueado no momento. Por favor, volte mais tarde!');

        // Capturar possíveis exceções durante a execução.
        try {
            // Cadastrar no banco de dados na tabela usuário
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'password_updated_at' => now(), // Adiciona a data/hora da atualização da senha
                'user_status_id' => 3, // Situação Aguardando Confirmação = 3
            ]);

            // Cadastrar o aceite dos termos de uso 
            UserTermAcceptance::create([
                'accepted_at' => now(),
                'term_version' => 1, // Versão do termo = 1
                'user_id' => $user->id,
            ]);

            // Verificar se o papel "Aluno" existe antes de atribuir
            if (Role::where('name', 'Aluno')->exists()) {
                $user->assignRole('Aluno');
            }

            // Salvar log
            Log::info('Usuário cadastrado e o aceito do termo de uso.', ['user_id' => $user->id]);

            // Redirecionar o usuário, enviar a mensagem de sucesso
            return redirect()->route('login')->with('success', 'Cadastro realizado com sucesso!');
        } catch (Exception $e) {

            // Salvar log
            Log::notice('Usuário não cadastrado.', ['error' => $e->getMessage()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Cadastro não realizado!');
        }
    }
}
