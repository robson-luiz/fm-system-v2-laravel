<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\JpegEncoder;

class UserController extends Controller
{
    // Listar os usuários
    public function index(Request $request)
    {
        // Recuperar os registros do banco dados
        // $users = User::where('id', 1000)->get();
        // $users = User::orderBy('id', 'DESC')->get();
        //$users = User::orderBy('id', 'DESC')->paginate(10);
        $users = User::when(
            $request->filled('name'),
            fn($query) =>
            $query->whereLike('name', '%' . $request->name .  '%')
        )
            ->when(
                $request->filled('email'),
                fn($query) =>
                $query->whereLike('email', '%' . $request->email .  '%')
            )
            ->when(
                $request->filled('start_date_registration'),
                fn($query) =>
                $query->where('created_at', '>=', Carbon::parse($request->start_date_registration))
            )
            ->when(
                $request->filled('end_date_registration'),
                fn($query) =>
                $query->where('created_at', '<=', Carbon::parse($request->end_date_registration))
            )
            ->orderBy('id', 'DESC')
            ->paginate(10)
            ->withQueryString();

        // Salvar log
        Log::info('Listar os usuários.', ['action_user_id' => Auth::id()]);

        // Carregar a view 
        return view('users.index', [
            'menu' => 'users',
            'name' => $request->name,
            'email' => $request->email,
            'start_date_registration' => $request->start_date_registration,
            'end_date_registration' => $request->end_date_registration,
            'users' => $users
        ]);
    }

    // Visualizar os detalhes do usuário
    public function show(User $user)
    {

        // Salvar log
        Log::info('Visualizar o usuário.', ['user_id' => $user->id, 'action_user_id' => Auth::id()]);

        // Carregar a view 
        return view('users.show', [
            'menu' => 'users',
            'user' => $user,
        ]);
    }

    // Carregar o formulário cadastrar novo usuário
    public function create()
    {
        // Recuperar os papéis
        $roles = Role::pluck('name')->all();

        // Carregar a view 
        return view('users.create', ['menu' => 'users', 'roles' => $roles]);
    }

    // Cadastrar no banco de dados o novo usuário
    public function store(UserRequest $request)
    {
        // Capturar possíveis exceções durante a execução.
        try {
            // Cadastrar no banco de dados na tabela usuário
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'cpf' => $request->cpf,
                'alias' => $request->alias,
                'password' => $request->password,
                'password_updated_at' => now(), // Adiciona a data/hora da atualização da senha 
            ]);

            // Verificar se veio algum papel selecionado
            if ($request->filled('roles')) {
                // Verifica se todos os papéis existem (opcional, mas recomendado)
                $validRoles = Role::whereIn('name', $request->roles)->pluck('name')->toArray();

                // Atribui os papéis válidos ao usuário
                $user->syncRoles($validRoles); // syncRoles() vários papeís ou assignRole() se for apenas um
            }

