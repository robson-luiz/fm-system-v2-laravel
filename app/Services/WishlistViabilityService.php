<?php

namespace App\Services;

use App\Models\Wishlist;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Installment;
use Carbon\Carbon;

class WishlistViabilityService
{
    /**
     * Analisa a viabilidade financeira de um item da wishlist
     *
     * @param Wishlist $wishlist
     * @param int $userId
     * @return array
     */
    public function analyzeViability(Wishlist $wishlist, $userId)
    {
        // Calcula saldo médio mensal
        $monthlyBalance = $this->calculateMonthlyBalance($userId);
        
        // Calcula valor restante
        $remainingAmount = $wishlist->remaining_amount;
        
        // Calcula meses necessários
        $monthsNeeded = $this->calculateMonthsNeeded($remainingAmount, $monthlyBalance);
        
        // Calcula data estimada de conclusão
        $estimatedCompletionDate = $this->calculateEstimatedCompletionDate($monthsNeeded);
        
        // Calcula valor mensal necessário
        $monthlyAmountNeeded = $this->calculateMonthlyAmountNeeded($wishlist, $monthlyBalance);
        
        // Avalia viabilidade
        $viabilityStatus = $this->evaluateViability($monthlyBalance, $monthlyAmountNeeded, $wishlist);
        
        // Gera recomendações
        $recommendations = $this->generateRecommendations($viabilityStatus, $monthlyBalance, $monthlyAmountNeeded, $wishlist);
        
        return [
            'remaining_amount' => (float) $remainingAmount,
            'monthly_balance' => (float) $monthlyBalance,
            'months_needed' => $monthsNeeded,
            'estimated_completion_date' => $estimatedCompletionDate,
            'monthly_amount_needed' => (float) $monthlyAmountNeeded,
            'viability_status' => $viabilityStatus,
            'viability_percentage' => $this->calculateViabilityPercentage($viabilityStatus),
            'recommendations' => $recommendations,
            'impact_analysis' => $this->analyzeImpact($monthlyBalance, $monthlyAmountNeeded),
        ];
    }
    
    /**
     * Calcula saldo médio mensal dos últimos 6 meses
     *
     * @param int $userId
     * @return float
     */
    private function calculateMonthlyBalance($userId)
    {
        $months = 6;
        $totalBalance = 0;
        
        for ($i = 0; $i < $months; $i++) {
            $currentMonth = Carbon::now()->subMonths($i);
            
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
            
            $monthlyBalance = $incomes - ($simpleExpenses + $installments);
            $totalBalance += $monthlyBalance;
        }
        
        return $totalBalance / $months;
    }
    
    /**
     * Calcula quantos meses serão necessários
     *
     * @param float $remainingAmount
     * @param float $monthlyBalance
     * @return int
     */
    private function calculateMonthsNeeded($remainingAmount, $monthlyBalance)
    {
        if ($monthlyBalance <= 0) {
            return PHP_INT_MAX; // Impossível alcançar
        }
        
        // Considera economizar 30% do saldo mensal
        $monthlySavings = $monthlyBalance * 0.30;
        
        if ($monthlySavings <= 0) {
            return PHP_INT_MAX;
        }
        
        return (int) ceil($remainingAmount / $monthlySavings);
    }
    
    /**
     * Calcula data estimada de conclusão
     *
     * @param int $monthsNeeded
     * @return string|null
     */
    private function calculateEstimatedCompletionDate($monthsNeeded)
    {
        if ($monthsNeeded >= PHP_INT_MAX) {
            return null;
        }
        
        return Carbon::now()->addMonths($monthsNeeded)->format('Y-m-d');
    }
    
    /**
     * Calcula valor mensal necessário
     *
     * @param Wishlist $wishlist
     * @param float $monthlyBalance
     * @return float
     */
    private function calculateMonthlyAmountNeeded(Wishlist $wishlist, $monthlyBalance)
    {
        if (!$wishlist->target_date) {
            // Se não tem data alvo, usar 30% do saldo
            return $monthlyBalance * 0.30;
        }
        
        $now = Carbon::now();
        $targetDate = Carbon::parse($wishlist->target_date);
        
        if ($targetDate->isPast()) {
            return 0;
        }
        
        $monthsUntilTarget = $now->diffInMonths($targetDate);
        
        if ($monthsUntilTarget <= 0) {
            return $wishlist->remaining_amount;
        }
        
        return $wishlist->remaining_amount / $monthsUntilTarget;
    }
    
    /**
     * Avalia status de viabilidade
     *
     * @param float $monthlyBalance
     * @param float $monthlyAmountNeeded
     * @param Wishlist $wishlist
     * @return string
     */
    private function evaluateViability($monthlyBalance, $monthlyAmountNeeded, $wishlist)
    {
        if ($monthlyBalance <= 0) {
            return 'inviavel'; // Sem saldo positivo
        }
        
        $percentageNeeded = ($monthlyAmountNeeded / $monthlyBalance) * 100;
        
        if ($percentageNeeded <= 20) {
            return 'muito_viavel'; // Precisa de até 20% do saldo
        } elseif ($percentageNeeded <= 40) {
            return 'viavel'; // Precisa de até 40% do saldo
        } elseif ($percentageNeeded <= 60) {
            return 'moderado'; // Precisa de até 60% do saldo
        } elseif ($percentageNeeded <= 80) {
            return 'dificil'; // Precisa de até 80% do saldo
        } else {
            return 'inviavel'; // Precisa de mais de 80% do saldo
        }
    }
    
