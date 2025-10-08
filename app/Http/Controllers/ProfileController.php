<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\JpegEncoder;

class ProfileController extends Controller
{

    // Visualizar os detalhes do perfil
    public function show()
    {

        // Recuperar do banco de dados as informações do usuário logado
        $user = User::where('id', Auth::id())->first();

        // Salvar log
        Log::info('Visualizar o perfil.', ['action_user_id' => Auth::id()]);

        // Carregar a view 
        return view('profile.show', [
            'user' => $user,
        ]);
    }

    // Carregar o formulário editar perfil
    public function edit()
    {

        // Recuperar do banco de dados as informações do usuário logado
        $user = User::where('id', Auth::id())->first();

        // Salvar log
        Log::info('Formulario editar o perfil.', ['action_user_id' => Auth::id()]);

        // Carregar a view 
        return view('profile.edit', ['user' => $user]);
    }

    // Editar no banco de dados o perfil
    public function update(Request $request)
    {
        // Recuperar do banco de dados as informações do usuário logado
        $user = User::where('id', Auth::id())->first();
        // Lorem ipsum dolor sit amet consectetur adipisicing elit. Vel excepturi officiis sed vitae ut eaque iusto dolorum, at, ipsum atque corrupti error dolores. Similique sint illum possimus in, ab error?

        // Retirar o "." e "-" do CPF
        $request->merge([
            'cpf' => preg_replace('/[\.\-]/', '', $request->input('cpf')),
        ]);

        // Validar o formulário
        $request->validate(
            [
                'name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . ($user ? $user->id : null),
                'cpf' => 'required|min:11|max:11|unique:users,cpf,' . ($user ? $user->id : null),
                'alias' => 'max:255',
            ],
            [
                'name.required' => "Campo nome é obrigatório!",
                'name.max' => "O campo nome não pode ser superior a :max caracteres.",
                'email.required' => "Campo e-mail é obrigatório!",
                'email.email' => "Necessário enviar e-mail válido!",
                'email.max' => "O campo e-mail não pode ser superior a :max caracteres.",
                'email.unique' => "O e-mail já está cadastrado!",

                'cpf.required' => "Campo CPF é obrigatório!",
                'cpf.min' => "O campo CPF não pode ser inferior a :min caracteres.",
                'cpf.max' => "O campo CPF não pode ser superior a :max caracteres.",
                'cpf.unique' => "O CPF já está cadastrado!",

                'alias.max' => "O campo alias não pode ser superior a :max caracteres.",
            ]
        );

        // Capturar possíveis exceções durante a execução.
        try {

            // Editar as informações do registro no banco de dados
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'cpf' => $request->cpf,
                'alias' => $request->alias,
            ]);

            // Salvar log
            Log::info('Perfil editado.', ['action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de sucesso
            return redirect()->route('profile.edit')->with('success', 'Perfil editado com sucesso!');
        } catch (Exception $e) {

            // Salvar log
            Log::notice('Perfil não editado.', ['error' => $e->getMessage(), 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Perfil não editado!');
        }
    }

    // Carregar o formulário editar senha do perfil
    public function editPassword()
    {
        // Recuperar do banco de dados as informações do usuário logado
        $user = User::where('id', Auth::id())->first();

        // Carregar a view 
        return view('profile.edit_password', ['user' => $user]);
    }

    // Editar no banco de dados a senha do perfil
    public function updatePassword(Request $request)
    {

        // Validar o formulário
        $request->validate(
            [
                'current_password' => 'required',
                'password' => 'required|confirmed|min:6',
            ],
            [
                'current_password.required' => "Campo senha atual é obrigatório!",
                'password.required' => "Campo nova senha é obrigatório!",
                'password.confirmed' => 'A confirmação da senha não corresponde!',
                'password.min' => "Senha com no mínimo :min caracteres!",
            ]
        );

        // Capturar possíveis exceções durante a execução.
        try {

            // Recuperar do banco de dados as informações do usuário logado
            $user = User::where('id', Auth::id())->first();

            // Verificar se a senha atual está correta
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withInput()->with('error', 'Senha atual incorreta!');
            }

            // Editar as informações do registro no banco de dados
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            // Salvar log
            Log::info('Senha do perfil editado.', ['action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de sucesso
            return redirect()->route('profile.edit_password')->with('success', 'Senha do perfil editada com sucesso!');
        } catch (Exception $e) {

            // Salvar log
            Log::notice('Senha do perfil não editada.', ['error' => $e->getMessage(), 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Senha do perfil não editada!');
        }
    }

    // Carregar o formulário editar imagem do perfil
    public function editImage()
    {
        // Recuperar do banco de dados as informações do usuário logado
        $user = User::where('id', Auth::id())->first();

        // Salvar log
        Log::info('Formulario editar imagem do perfil.', ['action_user_id' => Auth::id()]);

        // Carregar a view 
        return view('profile.edit_image', ['user' => $user]);
    }

    // Editar no banco de dados a imagem do perfil
    public function updateImage(Request $request)
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
            // Recuperar do banco de dados as informações do usuário logado
            $user = User::where('id', Auth::id())->first();

            // Upload no S3
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $file = $request->file('image');

                // Exclui a imagem anterior, se existir
                if (!empty($user->image)) {
                    $oldPath = public_path('images/users/' . $user->id . '/' . $user->image);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

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

                // Upload para a pasta public/images/users
                $uploadPath = public_path('images/users/' . $user->id);
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $file->move($uploadPath, $filename);

                // Atualiza a imagem no banco
                $user->update(['image' => $filename]);
            } else {
                Log::notice('Necessário enviar a imagem.', [
                    'action_user_id' => Auth::id()
                ]);

                return back()->withInput()->with('error', 'Necessário enviar a imagem!');
            }

            // Salvar log
            Log::info('Imagem do perfil editada.', ['action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de sucesso
            return redirect()->route('profile.edit_image')->with('success', 'Imagem do perfil editada com sucesso!');
        } catch (Exception $e) {

            // Salvar log
            Log::notice('Imagem do perfil não editada.', ['error' => $e->getMessage(), 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Imagem do perfil não editada!');
        }
    }
}