            // Salvar log
            Log::info('Usuário cadastrado.', ['user_id' => $user->id, 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de sucesso
            return redirect()->route('users.show', ['user' => $user->id])->with('success', 'Usuário cadastrado com sucesso!');
        } catch (Exception $e) {

            // Salvar log
            Log::notice('Usuário não cadastrado.', ['error' => $e->getMessage(), 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Usuário não cadastrado!');
        }
    }

    // Carregar o formulário editar usuário
    public function edit(User $user)
    {
        // Recuperar os papéis
        $roles = Role::pluck('name')->all();

        // Recuperar os papéis do usuário
        $userRoles = $user->roles->pluck('name')->toArray();

        // Carregar a view 
        return view('users.edit', [
            'menu' => 'users',
            'user' => $user,
            'roles' => $roles,
            'userRoles' => $userRoles
        ]);
    }

    // Editar no banco de dados o usuário
    public function update(UserRequest $request, User $user)
    {
        // Capturar possíveis exceções durante a execução.
        try {
            // Editar as informações do registro no banco de dados
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'cpf' => $request->cpf,
                'alias' => $request->alias,
            ]);

            // Se houver papéis enviados no request, sincroniza-os com o usuário
            if ($request->filled('roles')) {
                // Verifica se todos os papéis existem (opcional, mas recomendado)
                $validRoles = Role::whereIn('name', $request->roles)->pluck('name')->toArray();

                // Atribui os papéis válidos ao usuário
                $user->syncRoles($validRoles); // syncRoles() vários papeís ou assignRole() se for apenas um
            } else {
                // Se nenhum papel for enviado, remove todos os papéis do usuário
                $user->syncRoles([]);
            }

            // Salvar log
            Log::info('Usuário editado.', ['user_id' => $user->id, 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de sucesso
            return redirect()->route('users.show', ['user' => $user->id])->with('success', 'Usuário editado com sucesso!');
        } catch (Exception $e) {

            // Salvar log
            Log::notice('Usuário não editado.', ['error' => $e->getMessage(), 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Usuário não editado!');
        }
    }

    // Carregar o formulário editar senha do usuário
    public function editPassword(User $user)
    {
        // Carregar a view 
        return view('users.edit_password', ['user' => $user]);
    }

    // Editar no banco de dados a senha do usuário
    public function updatePassword(Request $request, User $user)
    {
        // Validar o formulário
        $request->validate(
            [
                'password' => 'required|confirmed|min:6',
            ],
            [
                'password.required' => "Campo senha é obrigatório!",
                'password.confirmed' => 'A confirmação da senha não corresponde!',
                'password.min' => "Senha com no mínimo :min caracteres!",
            ]
        );

        // Capturar possíveis exceções durante a execução.
        try {
            // Editar as informações do registro no banco de dados
            $user->update([
                'password' => $request->password,
                'password_updated_at' => now(), // Adiciona a data/hora da atualização da senha
            ]);

            // Salvar log
            Log::info('Senha do usuário editado.', ['user_id' => $user->id, 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de sucesso
            return redirect()->route('users.show', ['user' => $user->id])->with('success', 'Senha do usuário editado com sucesso!');
        } catch (Exception $e) {

            // Salvar log
            Log::notice('Senha do usuário não editado.', ['error' => $e->getMessage(), 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Senha do usuário não editado!');
        }
    }

    // Carregar o formulário editar imagem do usuário
    public function editImage(User $user)
    {

        // Salvar log
        Log::info('Formulario editar imagem do usuário.', ['user_id' => $user->id, 'action_user_id' => Auth::id()]);

        // Carregar a view 
        return view('users.edit_image', ['user' => $user]);
    }

    // Editar no banco de dados a imagem do perfil
    public function updateImage(User $user, Request $request)
    {
        // Validar o formulário
        $request->validate(
            [
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ],
            [
                'image.required' => "Campo imagem é obrigatório!",
                'image.image' => "Necessário enviar arquivo do tipo imagem!",
                'image.mimes' => "Imagem deve ser do tipo: jpeg, png ou jpg!",
                'image.max' => "Tamanho da imagem deve ser no máximo :max KB!",
            ]
        );

        // Capturar possíveis exceções durante a execução.
        try {

            // Upload no S3
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $file = $request->file('image');

                // Nome do arquivo sem extensão
                $nameWithoutExt = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                // Slug do nome (ex: "Foto de Perfil" => "foto-de-perfil")
                $slug = Str::slug($nameWithoutExt);

                // Extensão (ex: jpg)
                $extension = $file->getClientOriginalExtension();

                // Nome final do arquivo
                $filename = "{$slug}.{$extension}";

                // === PROCESSAMENTO DA IMAGEM ===
                // Cria manager com GD
                $manager = new ImageManager(new Driver());

                // Carrega imagem
                $image = $manager->read($file->getRealPath());

                // Dimensões
                $width  = $image->width();
                $height = $image->height();

                if ($width !== $height) {
                    $side = min($width, $height);
                    $x = intval(($width  - $side) / 2);
                    $y = intval(($height - $side) / 2);

                    // Recorta quadrado
                    $image->crop($side, $side, $x, $y);
                }

                // Redimensiona 150x150
                $image->resize(150, 150);

                // Converte para binário no formato original (qualidade 100%)
                // Detecta a extensão enviada
                $extension = strtolower($file->getClientOriginalExtension());

                switch ($extension) {
                    case 'png':
                        // PNG → nível de compressão (0 sem compressão, 9 máxima)
                        $encoded = $image->encode(new PngEncoder(0));
                        break;

                    case 'jpg':
                    case 'jpeg':
                        // JPG → qualidade 0 a 100
                        $encoded = $image->encode(new JpegEncoder(100));
                        break;

                    default:
                        // fallback: força para jpg
                        $encoded = $image->encode(new JpegEncoder(100));
                        $extension = 'jpg';
                        $filename = "{$slug}.jpg";
                        break;
                }

                // Exclui a imagem anterior, se existir
                if (!empty($user->image) && Storage::disk('s3')->exists("users/{$user->id}/" . $user->image)) {
                    Storage::disk('s3')->delete("users/{$user->id}/" . $user->image);
                }

                // Upload para o S3
                Storage::disk('s3')->put(
                    "users/{$user->id}/{$filename}",   // caminho final no S3
                    (string) $encoded                  // conteúdo binário da imagem
                );

                // Atualiza a imagem no banco
                $user->update(['image' => $filename]);
            } else {
                Log::notice('Editar a imagem do usuário necessário enviar a imagem.', [
                    'action_user_id' => Auth::id()
                ]);

                return back()->withInput()->with('error', 'Necessário enviar a imagem!');
            }

            // Salvar log
            Log::info('Imagem do perfil editada.', ['action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de sucesso
            return redirect()->route('users.show', ['user' => $user->id])->with('success', 'Imagem do usuário editada com sucesso!');
        } catch (Exception $e) {

            // Salvar log
            Log::notice('Imagem do perfil não editada.', ['error' => $e->getMessage(), 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Imagem do perfil não editada!');
        }
    }

    // Excluir o curso do banco de dados
    public function destroy(User $user)
    {
        // Capturar possíveis exceções durante a execução.
        try {

            // Excluir o registro do banco de dados
            $user->delete();

            // Salvar log
            Log::info('Usuário apagado.', ['user_id' => $user->id, 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de sucesso
            return redirect()->route('users.index')->with('success', 'Usuário apagado com sucesso!');
        } catch (Exception $e) {

            // Salvar log
            Log::notice('Usuário não editado.', ['error' => $e->getMessage(), 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Usuário não apagado!');
        }
    }

    // Gerar o link recuperar a senha
    public function passwordRecoveryLink(User $user)
    {

        // Gerar um token aleatório
        $plainToken = Str::random(64);

        // Apagar tokens antigos para esse e-mail (boa prática)
        DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->delete();

        // Salvar token hasheado no banco
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($plainToken),
            'created_at' => Carbon::now(),
        ]);

        // Montar o link de redefinição
        // 'password.reset' é a rota padrão do Laravel para redefinir a senha
        // Sobre escrever o endereço
        $resetLink = rtrim(config('app.url_login'), '/') . route('password.reset', [
            'token' => $plainToken,
            'email' => $user->email
        ], false);
        // $resetLink = str_replace('127.0.0.1:8082', '127.0.0.1:8081', url(route('password.reset', [
        //     'token' => $plainToken,
        //     'email' => $user->email
        // ], false)));

        // Salvar log
        Log::info('Visualizar a chave recuperar senha.', [
            'user_id' => $user->id,
            'action_user_id' => Auth::id()
        ]);

        // Enviar link para a view
        return view('users.password_recovery_link', [
            'menu' => 'users',
            'user' => $user,
            'resetLink' => $resetLink
        ]);
    }
}
