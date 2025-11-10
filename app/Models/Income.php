<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Income extends Model implements Auditable
{
    use AuditableTrait;

    protected $fillable = [
        'user_id',
        'description',
        'amount',
        'received_date',
        'category',
        'type',
        'status',
        'source',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'received_date' => 'date',
        'type' => 'string',
        'status' => 'string'
    ];

    protected $dates = [
        'received_date'
    ];

    /**
     * Relacionamento com User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para receitas do usuário autenticado
     */
    public function scopeForUser($query, $userId = null)
    {
        if (!$userId && Auth::check()) {
            $userId = Auth::id();
        }
        
        return $query->when($userId, function($q) use ($userId) {
            return $q->where('user_id', $userId);
        });
    }

    /**
     * Scope para receitas por status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para receitas por categoria
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope para receitas por tipo
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope para receitas do mês
     */
    public function scopeCurrentMonth($query)
    {
        return $query->whereMonth('received_date', now()->month)
                    ->whereYear('received_date', now()->year);
    }

    /**
     * Scope para receitas por período
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('received_date', [$startDate, $endDate]);
    }

    /**
     * Accessor para formatar valor monetário
     */
    public function getFormattedAmountAttribute()
    {
        return 'R$ ' . number_format($this->amount, 2, ',', '.');
    }

    /**
     * Accessor para badge de status
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'recebida' => 'success',
            'pendente' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Accessor para badge de tipo
     */
    public function getTypeBadgeAttribute()
    {
        return match($this->type) {
            'fixa' => 'primary',
            'variavel' => 'info',
            default => 'secondary'
        };
    }

    /**
     * Método para marcar como recebida
     */
    public function markAsReceived()
    {
        $this->update(['status' => 'recebida']);
    }

    /**
     * Método para marcar como pendente
     */
    public function markAsPending()
    {
        $this->update(['status' => 'pendente']);
    }

    /**
     * Verificar se a receita está atrasada (pendente e data já passou)
     */
    public function getIsOverdueAttribute()
    {
        return $this->status === 'pendente' && $this->received_date < now()->toDateString();
    }

    /**
     * Dias até o recebimento
     */
    public function getDaysToReceiveAttribute()
    {
        if ($this->status === 'recebida') {
            return 0;
        }
        
        return now()->diffInDays($this->received_date, false);
    }

    /**
     * Categorias padrão do sistema
     */
    public static function getDefaultCategories()
    {
        return [
            'Salário',
            'Freelance',
            'Investimentos',
            'Vendas',
            'Aluguéis',
            'Dividendos',
            'Bonificações',
            'Comissões',
            'Outros'
        ];
    }
}
