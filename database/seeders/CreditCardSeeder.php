<?php

namespace Database\Seeders;

use App\Models\CreditCard;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CreditCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar usuários para vincular cartões
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('Nenhum usuário encontrado. Execute primeiro o UserSeeder.');
            return;
        }

        // Dados dos cartões de exemplo
        $creditCards = [
            [
                'name' => 'Cartão Nubank',
                'bank' => 'Nubank',
                'last_four_digits' => '1234',
                'card_limit' => 5000.00,
                'available_limit' => 3200.00,
                'closing_day' => 15,
                'due_day' => 25,
                'best_purchase_day' => 16,
                'interest_rate' => 12.50,
                'annual_fee' => 0.00,
                'is_active' => true,
            ],
            [
                'name' => 'Visa Gold Bradesco',
                'bank' => 'Bradesco',
                'last_four_digits' => '5678',
                'card_limit' => 8000.00,
                'available_limit' => 6500.00,
                'closing_day' => 10,
                'due_day' => 20,
                'best_purchase_day' => 11,
                'interest_rate' => 14.80,
                'annual_fee' => 180.00,
                'is_active' => true,
            ],
            [
                'name' => 'Mastercard Itaú',
                'bank' => 'Itaú',
                'last_four_digits' => '9012',
                'card_limit' => 3000.00,
                'available_limit' => 2850.00,
                'closing_day' => 5,
                'due_day' => 15,
                'best_purchase_day' => 6,
                'interest_rate' => 13.25,
                'annual_fee' => 120.00,
                'is_active' => true,
            ],
            [
                'name' => 'Cartão Inter',
                'bank' => 'Banco Inter',
                'last_four_digits' => '3456',
                'card_limit' => 2500.00,
                'available_limit' => 2500.00,
                'closing_day' => 28,
                'due_day' => 8,
                'best_purchase_day' => 1,
                'interest_rate' => 11.90,
                'annual_fee' => 0.00,
                'is_active' => true,
            ],
            [
                'name' => 'Cartão Santander',
                'bank' => 'Santander',
                'last_four_digits' => '7890',
                'card_limit' => 6000.00,
                'available_limit' => 4200.00,
                'closing_day' => 22,
                'due_day' => 2,
                'best_purchase_day' => 23,
                'interest_rate' => 15.50,
                'annual_fee' => 240.00,
                'is_active' => false, // Cartão inativo para teste
            ],
            [
                'name' => 'C6 Bank Mastercard',
                'bank' => 'C6 Bank',
                'last_four_digits' => '2468',
                'card_limit' => 4500.00,
                'available_limit' => 4100.00,
                'closing_day' => 12,
                'due_day' => 22,
                'best_purchase_day' => 13,
                'interest_rate' => 10.75,
                'annual_fee' => 0.00,
                'is_active' => true,
            ]
        ];

        // Distribuir cartões entre os usuários
        foreach ($creditCards as $index => $cardData) {
            // Distribuir cartões ciclicamente entre os usuários
            $user = $users[$index % $users->count()];
            
            $cardData['user_id'] = $user->id;
            
            CreditCard::create($cardData);
            
            $this->command->info("Cartão '{$cardData['name']}' criado para o usuário '{$user->name}'");
        }

        $this->command->info("✅ CreditCardSeeder executado com sucesso!");
        $this->command->info("📊 Foram criados " . count($creditCards) . " cartões de crédito para teste.");
    }
}
