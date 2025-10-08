<?php

namespace Database\Seeders;

use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Capturar possíveis exceções durante a execução do seeder. 
        try {
            // Usuário 1: Robson (Super Admin)
            if (!User::where('email', 'robsonluiz_6@hotmail.com')->first()) {
                $robson = User::create([
                    'name' => 'Robson',
                    'email' => 'robsonluiz_6@hotmail.com',
                    'password' => '123456A#b',
                    'email_verified_at' => now(),
                ]);

                // Atribuir papel de Super Admin
                $robson->assignRole('Super Admin');
            }

            // Usuário 2: John Doe (Admin)
            if (!User::where('email', 'johndoe@hotmail.com')->first()) {
                $johnDoe = User::create([
                    'name' => 'John Doe',
                    'email' => 'johndoe@hotmail.com',
                    'password' => '123456A#b',
                    'email_verified_at' => now(),
                ]);

                // Atribuir papel de Admin
                $johnDoe->assignRole('Admin');
            }

            // Usuário 3: Teste (opcional - para demonstração)
            if (!User::where('email', 'teste@fmsystem.com')->first()) {
                $teste = User::create([
                    'name' => 'Usuário Teste',
                    'email' => 'teste@fmsystem.com',
                    'password' => '123456A#b',
                    'email_verified_at' => now(),
                ]);

                // Atribuir papel de Professor (ou outro papel de teste)
                $teste->assignRole('Professor');
            }

        } catch (Exception $e) {
            // Salvar log
            Log::notice('Usuário não cadastrado.', ['error' => $e->getMessage()]);
        }
    }
}
