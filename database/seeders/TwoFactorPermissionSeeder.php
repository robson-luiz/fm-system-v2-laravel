<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TwoFactorPermissionSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Criar permissão para gerenciar configurações do sistema
        $permission = Permission::firstOrCreate([
            'name' => 'manage-system-settings',
            'title' => 'Gerenciar Configurações do Sistema',
            'guard_name' => 'web'
        ]);

        // Atribuir permissão ao Super Admin
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole && !$superAdminRole->hasPermissionTo($permission)) {
            $superAdminRole->givePermissionTo($permission);
        }

        // Atribuir permissão ao Admin (se existir)
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole && !$adminRole->hasPermissionTo($permission)) {
            $adminRole->givePermissionTo($permission);
        }

        $this->command->info('Permissões de 2FA criadas com sucesso!');
    }
}