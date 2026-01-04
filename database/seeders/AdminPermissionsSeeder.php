<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Buscar roles administrativos
            $superAdmin = Role::where('name', 'Super Admin')->first();
            $admin = Role::where('name', 'Admin')->first();

            // Definir permissões que os administradores devem ter
            $adminPermissions = [
                // Configurações do Sistema
                'manage-system-settings',
                'manage-email-sms-settings', 
                'manage-two-factor-settings',
                
                // Gestão de Despesas e Parcelas
                'index-expense',
                'show-expense',
                'create-expense',
                'edit-expense',
                'destroy-expense',
                'manage-installments',
                
                // Gestão de Usuários
                'index-user',
                'show-user',
                'create-user',
                'edit-user',
                'edit-image-user',
                'edit-password-user',
                'password-recovery-link-user',
                'destroy-user',
                'edit-roles-user',
                
                // Gestão de Roles e Permissões
                'index-role',
                'show-role',
                'create-role',
                'edit-role',
                'destroy-role',
                'index-role-permission',
                'index-permission',
                'show-permission',
                'create-permission',
                'edit-permission',
                'destroy-permission',
                
                // Dashboards
                'dashboard-adm',
                'finance-dashboard',
            ];

            // Atribuir permissões ao Super Admin
            if ($superAdmin) {
                foreach ($adminPermissions as $permissionName) {
                    $permission = Permission::where('name', $permissionName)->first();
                    if ($permission && !$superAdmin->hasPermissionTo($permission)) {
                        $superAdmin->givePermissionTo($permission);
                    }
                }
                Log::info('Permissões administrativas atribuídas ao Super Admin');
            }

            // Atribuir permissões ao Admin
            if ($admin) {
                foreach ($adminPermissions as $permissionName) {
                    $permission = Permission::where('name', $permissionName)->first();
                    if ($permission && !$admin->hasPermissionTo($permission)) {
                        $admin->givePermissionTo($permission);
                    }
                }
                Log::info('Permissões administrativas atribuídas ao Admin');
            }

        } catch (Exception $e) {
            Log::error('Erro ao atribuir permissões administrativas: ' . $e->getMessage());
        }
    }
}