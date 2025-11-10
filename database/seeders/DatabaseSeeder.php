<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seeds que devem rodar em produção
        if (App::environment() === 'production') {
            $this->call([
                // Sistema de permissão
                PermissionSeeder::class,
                RoleSeeder::class,
                
                // Dados dos usuários
                UserStatusSeeder::class,
                UserSeeder::class,
            ]);
        }

        // Seeds que devem rodar em qualquer ambiente
        if (App::environment() !== 'production') {
            $this->call([
                // Sistema de permissão
                PermissionSeeder::class,
                RoleSeeder::class,

                // Dados dos usuários
                UserStatusSeeder::class,
                UserSeeder::class,
                
                // Dados financeiros de teste
                CreditCardSeeder::class,
                IncomeSeeder::class,
            ]);
        }
    }
}
