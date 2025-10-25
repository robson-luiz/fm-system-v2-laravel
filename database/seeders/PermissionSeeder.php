<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Capturar possíveis exceções durante a execução do seeder. 
        try {
            // Criar o array de páginas
            $permissions = [   

                // Permissão para acessar o dashboard do ADM
                ['title'=> 'Dashboard do ADM', 'name' => 'dashboard-adm'],
                
                // Permissão para acessar o dashboard do CLMS
                ['title'=> 'Dashboard do CLMS', 'name' => 'dashboard'], 
                
                // Permissões para gerenciar os dados dos usuários
                ['title'=> 'Listar os usuários', 'name' => 'index-user'],
                ['title'=> 'Visualizar o usuário', 'name' => 'show-user'],
                ['title'=> 'Cadastrar o usuário', 'name' => 'create-user'],
                ['title'=> 'Editar o usuário', 'name' => 'edit-user'],
                ['title'=> 'Editar a imagem do usuário', 'name' => 'edit-image-user'],
                ['title'=> 'Editar a senha do usuário', 'name' => 'edit-password-user'],
                ['title'=> 'Gerar o link recuperar senha', 'name' => 'password-recovery-link-user'],
                ['title'=> 'Apagar o usuário', 'name' => 'destroy-user'],
                ['title'=> 'Editar papéis do usuário', 'name' => 'edit-roles-user'],                
                
                // Permissões para gerenciar os dados do perfil do usuário logado
                ['title'=> 'Visualizar o perfil', 'name' => 'show-profile'],
                ['title'=> 'Editar o perfil', 'name' => 'edit-profile'],
                ['title'=> 'Editar a imagem do perfil', 'name' => 'edit-profile-image'],
                ['title'=> 'Editar a senha do perfil', 'name' => 'edit-password-profile'],
                ['title'=> 'Editar o nome no primeiro acesso na V8', 'name' => 'edit-name-profile'],

                // Permissões para gerenciar os status usuário
                ['title'=> 'Listar os status usuários', 'name' => 'index-user-status'],
                ['title'=> 'Visualizar o status usuário', 'name' => 'show-user-status'],
                ['title'=> 'Cadastrar o status usuário', 'name' => 'create-user-status'],
                ['title'=> 'Editar o status usuário', 'name' => 'edit-user-status'],
                ['title'=> 'Apagar o status usuário', 'name' => 'destroy-user-status'],
                
                // Permissões para Gerenciar os papéis
                ['title'=> 'Listar os papéis', 'name' => 'index-role'],
                ['title'=> 'Visualizar o papel', 'name' => 'show-role'],
                ['title'=> 'Cadastrar o papel', 'name' => 'create-role'],
                ['title'=> 'Editar o papel', 'name' => 'edit-role'],
                ['title'=> 'Apagar o papel', 'name' => 'destroy-role'],
                ['title'=> 'Listar as permissões do papel', 'name' => 'index-role-permission'],
                
                // Permissões para Gerenciar as permissões
                ['title'=> 'Listar as permissões', 'name' => 'index-permission'],
                ['title'=> 'Visualizar a permissão', 'name' => 'show-permission'],
                ['title'=> 'Cadastrar a permissão', 'name' => 'create-permission'],
                ['title'=> 'Editar a permissão', 'name' => 'edit-permission'],
                ['title'=> 'Apagar a permissão', 'name' => 'destroy-permission'],

                // Permissões para Gerenciar despesas
                ['title'=> 'Listar as despesas', 'name' => 'index-expense'],
                ['title'=> 'Visualizar a despesa', 'name' => 'show-expense'],
                ['title'=> 'Cadastrar a despesa', 'name' => 'create-expense'],
                ['title'=> 'Editar a despesa', 'name' => 'edit-expense'],
                ['title'=> 'Apagar a despesa', 'name' => 'destroy-expense'],
                ['title'=> 'Gerenciar parcelas', 'name' => 'manage-installments'],

                // Permissões para Gerenciar cartões de crédito
                ['title'=> 'Listar os cartões', 'name' => 'index-credit-card'],
                ['title'=> 'Visualizar o cartão', 'name' => 'show-credit-card'],
                ['title'=> 'Cadastrar o cartão', 'name' => 'create-credit-card'],
                ['title'=> 'Editar o cartão', 'name' => 'edit-credit-card'],
                ['title'=> 'Apagar o cartão', 'name' => 'destroy-credit-card'],

                // Permissões para Gerenciar receitas
                ['title'=> 'Listar as receitas', 'name' => 'index-income'],
                ['title'=> 'Visualizar a receita', 'name' => 'show-income'],
                ['title'=> 'Cadastrar a receita', 'name' => 'create-income'],
                ['title'=> 'Editar a receita', 'name' => 'edit-income'],
                ['title'=> 'Apagar a receita', 'name' => 'destroy-income'],

                // Permissões para Gerenciar wishlist
                ['title'=> 'Listar a wishlist', 'name' => 'index-wishlist'],
                ['title'=> 'Visualizar item da wishlist', 'name' => 'show-wishlist'],
                ['title'=> 'Cadastrar item na wishlist', 'name' => 'create-wishlist'],
                ['title'=> 'Editar item da wishlist', 'name' => 'edit-wishlist'],
                ['title'=> 'Apagar item da wishlist', 'name' => 'destroy-wishlist'],

                // Permissões para Gerenciar empréstimos
                ['title'=> 'Listar os empréstimos', 'name' => 'index-loan'],
                ['title'=> 'Visualizar o empréstimo', 'name' => 'show-loan'],
                ['title'=> 'Cadastrar o empréstimo', 'name' => 'create-loan'],
                ['title'=> 'Editar o empréstimo', 'name' => 'edit-loan'],
                ['title'=> 'Apagar o empréstimo', 'name' => 'destroy-loan'],

                // Permissão para acessar o dashboard financeiro
                ['title'=> 'Dashboard Financeiro', 'name' => 'finance-dashboard'],

                // Permissões para Configurações do Sistema
                ['title'=> 'Gerenciar Configurações do Sistema', 'name' => 'manage-system-settings'],
                ['title'=> 'Configurações de Email e SMS', 'name' => 'manage-email-sms-settings'],
                ['title'=> 'Configurações de Autenticação 2FA', 'name' => 'manage-two-factor-settings'],

            ];

            foreach ($permissions as $permission) {
                // Se não encontrar o registro, cadastra o registro no BD
                Permission::firstOrCreate(
                    ['title' => $permission['title'], 'name' => $permission['name']],
                    [
                        'title' => $permission['title'],
                        'name' => $permission['name'],
                        'guard_name' => 'web'
                    ],
                );
            }
        } catch (Exception $e) {
            // Salvar log
            Log::notice('Permissão não cadastrada.', ['error' => $e->getMessage()]);
        }
    }
}
