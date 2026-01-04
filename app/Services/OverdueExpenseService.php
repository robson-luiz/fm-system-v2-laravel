<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Installment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class OverdueExpenseService
{
    /**
     * Obter todas as contas vencidas (despesas e parcelas) para um usuário
     * 
     * @param int $userId
     * @return array
     */
    public function getOverdueAccounts($userId): array
    {
        $overdueExpenses = $this->getOverdueExpenses($userId);
        $overdueInstallments = $this->getOverdueInstallments($userId);

        return [
            'expenses' => $overdueExpenses,
            'installments' => $overdueInstallments,
            'total_count' => $overdueExpenses->count() + $overdueInstallments->count(),
            'total_amount' => $overdueExpenses->sum('amount') + $overdueInstallments->sum('amount'),
        ];
    }

    /**
     * Obter despesas simples vencidas (sem parcelas)
     * 
     * @param int $userId
     * @return Collection
     */
    private function getOverdueExpenses($userId): Collection
    {
        return Expense::where('user_id', $userId)
            ->where('status', 'pending')
            ->where('due_date', '<', now()->toDateString())
            ->whereNull('num_installments') // Apenas despesas sem parcelas
            ->with('creditCard')
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($expense) {
                return [
                    'id' => $expense->id,
                    'type' => 'expense',
                    'description' => $expense->description,
                    'amount' => $expense->amount,
                    'due_date' => $expense->due_date,
                    'days_overdue' => now()->diffInDays($expense->due_date),
                    'formatted_amount' => 'R$ ' . number_format($expense->amount, 2, ',', '.'),
                    'formatted_date' => $expense->due_date->format('d/m/Y'),
                    'card_name' => $expense->creditCard ? $expense->creditCard->name : null,
                ];
            });
    }

    /**
     * Obter parcelas vencidas
     * 
     * @param int $userId
     * @return Collection
     */
    private function getOverdueInstallments($userId): Collection
    {
        return Installment::whereHas('expense', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('status', 'pending')
            ->where('due_date', '<', now()->toDateString())
            ->with(['expense', 'expense.creditCard'])
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($installment) {
                return [
                    'id' => $installment->id,
                    'type' => 'installment',
                    'description' => $installment->expense->description . ' (' . $installment->installment_number . '/' . $installment->expense->num_installments . ')',
                    'amount' => $installment->amount,
                    'due_date' => $installment->due_date,
                    'days_overdue' => now()->diffInDays($installment->due_date),
                    'formatted_amount' => 'R$ ' . number_format($installment->amount, 2, ',', '.'),
                    'formatted_date' => $installment->due_date->format('d/m/Y'),
                    'card_name' => $installment->expense->creditCard ? $installment->expense->creditCard->name : null,
                    'expense_id' => $installment->expense_id,
                    'installment_number' => $installment->installment_number,
                ];
            });
    }

    /**
     * Marcar despesas como pagas em lote
     * 
     * @param array $expenseIds
     * @return int Quantidade de registros atualizados
     */
    public function markExpensesAsPaid(array $expenseIds): int
    {
        if (empty($expenseIds)) {
            return 0;
        }

        return Expense::whereIn('id', $expenseIds)
            ->where('status', 'pending')
            ->update([
                'status' => 'paid',
                'payment_date' => now()->toDateString(),
                'updated_at' => now(),
            ]);
    }

    /**
     * Marcar parcelas como pagas em lote
     * 
     * @param array $installmentIds
     * @return int Quantidade de registros atualizados
     */
    public function markInstallmentsAsPaid(array $installmentIds): int
    {
        if (empty($installmentIds)) {
            return 0;
        }

        return Installment::whereIn('id', $installmentIds)
            ->where('status', 'pending')
            ->update([
                'status' => 'paid',
                'payment_date' => now()->toDateString(),
                'updated_at' => now(),
            ]);
    }

    /**
     * Verificar se o usuário deve ver o modal de verificação
     * (Deve ter pelo menos uma conta vencida há mais de 1 dia)
     * 
     * @param int $userId
     * @return bool
     */
    public function shouldShowVerificationModal($userId): bool
    {
        $oneDayAgo = now()->subDay()->toDateString();

        // Verifica despesas vencidas há mais de 1 dia
        $hasOverdueExpenses = Expense::where('user_id', $userId)
            ->where('status', 'pending')
            ->where('due_date', '<', $oneDayAgo)
            ->whereNull('num_installments')
            ->exists();

        if ($hasOverdueExpenses) {
            return true;
        }

        // Verifica parcelas vencidas há mais de 1 dia
        $hasOverdueInstallments = Installment::whereHas('expense', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('status', 'pending')
            ->where('due_date', '<', $oneDayAgo)
            ->exists();

        return $hasOverdueInstallments;
    }
}
