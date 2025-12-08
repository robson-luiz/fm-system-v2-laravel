<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use App\Models\CreditCard;
use App\Models\Installment;
use App\Services\AlertService;
use App\Services\OverdueExpenseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $alertService;
    protected $overdueExpenseService;

    public function __construct(AlertService $alertService, OverdueExpenseService $overdueExpenseService)
    {
        $this->alertService = $alertService;
        $this->overdueExpenseService = $overdueExpenseService;
    }

    // Página inicial do administrativo
    public function index()
    {
        // Capturar possíveis exceções durante a execução.
        try {            
            $userId = Auth::id();

            // 1. Estatísticas de Receitas
            $incomesStats = $this->getIncomesStats($userId);
            
            // 2. Estatísticas de Despesas
            $expensesStats = $this->getExpensesStats($userId);
            
            // 3. Estatísticas de Cartões de Crédito
            $creditCardsStats = $this->getCreditCardsStats($userId);
            
            // 4. Alertas Inteligentes (usando o novo serviço)
            $alerts = $this->alertService->getAllAlerts($userId);
            $alertsSummary = $this->alertService->getImportantAlertsSummary($userId);
            $alertsCounts = $this->alertService->getAlertCountsByPriority($userId);
            
            // 5. Dados para Gráficos
            $chartsData = $this->getChartsData($userId);
            
            // 6. Saldo Geral
            $balance = $this->calculateBalance($incomesStats, $expensesStats);

            // Salvar log
            Log::notice('Dashboard carregado.', ['action_user_id' => $userId]);

            // Carregar a VIEW com todas as estatísticas
            return view('dashboard.index', [
                'menu' => 'dashboard',
                'incomesStats' => $incomesStats,
                'expensesStats' => $expensesStats,
                'creditCardsStats' => $creditCardsStats,
                'alerts' => $alerts,
                'alertsSummary' => $alertsSummary,
                'alertsCounts' => $alertsCounts,
                'chartsData' => $chartsData,
                'balance' => $balance,
            ]);
        } catch (Exception $e) {

            // Salvar log
            Log::notice('Dados para o dashboard não gerado.', ['error' => $e->getMessage(), 'action_user_id' => Auth::id()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Dados para o dashboard não gerado!');
        }
    }

    /**
     * Estatísticas de Receitas (Otimizado)
     */
    private function getIncomesStats($userId)
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        // Query otimizada com agregações em uma única consulta
        $monthlyStats = Income::selectRaw('
            SUM(amount) as monthly_total,
            SUM(CASE WHEN status = "recebida" THEN amount ELSE 0 END) as monthly_received,
            SUM(CASE WHEN status = "pendente" THEN amount ELSE 0 END) as monthly_pending
        ')
        ->forUser($userId)
        ->whereMonth('received_date', $currentMonth)
        ->whereYear('received_date', $currentYear)
        ->first();
        
        // Outras estatísticas em consultas separadas mas otimizadas
        $yearlyTotal = Income::forUser($userId)
            ->whereYear('received_date', $currentYear)
            ->sum('amount');
            
        $fixedCount = Income::forUser($userId)
            ->where('type', 'fixa')
            ->count();
            
        $upcomingCount = Income::forUser($userId)
            ->where('status', 'pendente')
            ->whereBetween('received_date', [now(), now()->addDays(30)])
            ->count();
        
        return [
            'monthly_total' => $monthlyStats->monthly_total ?? 0,
            'monthly_received' => $monthlyStats->monthly_received ?? 0,
            'monthly_pending' => $monthlyStats->monthly_pending ?? 0,
            'yearly_total' => $yearlyTotal ?? 0,
            'fixed_count' => $fixedCount,
            'upcoming_count' => $upcomingCount,
        ];
    }

    /**
     * Estatísticas de Despesas (Otimizado)
     */
    private function getExpensesStats($userId)
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        // Query otimizada com agregações em uma única consulta
        $monthlyStats = Expense::selectRaw('
            SUM(amount) as monthly_total,
            SUM(CASE WHEN status = "paid" THEN amount ELSE 0 END) as monthly_paid,
            SUM(CASE WHEN status = "pending" THEN amount ELSE 0 END) as monthly_pending
        ')
        ->where('user_id', $userId)
        ->whereMonth('due_date', $currentMonth)
        ->whereYear('due_date', $currentYear)
        ->first();
        
        // Estatísticas adicionais
        $yearlyTotal = Expense::where('user_id', $userId)
            ->whereYear('due_date', $currentYear)
            ->sum('amount');
            
        $overdueCount = Expense::where('user_id', $userId)
            ->where('status', 'pending')
            ->where('due_date', '<', now())
            ->count();
            
        $dueSoonCount = Expense::where('user_id', $userId)
            ->where('status', 'pending')
            ->whereBetween('due_date', [now(), now()->addDays(7)])
            ->count();
        
        return [
            'monthly_total' => $monthlyStats->monthly_total ?? 0,
            'monthly_paid' => $monthlyStats->monthly_paid ?? 0,
            'monthly_pending' => $monthlyStats->monthly_pending ?? 0,
            'yearly_total' => $yearlyTotal ?? 0,
            'overdue_count' => $overdueCount,
            'due_soon_count' => $dueSoonCount,
        ];
    }

    /**
     * Estatísticas de Cartões de Crédito
     */
    private function getCreditCardsStats($userId)
    {
        $creditCards = CreditCard::where('user_id', $userId)->active()->get();
        
        return [
            'total_count' => $creditCards->count(),
            'total_limit' => $creditCards->sum('card_limit'),
            'total_available' => $creditCards->sum('available_limit'),
            'total_used' => $creditCards->sum(function($card) {
                return $card->card_limit - $card->available_limit;
            }),
            'average_usage' => $creditCards->count() > 0 
                ? $creditCards->avg(function($card) {
                    return $card->limit_usage_percentage;
                }) : 0,
            'cards_near_limit' => $creditCards->filter(function($card) {
                return $card->limit_usage_percentage > 80;
            })->count(),
        ];
    }

    /**
     * Dados para Gráficos
     */
    private function getChartsData($userId)
    {
        // Dados para gráfico de receitas vs despesas (últimos 6 meses)
        $monthlyData = $this->getMonthlyIncomesVsExpenses($userId);
        
        // Dados para gráfico de gastos por categoria
        $expensesByCategory = $this->getExpensesByCategory($userId);
        
        // Dados para gráfico de receitas por tipo
        $incomesByType = $this->getIncomesByType($userId);
        
        // Evolução do limite dos cartões
        $creditCardUsage = $this->getCreditCardUsage($userId);
        
        return [
            'monthly_comparison' => $monthlyData,
            'expenses_by_category' => $expensesByCategory,
            'incomes_by_type' => $incomesByType,
            'credit_card_usage' => $creditCardUsage,
        ];
    }

    /**
     * Receitas vs Despesas dos últimos 6 meses
     */
    private function getMonthlyIncomesVsExpenses($userId)
    {
        $months = [];
        $incomes = [];
        $expenses = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;
            
            $months[] = $date->format('M/Y');
            
            $incomes[] = Income::forUser($userId)
                ->whereMonth('received_date', $month)
                ->whereYear('received_date', $year)
                ->sum('amount');
                
            $expenses[] = Expense::where('user_id', $userId)
                ->whereMonth('due_date', $month)
                ->whereYear('due_date', $year)
                ->sum('amount');
        }
        
        return [
            'labels' => $months,
            'incomes' => $incomes,
            'expenses' => $expenses,
        ];
    }

    /**
     * Gastos por categoria baseado nos cartões de crédito
     */
    private function getExpensesByCategory($userId)
    {
        // Como não temos categoria nas despesas, vamos usar os cartões
        $creditCards = CreditCard::where('user_id', $userId)->with('expenses')->get();
        
        $categories = [];
        foreach ($creditCards as $card) {
            $categories[$card->name] = $card->expenses()
                ->thisMonth()
                ->sum('amount');
        }
        
        // Despesas sem cartão
        $noCardExpenses = Expense::where('user_id', $userId)
            ->whereNull('credit_card_id')
            ->thisMonth()
            ->sum('amount');
            
        if ($noCardExpenses > 0) {
            $categories['Outros'] = $noCardExpenses;
        }
        
        return $categories;
    }

    /**
     * Receitas por tipo
     */
    private function getIncomesByType($userId)
    {
        return [
            'Fixa' => Income::forUser($userId)
                ->where('type', 'fixa')
                ->thisMonth()
                ->sum('amount'),
            'Variável' => Income::forUser($userId)
                ->where('type', 'variavel')
                ->thisMonth()
                ->sum('amount'),
        ];
    }

    /**
     * Uso dos cartões de crédito
     */
    private function getCreditCardUsage($userId)
    {
        return CreditCard::where('user_id', $userId)
            ->active()
            ->get()
            ->map(function($card) {
                return [
                    'name' => $card->name,
                    'usage_percentage' => $card->limit_usage_percentage,
                    'used_amount' => $card->used_limit,
                    'available_amount' => $card->available_limit,
                ];
            });
    }

    /**
     * Calcular saldo geral
     */
    private function calculateBalance($incomesStats, $expensesStats)
    {
        $monthlyBalance = $incomesStats['monthly_received'] - $expensesStats['monthly_paid'];
        $projectedBalance = $incomesStats['monthly_total'] - $expensesStats['monthly_total'];
        
        return [
            'monthly_actual' => $monthlyBalance,
            'monthly_projected' => $projectedBalance,
            'yearly_projected' => $incomesStats['yearly_total'] - $expensesStats['yearly_total'],
        ];
    }

    /**
     * Obter contas vencidas para o modal (AJAX)
     */
    public function getOverdueAccounts()
    {
        try {
            $userId = Auth::id();
            $overdueData = $this->overdueExpenseService->getOverdueAccounts($userId);
            
            return response()->json([
                'success' => true,
                'data' => $overdueData,
            ]);
        } catch (Exception $e) {
            Log::error('Erro ao obter contas vencidas.', ['error' => $e->getMessage(), 'user_id' => Auth::id()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter contas vencidas.',
            ], 500);
        }
    }

    /**
     * Marcar contas como pagas em lote (AJAX)
     */
    public function markAccountsAsPaid(Request $request)
    {
        try {
            $userId = Auth::id();
            
            $expenseIds = $request->input('expense_ids', []);
            $installmentIds = $request->input('installment_ids', []);
            
            // Validar que os IDs pertencem ao usuário autenticado
            if (!empty($expenseIds)) {
                $validExpenseIds = Expense::where('user_id', $userId)
                    ->whereIn('id', $expenseIds)
                    ->pluck('id')
                    ->toArray();
                    
                if (count($validExpenseIds) !== count($expenseIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Algumas despesas não pertencem ao usuário.',
                    ], 403);
                }
            }
            
            if (!empty($installmentIds)) {
                $validInstallmentIds = Installment::whereHas('expense', function ($query) use ($userId) {
                        $query->where('user_id', $userId);
                    })
                    ->whereIn('id', $installmentIds)
                    ->pluck('id')
                    ->toArray();
                    
                if (count($validInstallmentIds) !== count($installmentIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Algumas parcelas não pertencem ao usuário.',
                    ], 403);
                }
            }
            
            // Marcar como pagas
            DB::beginTransaction();
            
            $expensesUpdated = $this->overdueExpenseService->markExpensesAsPaid($expenseIds);
            $installmentsUpdated = $this->overdueExpenseService->markInstallmentsAsPaid($installmentIds);
            
            DB::commit();
            
            Log::notice('Contas marcadas como pagas em lote.', [
                'action_user_id' => $userId,
                'expenses_updated' => $expensesUpdated,
                'installments_updated' => $installmentsUpdated,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Contas marcadas como pagas com sucesso!',
                'expenses_updated' => $expensesUpdated,
                'installments_updated' => $installmentsUpdated,
            ]);
            
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Erro ao marcar contas como pagas.', ['error' => $e->getMessage(), 'user_id' => Auth::id()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao marcar contas como pagas.',
            ], 500);
        }
    }

    /**
     * Obter estatísticas atualizadas do dashboard (AJAX)
     */
    public function getUpdatedStats()
    {
        try {
            $userId = Auth::id();

            // Recalcular todas as estatísticas
            $incomesStats = $this->getIncomesStats($userId);
            $expensesStats = $this->getExpensesStats($userId);
            $creditCardsStats = $this->getCreditCardsStats($userId);
            $balance = $this->calculateBalance($incomesStats, $expensesStats);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'incomes' => $incomesStats,
                    'expenses' => $expensesStats,
                    'creditCards' => $creditCardsStats,
                    'balance' => $balance,
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Erro ao obter estatísticas atualizadas.', ['error' => $e->getMessage(), 'user_id' => Auth::id()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar estatísticas.',
            ], 500);
        }
    }
}