    /**
     * Calcula porcentagem de viabilidade
     *
     * @param string $status
     * @return int
     */
    private function calculateViabilityPercentage($status)
    {
        return match($status) {
            'muito_viavel' => 95,
            'viavel' => 75,
            'moderado' => 50,
            'dificil' => 30,
            'inviavel' => 10,
            default => 0,
        };
    }
    
    /**
     * Gera recomendações baseadas na análise
     *
     * @param string $viabilityStatus
     * @param float $monthlyBalance
     * @param float $monthlyAmountNeeded
     * @param Wishlist $wishlist
     * @return array
     */
    private function generateRecommendations($viabilityStatus, $monthlyBalance, $monthlyAmountNeeded, $wishlist)
    {
        $recommendations = [];
        
        switch ($viabilityStatus) {
            case 'muito_viavel':
                $recommendations[] = [
                    'type' => 'success',
                    'message' => 'Excelente! Este objetivo é muito viável com seu orçamento atual.',
                    'icon' => '✅'
                ];
                $recommendations[] = [
                    'type' => 'info',
                    'message' => 'Considere aumentar o valor mensal para alcançar seu objetivo mais rapidamente.',
                    'icon' => '💡'
                ];
                break;
                
            case 'viavel':
                $recommendations[] = [
                    'type' => 'success',
                    'message' => 'Este objetivo é viável com seu orçamento atual.',
                    'icon' => '✅'
                ];
                $recommendations[] = [
                    'type' => 'info',
                    'message' => sprintf('Reserve %.1f%% do seu saldo mensal para este objetivo.', ($monthlyAmountNeeded / $monthlyBalance) * 100),
                    'icon' => '💰'
                ];
                break;
                
            case 'moderado':
                $recommendations[] = [
                    'type' => 'warning',
                    'message' => 'Este objetivo requer atenção ao orçamento.',
                    'icon' => '⚠️'
                ];
                $recommendations[] = [
                    'type' => 'info',
                    'message' => 'Considere reduzir despesas não essenciais para facilitar.',
                    'icon' => '💡'
                ];
                $recommendations[] = [
                    'type' => 'info',
                    'message' => 'Ou estenda a data alvo para tornar mais confortável.',
                    'icon' => '📅'
                ];
                break;
                
            case 'dificil':
                $recommendations[] = [
                    'type' => 'warning',
                    'message' => 'Este objetivo será difícil de alcançar com o orçamento atual.',
                    'icon' => '⚠️'
                ];
                $recommendations[] = [
                    'type' => 'info',
                    'message' => 'Sugestões: aumentar receitas, reduzir despesas ou estender prazo.',
                    'icon' => '💡'
                ];
                if ($wishlist->target_date) {
                    $newDate = Carbon::parse($wishlist->target_date)->addMonths(6);
                    $recommendations[] = [
                        'type' => 'info',
                        'message' => sprintf('Considere adiar a data alvo para %s.', $newDate->format('d/m/Y')),
                        'icon' => '📅'
                    ];
                }
                break;
                
            case 'inviavel':
                $recommendations[] = [
                    'type' => 'danger',
                    'message' => 'Este objetivo não é viável com o orçamento atual.',
                    'icon' => '❌'
                ];
                $recommendations[] = [
                    'type' => 'info',
                    'message' => 'Reavalie suas finanças ou reduza o valor do objetivo.',
                    'icon' => '💡'
                ];
                if ($monthlyBalance > 0) {
                    $viableAmount = $monthlyBalance * 0.60 * 12; // 60% do saldo por 12 meses
                    $recommendations[] = [
                        'type' => 'info',
                        'message' => sprintf('Um objetivo de até R$ %.2f seria mais viável em 12 meses.', $viableAmount),
                        'icon' => '💰'
                    ];
                } else {
                    $recommendations[] = [
                        'type' => 'danger',
                        'message' => 'Suas despesas estão maiores que suas receitas. Ajuste seu orçamento primeiro.',
                        'icon' => '⚠️'
                    ];
                }
                break;
        }
        
        return $recommendations;
    }
    
    /**
     * Analisa impacto no orçamento
     *
     * @param float $monthlyBalance
     * @param float $monthlyAmountNeeded
     * @return array
     */
    private function analyzeImpact($monthlyBalance, $monthlyAmountNeeded)
    {
        if ($monthlyBalance <= 0) {
            return [
                'percentage' => 0,
                'remaining_after_saving' => 0,
                'impact_level' => 'critico',
                'message' => 'Orçamento comprometido. Ajustes necessários.',
            ];
        }
        
        $percentage = ($monthlyAmountNeeded / $monthlyBalance) * 100;
        $remainingAfterSaving = $monthlyBalance - $monthlyAmountNeeded;
        
        if ($percentage <= 20) {
            $impactLevel = 'baixo';
            $message = 'Baixo impacto no orçamento mensal.';
        } elseif ($percentage <= 40) {
            $impactLevel = 'moderado';
            $message = 'Impacto moderado. Mantenha controle das despesas.';
        } elseif ($percentage <= 60) {
            $impactLevel = 'alto';
            $message = 'Alto impacto. Controle rigoroso necessário.';
        } else {
            $impactLevel = 'critico';
            $message = 'Impacto crítico. Considere ajustes.';
        }
        
        return [
            'percentage' => round($percentage, 2),
            'remaining_after_saving' => (float) $remainingAfterSaving,
            'impact_level' => $impactLevel,
            'message' => $message,
        ];
    }
}
