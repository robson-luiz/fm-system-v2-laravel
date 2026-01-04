<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

class Expense extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'credit_card_id',
        'category_id',
        'description',
        'amount',
        'due_date',
        'periodicity',
        'status', // 'pending', 'paid', 'overdue'
        'payment_date',
        'num_installments',
        'reason_not_paid',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'payment_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Relacionamento com usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com cartão de crédito
     */
    public function creditCard()
    {
        return $this->belongsTo(CreditCard::class);
    }

    /**
     * Relacionamento com categoria
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relacionamento com parcelas
     */
    public function installments()
    {
        return $this->hasMany(Installment::class)->orderBy('installment_number');
    }

    /**
     * Scope para filtrar despesas pendentes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope para filtrar despesas pagas
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope para filtrar por período
     */
    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('due_date', [$startDate, $endDate]);
    }

    /**
     * Scope para despesas do mês atual
     */
    public function scopeThisMonth($query)
    {
        return $query->whereYear('due_date', now()->year)
            ->whereMonth('due_date', now()->month);
    }

    /**
     * Scope para despesas vencidas
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
            ->where('due_date', '<', now());
    }

    /**
     * Scope para despesas próximas ao vencimento (7 dias)
     */
    public function scopeDueSoon($query, $days = 7)
    {
        return $query->where('status', 'pending')
            ->whereBetween('due_date', [now(), now()->addDays($days)]);
    }

    /**
     * Accessor para formatar o valor em reais
     */
    public function getAmountFormattedAttribute()
    {
        return 'R$ ' . number_format($this->amount, 2, ',', '.');
    }

    /**
     * Accessor para formatar a data de vencimento
     */
    public function getDueDateFormattedAttribute()
    {
        return $this->due_date?->format('d/m/Y');
    }

    /**
     * Accessor para formatar a data de pagamento
     */
    public function getPaymentDateFormattedAttribute()
    {
        return $this->payment_date?->format('d/m/Y');
    }

    /**
     * Accessor para verificar se está vencida
     */
    public function getIsOverdueAttribute()
    {
    return ($this->status === 'pending' && $this->due_date < now()) || $this->status === 'overdue';
    }

    /**
     * Accessor para verificar se está próxima do vencimento
     */
    public function getIsDueSoonAttribute()
    {
        return $this->status === 'pending' 
            && $this->due_date >= now() 
            && $this->due_date <= now()->addDays(7);
    }

    /**
     * Accessor para traduzir periodicidade
     */
    public function getPeriodicityTranslatedAttribute()
    {
        $translations = [
            'one-time' => 'Única',
            'monthly' => 'Mensal',
            'biweekly' => 'Quinzenal',
            'bimonthly' => 'Bimestral',
            'semiannual' => 'Semestral',
            'yearly' => 'Anual',
        ];

        return $translations[$this->periodicity] ?? $this->periodicity;
    }

    /**
     * Accessor para traduzir status
     */
    public function getStatusTranslatedAttribute()
    {
    if ($this->status === 'paid') return 'Paga';
    if ($this->status === 'overdue') return 'Atrasada';
    return 'Pendente';
    }

    /**
     * Accessor para obter badge HTML do status
     */
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'paid' => 'bg-green-500',
            'pending' => 'bg-yellow-500',
            'overdue' => 'bg-red-500',
        ];
        $text = $this->status_translated;
        $color = $colors[$this->status] ?? 'bg-gray-500';
        return "<span class=\"px-2 py-1 text-xs rounded {$color} text-white\">{$text}</span>";
    }

    /**
     * Verifica se a despesa tem parcelas
     */
    public function hasInstallments()
    {
        return $this->num_installments > 1;
    }

    /**
     * Obtém resumo das parcelas no formato "3x de R$ 100,00"
     */
    public function getInstallmentsSummary()
    {
        if (!$this->hasInstallments()) {
            return null;
        }

        $installmentAmount = $this->amount / $this->num_installments;
        $formatted = 'R$ ' . number_format($installmentAmount, 2, ',', '.');
        
        return "{$this->num_installments}x de {$formatted}";
    }

    /**
     * Accessor para obter resumo das parcelas
     */
    public function getInstallmentsSummaryAttribute()
    {
        return $this->getInstallmentsSummary();
    }

    /**
     * Obtém estatísticas das parcelas
     */
    public function getInstallmentsStats()
    {
        if (!$this->hasInstallments()) {
            return null;
        }

        return [
            'total' => $this->installments->count(),
            'paid' => $this->installments->where('status', 'paid')->count(),
            'pending' => $this->installments->where('status', 'pending')->count(),
            'overdue' => $this->installments->where('status', 'overdue')->count(),
        ];
    }

    /**
     * Marca a despesa e todas as parcelas como pagas
     */
    public function markAllAsPaid($paymentDate = null)
    {
        $paymentDate = $paymentDate ?? now()->toDateString();
        
        return DB::transaction(function () use ($paymentDate) {
            // Atualizar a despesa principal
            $this->update([
                'status' => 'paid',
                'payment_date' => $paymentDate,
                'reason_not_paid' => null,
            ]);

            // Atualizar todas as parcelas
            if ($this->hasInstallments()) {
                $this->installments()->update([
                    'status' => 'paid',
                    'payment_date' => $paymentDate,
                    'reason_not_paid' => null,
                ]);
            }

            return true;
        });
    }

    /**
     * Marca a despesa e todas as parcelas como atrasadas
     */
    public function markAllAsOverdue($reason = null)
    {
        return DB::transaction(function () use ($reason) {
            // Atualizar a despesa principal
            $this->update([
                'status' => 'overdue',
                'payment_date' => null,
                'reason_not_paid' => $reason,
            ]);

            // Atualizar todas as parcelas
            if ($this->hasInstallments()) {
                $this->installments()->update([
                    'status' => 'overdue',
                    'payment_date' => null,
                    'reason_not_paid' => $reason,
                ]);
            }

            return true;
        });
    }

    /**
     * Scope para filtrar despesas atrasadas
     */
    public function scopeOverdueStatus($query)
    {
        return $query->where('status', 'overdue');
    }
}
