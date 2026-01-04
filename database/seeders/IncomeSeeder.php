<?php

namespace Database\Seeders;

use App\Models\Income;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class IncomeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Busca todos os usuários
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('Nenhum usuário encontrado. Execute primeiro o UserSeeder.');
            return;
        }

        $categories = Income::getDefaultCategories();
        $types = ['fixa', 'variavel'];
        $statuses = ['pendente', 'recebida'];
        
        $sources = [
            'Empresa ABC Ltda',
            'Freelance Design',
            'Consultoria TI',
            'Vendas Online',
            'Investimentos',
            'Aluguel de Imóvel',
            'Royalties',
            'Comissões',
            'Prestação de Serviços',
            'Cliente XYZ',
            'Plataforma Digital',
            'E-commerce',
            'Curso Online',
            'Mentoria',
            'Projeto Especial'
        ];

        $descriptions = [
            'Salário' => ['Empresa ABC Ltda', 'Corporação XYZ', 'Startup Tech'],
            'Freelance' => ['Desenvolvimento Web', 'Design Gráfico', 'Consultoria', 'Redação'],
            'Vendas' => ['Produto A', 'Serviço B', 'Curso Online', 'E-book'],
            'Investimentos' => ['Dividendos', 'Renda Fixa', 'Ações', 'Fundos'],
            'Aluguel' => ['Apartamento Centro', 'Casa Comercial', 'Sala Escritório'],
            'Comissões' => ['Venda Imóvel', 'Indicação Cliente', 'Parceria'],
            'Outros' => ['Reembolso', 'Prêmio', 'Bonificação', 'Restituição IR']
        ];

        foreach ($users as $user) {
            // Gera receitas dos últimos 6 meses
            for ($month = 5; $month >= 0; $month--) {
                $startDate = Carbon::now()->subMonths($month)->startOfMonth();
                $endDate = Carbon::now()->subMonths($month)->endOfMonth();
                
                // Gera de 2 a 8 receitas por mês
                $numIncomes = rand(2, 8);
                
                for ($i = 0; $i < $numIncomes; $i++) {
                    $category = $categories[array_rand($categories)];
                    $type = $types[array_rand($types)];
                    $status = $statuses[array_rand($statuses)];
                    
                    // Gera descrição baseada na categoria
                    if (isset($descriptions[$category])) {
                        $descOptions = $descriptions[$category];
                        $description = $descOptions[array_rand($descOptions)];
                    } else {
                        $description = $category . ' ' . chr(65 + rand(0, 25));
                    }
                    
                    // Define valor baseado no tipo e categoria
                    $amount = $this->generateAmount($category, $type);
                    
                    // Data de recebimento aleatória no mês
                    $receivedDate = Carbon::createFromTimestamp(
                        rand($startDate->timestamp, $endDate->timestamp)
                    )->startOfDay();
                    
                    // Ajusta status baseado na data
                    if ($receivedDate->isFuture()) {
                        $status = 'pendente';
                    } elseif ($receivedDate->isPast() && rand(1, 100) <= 85) {
                        // 85% das receitas passadas são recebidas
                        $status = 'recebida';
                    }
                    
                    $source = null;
                    if (rand(1, 100) <= 70) { // 70% têm fonte definida
                        $source = $sources[array_rand($sources)];
                    }
                    
                    $notes = null;
                    if (rand(1, 100) <= 30) { // 30% têm observações
                        $notes = $this->generateNotes($category);
                    }

                    Income::create([
                        'user_id' => $user->id,
                        'description' => $description,
                        'amount' => $amount,
                        'received_date' => $receivedDate,
                        'category' => $category,
                        'type' => $type,
                        'status' => $status,
                        'source' => $source,
                        'notes' => $notes,
                        'created_at' => $receivedDate->copy()->subDays(rand(0, 5)),
                        'updated_at' => $receivedDate->copy()->subDays(rand(0, 5)),
                    ]);
                }
            }
            
            // Adiciona algumas receitas fixas mensais (salário, aluguel, etc.)
            $this->createRecurringIncomes($user);
        }

        $this->command->info('Receitas criadas com sucesso!');
    }

    /**
     * Gera valor baseado na categoria e tipo
     */
    private function generateAmount(string $category, string $type): float
    {
        switch ($category) {
            case 'Salário':
                return rand(280000, 1200000) / 100; // R$ 2.800 a R$ 12.000
            
            case 'Freelance':
                return rand(50000, 800000) / 100; // R$ 500 a R$ 8.000
            
            case 'Vendas':
                return rand(10000, 500000) / 100; // R$ 100 a R$ 5.000
            
            case 'Investimentos':
                return rand(5000, 300000) / 100; // R$ 50 a R$ 3.000
            
            case 'Aluguel':
                return rand(80000, 400000) / 100; // R$ 800 a R$ 4.000
            
            case 'Comissões':
                return rand(20000, 1000000) / 100; // R$ 200 a R$ 10.000
            
            default:
                return rand(5000, 200000) / 100; // R$ 50 a R$ 2.000
        }
    }

    /**
     * Gera observações baseadas na categoria
     */
    private function generateNotes(string $category): string
    {
        $notes = [
            'Salário' => [
                'Salário mensal conforme contrato',
                'Inclui vale alimentação e transporte',
                'Desconto de 11% INSS',
                'Adiantamento salarial',
                'Salário + bonificação por meta'
            ],
            'Freelance' => [
                'Projeto de desenvolvimento web',
                'Consultoria em transformação digital',
                'Design de identidade visual',
                'Pagamento parcelado em 2x',
                'Trabalho extra fim de semana'
            ],
            'Vendas' => [
                'Venda realizada online',
                'Cliente indicado por parceiro',
                'Produto com desconto promocional',
                'Primeira venda do mês',
                'Meta mensal atingida'
            ],
            'Investimentos' => [
                'Dividendos trimestrais',
                'Rendimento poupança',
                'Lucro com day trade',
                'Resgate de aplicação',
                'Juros de título público'
            ],
            'Aluguel' => [
                'Aluguel apartamento centro',
                'Pagamento em dia',
                'Inclui taxa de condomínio',
                'Contrato renovado',
                'Inquilino pontual'
            ],
            'Comissões' => [
                'Comissão venda de imóvel',
                'Indicação de cliente premium',
                'Parceria comercial',
                'Meta de vendas atingida',
                'Bônus por performance'
            ]
        ];

        if (isset($notes[$category])) {
            return $notes[$category][array_rand($notes[$category])];
        }

        return 'Receita registrada conforme planejado';
    }

    /**
     * Cria receitas fixas recorrentes
     */
    private function createRecurringIncomes(User $user): void
    {
        $recurringIncomes = [
            [
                'description' => 'Salário Empresa',
                'amount' => rand(450000, 800000) / 100,
                'category' => 'Salário',
                'type' => 'fixa',
                'source' => 'Empresa ABC Ltda',
                'day' => 5 // Todo dia 5
            ],
            [
                'description' => 'Aluguel Apartamento',
                'amount' => rand(120000, 300000) / 100,
                'category' => 'Aluguel',
                'type' => 'fixa',
                'source' => 'Imobiliária XYZ',
                'day' => 10 // Todo dia 10
            ]
        ];

        foreach ($recurringIncomes as $recurring) {
            // Cria para os próximos 3 meses
            for ($month = 0; $month < 3; $month++) {
                $date = Carbon::now()->addMonths($month)->setDay($recurring['day']);
                
                // Se o dia não existe no mês, usa o último dia
                if ($date->month !== Carbon::now()->addMonths($month)->month) {
                    $date = Carbon::now()->addMonths($month)->endOfMonth();
                }

                Income::create([
                    'user_id' => $user->id,
                    'description' => $recurring['description'],
                    'amount' => $recurring['amount'],
                    'received_date' => $date,
                    'category' => $recurring['category'],
                    'type' => $recurring['type'],
                    'status' => $date->isPast() ? 'recebida' : 'pendente',
                    'source' => $recurring['source'],
                    'notes' => 'Receita fixa mensal',
                ]);
            }
        }
    }
}