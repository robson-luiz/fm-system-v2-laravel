<?php

namespace Database\Seeders;

use App\Models\UserStatus;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class UserStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Capturar possíveis exceções durante a execução do seeder. 
        try {
            // Se não encontrar o registro com o nome e o id, cadastra o registro no BD
            UserStatus::firstOrCreate(
                ['name' => 'Ativo', 'id' => 1],
                ['id' => 1, 'name' => 'Ativo'],
            );

            // Se não encontrar o registro com o nome, cadastra o registro no BD
            UserStatus::firstOrCreate(
                ['name' => 'Inativo', 'id' => 2],
                ['id' => 2, 'name' => 'Inativo'],
            );

            // Se não encontrar o registro com o nome, cadastra o registro no BD
            UserStatus::firstOrCreate(
                ['name' => 'Aguardando Confirmação', 'id' => 3],
                ['id' => 3, 'name' => 'Aguardando Confirmação'],
            );

            // Se não encontrar o registro com o nome, cadastra o registro no BD
            UserStatus::firstOrCreate(
                ['name' => 'Spam', 'id' => 4],
                ['id' => 4, 'name' => 'Spam'],
            );

            // Se não encontrar o registro com o nome, cadastra o registro no BD
            UserStatus::firstOrCreate(
                ['name' => 'Descadastrado', 'id' => 5],
                ['id' => 5, 'name' => 'Descadastrado'],
            );
        } catch (Exception $e) {
            // Salvar log
            Log::notice('Status para usuário não cadastrado.', ['error' => $e->getMessage()]);
        }
    }
}
