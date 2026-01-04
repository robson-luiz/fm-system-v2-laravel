<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ExpensesWithCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        
        if (!$user) {
            $this->command->error('Nenhum usuário encontrado. Execute o DatabaseSeeder primeiro.');
            return;
        }
        
        $categories = Category::all();
        
        if ($categories->isEmpty()) {
            $this->command->error('Nenhuma categoria encontrada. Execute o DatabaseSeeder primeiro.');
            return;
        }
        
        $this->command->info('Criando despesas com categorias para análise de tendências...');
        
        // Criar despesas nos últimos 12 meses
        for ($monthsAgo = 11; $monthsAgo >= 0; $monthsAgo--) {
            $month = Carbon::now()->subMonths($monthsAgo);
            
            // Para cada categoria, criar de 2 a 5 despesas no mês
            foreach ($categories as $category) {
                $numExpenses = rand(2, 5);
                
                for ($i = 0; $i < $numExpenses; $i++) {
                    $baseAmount = $this->getBaseAmountForCategory($category->slug);
                    $amount = $baseAmount * (rand(50, 150) / 100); // Variação de ±50%
                    
                    // Data aleatória dentro do mês
                    $dueDate = (clone $month)->addDays(rand(1, 28));
                    
                    // 70% das despesas são pagas
                    $isPaid = rand(1, 100) <= 70;
                    
                    Expense::create([
                        'user_id' => $user->id,
                        'category_id' => $category->id,
                        'description' => $this->getDescriptionForCategory($category->slug),
                        'amount' => round($amount, 2),
                        'due_date' => $dueDate,
                        'periodicity' => 'monthly',
                        'status' => $isPaid ? 'paid' : 'pending',
                        'payment_date' => $isPaid ? $dueDate : null,
                        'num_installments' => 1,
                    ]);
                }
            }
            
            $this->command->info("✓ Despesas criadas para {$month->format('M/Y')}");
        }
        
        $total = Expense::count();
        $this->command->info("🎉 Total de {$total} despesas criadas com sucesso!");
    }
    
    /**
     * Retorna o valor base para cada categoria
     */
    private function getBaseAmountForCategory(string $slug): float
    {
        return match($slug) {
            'alimentacao' => 800.00,
            'transporte' => 400.00,
            'lazer' => 300.00,
            'saude' => 250.00,
            'educacao' => 500.00,
            'moradia' => 1200.00,
            'servicos' => 350.00,
            'outros' => 200.00,
            default => 150.00,
        };
    }
    
    /**
     * Retorna descrições aleatórias para cada categoria
     */
    private function getDescriptionForCategory(string $slug): string
    {
        $descriptions = [
            'alimentacao' => [
                'Supermercado',
                'Restaurante',
                'Delivery',
                'Padaria',
                'Feira',
                'Lanche',
            ],
            'transporte' => [
                'Combustível',
                'Uber',
                'Estacionamento',
                'Pedágio',
                'Manutenção veículo',
                'Transporte público',
            ],
            'lazer' => [
                'Cinema',
                'Streaming',
                'Show',
                'Viagem',
                'Academia',
                'Passeio',
            ],
            'saude' => [
                'Farmácia',
                'Consulta médica',
                'Dentista',
                'Exames',
                'Plano de saúde',
                'Medicamentos',
            ],
            'educacao' => [
                'Curso online',
                'Livros',
                'Material escolar',
                'Mensalidade',
                'Workshop',
                'Treinamento',
            ],
            'moradia' => [
                'Aluguel',
                'Condomínio',
                'Luz',
                'Água',
                'Internet',
                'Gás',
            ],
            'servicos' => [
                'Celular',
                'Streaming',
                'Assinatura',
                'Conserto',
                'Limpeza',
                'Manutenção',
            ],
            'outros' => [
                'Presente',
                'Doação',
                'Taxa bancária',
                'Imposto',
                'Diversos',
                'Emergência',
            ],
        ];
        
        $categoryDescriptions = $descriptions[$slug] ?? ['Despesa'];
        return $categoryDescriptions[array_rand($categoryDescriptions)];
    }
}
