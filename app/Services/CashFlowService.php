<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Installment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CashFlowService
{
    /**
     * Calcula o fluxo de caixa mensal
     *
     * @param int $userId
     * @param int $months Número de meses para análise (padrão: 12)
     * @return array
     */
    public function getMonthlyFlow($userId, $months = 12)
    {
        $data = [];
        $startDate = Carbon::now()->subMonths($months)->startOfMonth();
        
        for ($i = 0; $i < $months; $i++) {
            $currentMonth = Carbon::now()->subMonths($months - $i - 1);
            $monthKey = $currentMonth->format('Y-m');
            
            // Receitas do mês
            $incomes = Income::where('user_id', $userId)
                ->whereYear('received_date', $currentMonth->year)
                ->whereMonth('received_date', $currentMonth->month)
                ->where('status', 'recebida')
                ->sum('amount');
            
            // Despesas simples do mês
            $simpleExpenses = Expense::where('user_id', $userId)
                ->whereNull('num_installments')
                ->whereYear('due_date', $currentMonth->year)
                ->whereMonth('due_date', $currentMonth->month)
                ->where('status', 'paga')
                ->sum('amount');
            
            // Parcelas do mês
            $installments = Installment::whereHas('expense', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
                ->whereYear('due_date', $currentMonth->year)
                ->whereMonth('due_date', $currentMonth->month)
                ->where('status', 'paga')
                ->sum('amount');
            
            $totalExpenses = $simpleExpenses + $installments;
            $balance = $incomes - $totalExpenses;
            
            $data[] = [
                'month' => $currentMonth->format('M/Y'),
                'month_full' => $currentMonth->translatedFormat('F \d\e Y'),
                'incomes' => (float) $incomes,
                'expenses' => (float) $totalExpenses,
                'balance' => (float) $balance,
                'date' => $currentMonth->format('Y-m-d')
            ];
        }
        
        return $data;
    }
    
    /**
     * Calcula projeções futuras baseadas em histórico
     *
     * @param int $userId
     * @param int $futureMonths Meses futuros a projetar (padrão: 6)
     * @return array
     */
    public function getProjections($userId, $futureMonths = 6)
    {
        // Calcula médias dos últimos 6 meses
        $historicalData = $this->getMonthlyFlow($userId, 6);
        
        $avgIncome = collect($historicalData)->avg('incomes');
        $avgExpense = collect($historicalData)->avg('expenses');
        
        // Busca receitas fixas (salários, aluguéis) para projeção mais precisa
        $fixedIncomes = Income::where('user_id', $userId)
            ->where('type', 'fixa')
            ->where('status', 'recebida')
            ->avg('amount');
        
        // Se existem receitas fixas, usar essa média para projeção de receitas
        $projectedIncome = $fixedIncomes > 0 ? $fixedIncomes : $avgIncome;
        
        $projections = [];
        
        for ($i = 1; $i <= $futureMonths; $i++) {
            $futureMonth = Carbon::now()->addMonths($i);
            
            // Adiciona variação de 5% para simular oscilações
            $incomeVariation = rand(-5, 5) / 100;
            $expenseVariation = rand(-5, 5) / 100;
            
            $projectedIncomeValue = $projectedIncome * (1 + $incomeVariation);
            $projectedExpenseValue = $avgExpense * (1 + $expenseVariation);
            $projectedBalance = $projectedIncomeValue - $projectedExpenseValue;
            
            $projections[] = [
                'month' => $futureMonth->format('M/Y'),
                'month_full' => $futureMonth->translatedFormat('F \d\e Y'),
                'projected_income' => (float) $projectedIncomeValue,
                'projected_expense' => (float) $projectedExpenseValue,
                'projected_balance' => (float) $projectedBalance,
                'date' => $futureMonth->format('Y-m-d')
            ];
        }
        
        return $projections;
    }
    
    /**
     * Calcula resumo anual do fluxo de caixa
     *
     * @param int $userId
     * @param int $year
     * @return array
     */
    public function getYearlyFlow($userId, $year = null)
    {
        $year = $year ?? Carbon::now()->year;
        
        // Receitas anuais
        $totalIncome = Income::where('user_id', $userId)
            ->whereYear('received_date', $year)
            ->where('status', 'recebida')
            ->sum('amount');
        
        // Despesas simples anuais
        $simpleExpenses = Expense::where('user_id', $userId)
            ->whereNull('num_installments')
            ->whereYear('due_date', $year)
            ->where('status', 'paga')
            ->sum('amount');
        
        // Parcelas anuais
        $installments = Installment::whereHas('expense', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->whereYear('due_date', $year)
            ->where('status', 'paga')
            ->sum('amount');
        
        $totalExpense = $simpleExpenses + $installments;
        $balance = $totalIncome - $totalExpense;
        
        return [
            'year' => $year,
            'total_income' => (float) $totalIncome,
            'total_expense' => (float) $totalExpense,
            'balance' => (float) $balance,
            'avg_monthly_income' => (float) ($totalIncome / 12),
            'avg_monthly_expense' => (float) ($totalExpense / 12),
        ];
    }
    
    /**
     * Analisa tendências de crescimento/queda
     *
     * @param int $userId
     * @return array
     */
    public function getTrends($userId)
    {
        $lastSixMonths = $this->getMonthlyFlow($userId, 6);
        
        if (count($lastSixMonths) < 2) {
            return [
                'income_trend' => 'stable',
                'expense_trend' => 'stable',
                'balance_trend' => 'stable',
            ];
        }
        
        // Calcula tendência comparando primeira e última metade do período
        $firstHalf = array_slice($lastSixMonths, 0, 3);
        $secondHalf = array_slice($lastSixMonths, 3, 3);
        
        $avgIncomeFirst = collect($firstHalf)->avg('incomes');
        $avgIncomeSecond = collect($secondHalf)->avg('incomes');
        
        $avgExpenseFirst = collect($firstHalf)->avg('expenses');
        $avgExpenseSecond = collect($secondHalf)->avg('expenses');
        
        // Define tendências (variação > 10% = crescimento/queda, senão estável)
        $incomeTrend = 'stable';
        if ($avgIncomeSecond > $avgIncomeFirst * 1.1) {
            $incomeTrend = 'growing';
        } elseif ($avgIncomeSecond < $avgIncomeFirst * 0.9) {
            $incomeTrend = 'falling';
        }
        
        $expenseTrend = 'stable';
        if ($avgExpenseSecond > $avgExpenseFirst * 1.1) {
            $expenseTrend = 'growing';
        } elseif ($avgExpenseSecond < $avgExpenseFirst * 0.9) {
            $expenseTrend = 'falling';
        }
        
        // Tendência de saldo
        $avgBalanceFirst = collect($firstHalf)->avg('balance');
        $avgBalanceSecond = collect($secondHalf)->avg('balance');
        
        $balanceTrend = 'stable';
        if ($avgBalanceSecond > $avgBalanceFirst * 1.1) {
            $balanceTrend = 'improving';
        } elseif ($avgBalanceSecond < $avgBalanceFirst * 0.9) {
            $balanceTrend = 'declining';
        }
        
        return [
            'income_trend' => $incomeTrend,
            'income_change' => (float) (($avgIncomeSecond - $avgIncomeFirst) / ($avgIncomeFirst ?: 1) * 100),
            'expense_trend' => $expenseTrend,
            'expense_change' => (float) (($avgExpenseSecond - $avgExpenseFirst) / ($avgExpenseFirst ?: 1) * 100),
            'balance_trend' => $balanceTrend,
            'balance_change' => (float) (($avgBalanceSecond - $avgBalanceFirst) / (abs($avgBalanceFirst) ?: 1) * 100),
        ];
    }
    
    /**
     * Retorna dados completos para visualização
     *
     * @param int $userId
     * @param int $months Número de meses para análise histórica
     * @return array
     */
    public function getCompleteAnalysis($userId, $months = 12)
    {
        return [
            'monthly_flow' => $this->getMonthlyFlow($userId, $months),
            'projections' => $this->getProjections($userId, 6),
            'yearly_flow' => $this->getYearlyFlow($userId),
            'trends' => $this->getTrends($userId),
        ];
    }
}
