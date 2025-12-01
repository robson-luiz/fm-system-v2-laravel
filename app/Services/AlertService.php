<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Income;
use App\Models\CreditCard;
use App\Models\Installment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AlertService
{
    /**
     * Obter todos os alertas para um usuário
     */
    public function getAllAlerts($userId): array
    {
        return [
            'financial_health' => $this->getFinancialHealthAlerts($userId),
            'overdue_expenses' => $this->getOverdueExpenses($userId),
            'due_soon_expenses' => $this->getDueSoonExpenses($userId),
            'overdue_incomes' => $this->getOverdueIncomes($userId),
            'credit_card_alerts' => $this->getCreditCardAlerts($userId),
            'budget_alerts' => $this->getBudgetAlerts($userId),
            'upcoming_opportunities' => $this->getUpcomingOpportunities($userId),
        ];
    }

    /**
     * Alertas de saúde financeira
     */
    private function getFinancialHealthAlerts($userId): array
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        // Receitas vs Despesas do mês
        $monthlyIncomes = Income::forUser($userId)
            ->whereMonth('received_date', $currentMonth)
            ->whereYear('received_date', $currentYear)
            ->where('status', 'recebida')
            ->sum('amount');
            
        $monthlyExpenses = Expense::where('user_id', $userId)
            ->whereMonth('due_date', $currentMonth)
            ->whereYear('due_date', $currentYear)
            ->where('status', 'paid')
            ->sum('amount');

        $alerts = [];

        // Alerta de déficit
        if ($monthlyExpenses > $monthlyIncomes) {
            $deficit = $monthlyExpenses - $monthlyIncomes;
            $alerts[] = [
                'type' => 'danger',
                'title' => 'Déficit Detectado',
                'message' => "Suas despesas excedem suas receitas em R$ " . number_format($deficit, 2, ',', '.') . " este mês.",
                'action' => 'Revise seus gastos ou aumente sua renda.',
                'priority' => 'high'
            ];
        }

        // Alerta de economia excessiva (mais de 50% não gasto)
        if ($monthlyIncomes > 0 && ($monthlyIncomes - $monthlyExpenses) / $monthlyIncomes > 0.5) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Oportunidade de Investimento',
                'message' => 'Você tem uma alta taxa de economia este mês. Considere investir parte dos recursos.',
                'action' => 'Explore opções de investimento.',
                'priority' => 'low'
            ];
        }

        return $alerts;
    }

    /**
     * Despesas vencidas
     */
    private function getOverdueExpenses($userId): Collection
    {
        return Expense::where('user_id', $userId)
            ->where('status', 'pending')
            ->where('due_date', '<', now()->toDateString())
            ->with(['creditCard', 'installments'])
            ->orderBy('due_date', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($expense) {
                $daysOverdue = now()->diffInDays($expense->due_date);
                return [
                    'expense' => $expense,
                    'days_overdue' => $daysOverdue,
                    'priority' => $daysOverdue > 30 ? 'high' : ($daysOverdue > 7 ? 'medium' : 'low'),
                    'suggested_action' => $this->getSuggestedActionForOverdueExpense($expense, $daysOverdue)
                ];
            });
    }

    /**
     * Despesas vencendo em breve
     */
    private function getDueSoonExpenses($userId): Collection
    {
        return Expense::where('user_id', $userId)
            ->where('status', 'pending')
            ->whereBetween('due_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->with(['creditCard', 'installments'])
            ->orderBy('due_date', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($expense) {
                $daysUntilDue = now()->diffInDays($expense->due_date, false);
                return [
                    'expense' => $expense,
                    'days_until_due' => $daysUntilDue,
                    'priority' => $daysUntilDue <= 2 ? 'high' : 'medium',
                    'suggested_action' => $this->getSuggestedActionForDueSoonExpense($expense, $daysUntilDue)
                ];
            });
    }

    /**
     * Receitas em atraso
     */
    private function getOverdueIncomes($userId): Collection
    {
        return Income::forUser($userId)
            ->where('status', 'pendente')
            ->where('received_date', '<', now()->toDateString())
            ->orderBy('received_date', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($income) {
                $daysOverdue = now()->diffInDays($income->received_date);
                return [
                    'income' => $income,
                    'days_overdue' => $daysOverdue,
                    'priority' => $daysOverdue > 15 ? 'high' : 'medium',
                    'suggested_action' => $this->getSuggestedActionForOverdueIncome($income, $daysOverdue)
                ];
            });
    }

    /**
     * Alertas de cartão de crédito
     */
    private function getCreditCardAlerts($userId): array
    {
        $alerts = [];
        $cards = CreditCard::where('user_id', $userId)->active()->get();

        foreach ($cards as $card) {
            // Cartão próximo ao limite (80%+)
            if ($card->limit_usage_percentage > 80) {
                $alerts[] = [
                    'type' => $card->limit_usage_percentage > 95 ? 'danger' : 'warning',
                    'title' => 'Cartão Próximo ao Limite',
                    'message' => "{$card->name} está com {$card->limit_usage_percentage}% do limite utilizado.",
                    'card' => $card,
                    'priority' => $card->limit_usage_percentage > 95 ? 'high' : 'medium',
                    'suggested_action' => $this->getSuggestedActionForCreditCard($card)
                ];
            }

            // Verificar se está próximo do melhor dia para compra
            $today = now()->day;
            $bestDay = $card->best_purchase_day_calculated;
            
            if ($today >= $bestDay - 2 && $today <= $bestDay + 2) {
                $alerts[] = [
                    'type' => 'info',
                    'title' => 'Melhor Período para Compras',
                    'message' => "Este é o período ideal para compras no {$card->name}.",
                    'card' => $card,
                    'priority' => 'low',
                    'suggested_action' => 'Aproveite para fazer compras com melhor prazo de pagamento.'
                ];
            }
        }

        return $alerts;
    }

    /**
     * Alertas de orçamento
     */
    private function getBudgetAlerts($userId): array
    {
        $alerts = [];
        
        // Verificar gastos por categoria nos últimos 30 dias
        $recentExpenses = Expense::where('user_id', $userId)
            ->where('due_date', '>=', now()->subDays(30))
            ->sum('amount');

        $monthlyAverage = $recentExpenses; // Simplificado para este exemplo
        
        // Gastos atuais do mês
        $currentMonthExpenses = Expense::where('user_id', $userId)
            ->whereMonth('due_date', now()->month)
            ->whereYear('due_date', now()->year)
            ->sum('amount');

        if ($currentMonthExpenses > $monthlyAverage * 1.2) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Gastos Acima da Média',
                'message' => 'Seus gastos este mês estão 20% acima da média mensal.',
                'priority' => 'medium',
                'suggested_action' => 'Revise seus gastos recentes e identifique possíveis cortes.'
            ];
        }

        return $alerts;
    }

    /**
     * Oportunidades futuras
     */
    private function getUpcomingOpportunities($userId): array
    {
        $opportunities = [];

        // Receitas futuras grandes
        $upcomingLargeIncomes = Income::forUser($userId)
            ->where('status', 'pendente')
            ->where('received_date', '>', now())
            ->where('amount', '>', 1000)
            ->orderBy('received_date', 'asc')
            ->limit(3)
            ->get();

        foreach ($upcomingLargeIncomes as $income) {
            $opportunities[] = [
                'type' => 'success',
                'title' => 'Receita Importante Chegando',
                'message' => "{$income->description} de {$income->formatted_amount} prevista para {$income->received_date->format('d/m/Y')}.",
                'priority' => 'medium',
                'suggested_action' => 'Planeje como investir ou usar esse valor de forma inteligente.'
            ];
        }

        return $opportunities;
    }

    /**
     * Sugestões para despesas vencidas
     */
    private function getSuggestedActionForOverdueExpense($expense, $daysOverdue): string
    {
        if ($daysOverdue > 30) {
            return 'Prioridade máxima: contate o credor para negociar parcelamento.';
        } elseif ($daysOverdue > 7) {
            return 'Pague imediatamente para evitar juros maiores.';
        } else {
            return 'Quite o quanto antes para manter seu nome limpo.';
        }
    }

    /**
     * Sugestões para despesas vencendo
     */
    private function getSuggestedActionForDueSoonExpense($expense, $daysUntilDue): string
    {
        if ($daysUntilDue <= 1) {
            return 'Pague hoje para evitar atraso.';
        } elseif ($daysUntilDue <= 3) {
            return 'Prepare o pagamento, vence em breve.';
        } else {
            return 'Agende o pagamento para não esquecer.';
        }
    }

    /**
     * Sugestões para receitas atrasadas
     */
    private function getSuggestedActionForOverdueIncome($income, $daysOverdue): string
    {
        if ($income->type === 'fixa') {
            return 'Receita fixa atrasada: verifique com o pagador.';
        } else {
            return 'Faça o follow-up do pagamento.';
        }
    }

    /**
     * Sugestões para cartão de crédito
     */
    private function getSuggestedActionForCreditCard($card): string
    {
        if ($card->limit_usage_percentage > 95) {
            return 'Urgente: pare de usar este cartão até quitar a fatura.';
        } elseif ($card->limit_usage_percentage > 85) {
            return 'Cuidado: use apenas para emergências.';
        } else {
            return 'Monitore os gastos e considere pagar antecipadamente.';
        }
    }

    /**
     * Contar alertas por prioridade
     */
    public function getAlertCountsByPriority($userId): array
    {
        $allAlerts = $this->getAllAlerts($userId);
        $counts = ['high' => 0, 'medium' => 0, 'low' => 0];

        // Flatten e contar alertas
        foreach ($allAlerts as $category => $alerts) {
            if (is_array($alerts)) {
                foreach ($alerts as $alert) {
                    if (isset($alert['priority'])) {
                        $counts[$alert['priority']]++;
                    }
                }
            }
        }

        return $counts;
    }

    /**
     * Obter resumo de alertas importantes
     */
    public function getImportantAlertsSummary($userId): array
    {
        $allAlerts = $this->getAllAlerts($userId);
        $important = [];

        // Coletar apenas alertas de alta prioridade
        foreach ($allAlerts as $category => $alerts) {
            if (is_array($alerts)) {
                foreach ($alerts as $alert) {
                    if (isset($alert['priority']) && $alert['priority'] === 'high') {
                        $important[] = $alert;
                    }
                }
            }
        }

        return array_slice($important, 0, 5); // Máximo 5 alertas importantes
    }
}