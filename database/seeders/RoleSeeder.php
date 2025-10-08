<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Capturar possíveis exceções durante a execução do seeder. 
        try {
            /******* Super Admin - tem acesso a todas as páginas *******/
            // Se não encontrar o registro, cadastra o registro no BD
            Role::firstOrCreate(
                ['name' => 'Super Admin'],
                ['name' => 'Super Admin', 'order' => '1'],
            );

            /******* Admin *******/
            // Se não encontrar o registro, cadastra o registro no BD
            $admin = Role::firstOrCreate(
                ['name' => 'Admin'],
                ['name' => 'Admin', 'order' => '2'],
            );

            // Cadastrar permissão para o papel
            $admin->givePermissionTo([ 
                'dashboard',
                            
                'show-profile',
                'edit-profile',
                'edit-profile-image',
                'edit-password-profile',
                'edit-name-profile',
            ]);

            /******* Gestor *******/
            // Se não encontrar o registro, cadastra o registro no BD
            $teacher = Role::firstOrCreate(
                ['name' => 'Gestor'],
                ['name' => 'Gestor', 'order' => '3'],
            );

            // Cadastrar permissão para o papel
            $teacher->givePermissionTo([
                'dashboard',
                            
                'show-profile',
                'edit-profile',
                'edit-profile-image',
                'edit-password-profile',
                'edit-name-profile',
            ]);

            /******* Professor *******/
            // Se não encontrar o registro, cadastra o registro no BD
            $teacher = Role::firstOrCreate(
                ['name' => 'Professor'],
                ['name' => 'Professor', 'order' => '4'],
            );

            // Cadastrar permissão para o papel
            $teacher->givePermissionTo([ 
                'dashboard',
                            
                'show-profile',
                'edit-profile',
                'edit-profile-image',
                'edit-password-profile',
                'edit-name-profile',
            ]);

            /******* Tutor *******/
            // Se não encontrar o registro, cadastra o registro no BD
            $tutor = Role::firstOrCreate(
                ['name' => 'Tutor'],
                ['name' => 'Tutor', 'order' => '5'],
            );

            // Cadastrar permissão para o papel
            $tutor->givePermissionTo([ 
                'dashboard',
                            
                'show-profile',
                'edit-profile',
                'edit-profile-image',
                'edit-password-profile',
                'edit-name-profile',
            ]);

            /******* Moderador *******/
            // Se não encontrar o registro, cadastra o registro no BD
            $teacher = Role::firstOrCreate(
                ['name' => 'Moderador'],
                ['name' => 'Moderador', 'order' => '6'],
            );

            // Cadastrar permissão para o papel
            $teacher->givePermissionTo([
                'dashboard',
                            
                'show-profile',
                'edit-profile',
                'edit-profile-image',
                'edit-password-profile',
                'edit-name-profile',
            ]);

            /******* Aluno *******/
            // Se não encontrar o registro, cadastra o registro no BD
            $student = Role::firstOrCreate(
                ['name' => 'Aluno'],
                ['name' => 'Aluno', 'order' => '7'],
            );

            // Cadastrar permissão para o papel
            $student->givePermissionTo([    
                'dashboard',
                            
                'show-profile',
                'edit-profile',
                'edit-profile-image',
                'edit-password-profile',
                'edit-name-profile',
            ]);
            
        } catch (Exception $e) {
            // Salvar log
            Log::notice('Papel não cadastrado.', ['error' => $e->getMessage()]);
        }
    }
}
