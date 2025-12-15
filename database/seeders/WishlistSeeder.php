<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class WishlistSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        
        if (!$user) {
            $this->command->warn('Nenhum usuário encontrado. Execute UserSeeder primeiro.');
            return;
        }

        $wishlists = [
            [
                'name' => 'Viagem para a Europa',
                'description' => 'Viagem de 15 dias visitando Paris, Roma e Barcelona',
                'target_amount' => 25000.00,
                'current_amount' => 8500.00,
                'priority' => 'alta',
                'target_date' => Carbon::now()->addMonths(10),
                'status' => 'em_progresso',
            ],
            [
                'name' => 'MacBook Pro',
                'description' => 'MacBook Pro 16" M3 para trabalho',
                'target_amount' => 18000.00,
                'current_amount' => 12000.00,
                'priority' => 'alta',
                'target_date' => Carbon::now()->addMonths(4),
                'status' => 'em_progresso',
            ],
            [
                'name' => 'Reforma da Sala',
                'description' => 'Pintura, móveis novos e decoração',
                'target_amount' => 15000.00,
                'current_amount' => 3000.00,
                'priority' => 'media',
                'target_date' => Carbon::now()->addMonths(12),
                'status' => 'em_progresso',
            ],
            [
                'name' => 'Carro Novo',
                'description' => 'Entrada para carro 0km',
                'target_amount' => 40000.00,
                'current_amount' => 15000.00,
                'priority' => 'alta',
                'target_date' => Carbon::now()->addMonths(18),
                'status' => 'em_progresso',
            ],
            [
                'name' => 'Curso de Inglês',
                'description' => 'Curso intensivo de 6 meses',
                'target_amount' => 4500.00,
                'current_amount' => 4500.00,
                'priority' => 'media',
                'target_date' => Carbon::now()->subMonths(1),
                'status' => 'concluida',
            ],
        ];

        foreach ($wishlists as $wishlist) {
            Wishlist::create(array_merge($wishlist, ['user_id' => $user->id]));
        }

        $this->command->info('🎯 5 objetivos da wishlist criados com sucesso!');
    }
}

