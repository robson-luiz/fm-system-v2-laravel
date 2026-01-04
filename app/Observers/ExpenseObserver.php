<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\CreditCard;

class ExpenseObserver
{
    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
        $this->updateCreditCardLimit($expense);
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        $this->updateCreditCardLimit($expense);
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        $this->updateCreditCardLimit($expense);
    }

    /**
     * Atualizar limite do cartão de crédito baseado nas despesas
     */
    private function updateCreditCardLimit(Expense $expense): void
    {
        // Se a despesa não tem cartão de crédito, não fazer nada
        if (!$expense->credit_card_id) {
            return;
        }

        $creditCard = CreditCard::find($expense->credit_card_id);
        
        if (!$creditCard) {
            return;
        }

        // Só atualizar automaticamente se a configuração estiver habilitada
        if (!$creditCard->auto_calculate_limit) {
            return;
        }

        // Calcular o total usado baseado em todas as despesas pendentes do cartão
        $totalUsed = $creditCard->expenses()
            ->where('status', 'pending')
            ->sum('amount');

        // Calcular limite disponível
        $availableLimit = max(0, $creditCard->card_limit - $totalUsed);

        // Atualizar o cartão (sem disparar eventos para evitar loop)
        $creditCard->updateQuietly([
            'available_limit' => $availableLimit
        ]);
    }
}